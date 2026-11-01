<?php

namespace App\Http\Controllers\Manage\Admin;

use App\Helpers\Pagination;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Competition;
use App\Models\Organisation\Organisation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdminUsersController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $users = User::query()
            ->when($q !== '', fn ($query) => $query->where(fn ($where) => $where
                ->where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")))
            ->withCount('officiating')
            // organisation members can access every competition the organisation runs
            ->addSelect(['competitions_count' => DB::table('competitions')
                ->selectRaw('count(*)')
                ->where(fn ($where) => $where
                    ->whereIn('competitions.id', DB::table('user_competition_accesses')
                        ->select('competition')
                        ->whereColumn('user_competition_accesses.user', 'users.id')
                        ->where('access_to', '!=', 'none'))
                    ->orWhereIn('competitions.organisation', DB::table('organisation_user_accesses')
                        ->select('organisation')
                        ->whereColumn('organisation_user_accesses.user', 'users.id')
                        ->where('access_to', '!=', 'none')))])
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Admin/Users', [
            'q' => $q,
            'users' => collect($users->items())->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'officialOnly' => $user->isJudgeOnly(),
                'admin' => (bool) $user->isAdmin(),
                'competitions' => (int) $user->competitions_count,
                'officiating' => (int) $user->officiating_count,
                'joined' => $user->created_at?->toDateString(),
                'url' => route('manage.admin.users.show', $user),
            ]),
            'pagination' => Pagination::props($users),
        ]);
    }

    public function show(User $user)
    {
        $accessNames = Competition::$accessTypes + ['owner' => 'Owner'];
        $orgAccessNames = Organisation::$accessTypes['Organisation'] + Organisation::$accessTypes['Competition'] + ['owner' => 'Owner'];

        $direct = DB::table('user_competition_accesses')
            ->join('competitions', 'competitions.id', '=', 'user_competition_accesses.competition')
            ->where('user_competition_accesses.user', $user->id)
            ->where('access_to', '!=', 'none')
            ->get(['competitions.id', 'competitions.name', 'competitions.when', 'access_to']);

        // organisation members can access every competition the organisation runs
        $viaOrganisation = DB::table('organisation_user_accesses')
            ->join('organisations', 'organisations.id', '=', 'organisation_user_accesses.organisation')
            ->join('competitions', 'competitions.organisation', '=', 'organisations.id')
            ->where('organisation_user_accesses.user', $user->id)
            ->where('access_to', '!=', 'none')
            ->get(['competitions.id', 'competitions.name', 'competitions.when', 'organisations.name as organisation', 'access_to']);

        $competitions = $direct->concat($viaOrganisation)
            ->groupBy('id')
            ->map(fn ($rows) => [
                'id' => $rows->first()->id,
                'name' => $rows->first()->name,
                'when' => $rows->first()->when ? Carbon::parse($rows->first()->when)->toDateString() : null,
                'url' => route('manage.admin.competitions.show', $rows->first()->id),
                'access' => $rows->whereNull('organisation')->pluck('access_to')
                    ->map(fn ($access) => $accessNames[$access] ?? $access)->values()->all(),
                'via' => $rows->whereNotNull('organisation')->groupBy('organisation')
                    ->map(fn ($orgRows, $org) => "Via {$org} (" . $orgRows->pluck('access_to')
                        ->map(fn ($access) => $orgAccessNames[$access] ?? $access)->implode(', ') . ')')
                    ->values()->all(),
            ])
            ->sortByDesc('when')
            ->values();

        $organisations = DB::table('organisation_user_accesses')
            ->join('organisations', 'organisations.id', '=', 'organisation_user_accesses.organisation')
            ->where('organisation_user_accesses.user', $user->id)
            ->where('access_to', '!=', 'none')
            ->orderBy('organisations.name')
            ->get(['organisations.id', 'organisations.name', 'access_to'])
            ->groupBy('id')
            ->map(fn ($rows) => [
                'id' => $rows->first()->id,
                'name' => $rows->first()->name,
                'url' => route('manage.organisations.show', $rows->first()->id),
                'access' => $rows->pluck('access_to')->map(fn ($access) => $orgAccessNames[$access] ?? $access)->all(),
            ])
            ->values();

        $officiating = $user->officiating()
            ->withPivot('role')
            ->withTimestamps()
            ->orderByDesc('when')
            ->get()
            ->map(fn (Competition $comp) => [
                'id' => $comp->id,
                'name' => $comp->name,
                'when' => $comp->when?->toDateString(),
                'url' => route('manage.admin.competitions.show', $comp),
                'role' => $comp->pivot->role,
                'roleName' => Competition::$officialRoles[$comp->pivot->role] ?? $comp->pivot->role,
                'added' => $comp->pivot->created_at?->toDateString(),
            ]);

        $submissions = $user->violations()
            ->selectRaw('count(*) as total')
            ->selectRaw('sum(case when resolved is null then 1 else 0 end) as pending')
            ->selectRaw('sum(case when resolved = 1 then 1 else 0 end) as accepted')
            ->selectRaw('sum(case when resolved = 0 then 1 else 0 end) as rejected')
            ->toBase()
            ->first();

        $lastActive = $user->logins()->withExpired()->max('last_activity_at');

        return Inertia::render('Admin/User', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'officialOnly' => $user->isJudgeOnly(),
                'admin' => (bool) $user->isAdmin(),
                'joined' => $user->created_at?->toDateString(),
                'verified' => $user->email_verified_at?->toDateString(),
                // admin-made official accounts stay unverified until they first sign in with a PIN
                'neverSignedIn' => $user->isJudgeOnly() && !$user->hasVerifiedEmail(),
                'lastActive' => $lastActive ? Carbon::parse($lastActive)->diffForHumans() : null,
            ],
            'competitions' => $competitions,
            'organisations' => $organisations,
            'officiating' => $officiating,
            'submissions' => $submissions?->total > 0 ? [
                'pending' => (int) $submissions->pending,
                'accepted' => (int) $submissions->accepted,
                'rejected' => (int) $submissions->rejected,
            ] : null,
        ]);
    }
}
