<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Competition;
use App\Models\Organisation\Organisation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        return view('admin.users.index', [
            'users' => $users,
            'q' => $q,
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
                'when' => $rows->first()->when ? Carbon::parse($rows->first()->when) : null,
                'access' => $rows->whereNull('organisation')->pluck('access_to')
                    ->map(fn ($access) => $accessNames[$access] ?? $access)->values()->all(),
                'via' => $rows->whereNotNull('organisation')->groupBy('organisation')
                    ->map(fn ($orgRows) => $orgRows->pluck('access_to')
                        ->map(fn ($access) => $orgAccessNames[$access] ?? $access)->implode(', '))
                    ->all(),
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
                'name' => $rows->first()->name,
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
                'when' => $comp->when,
                'role' => $comp->pivot->role,
                'roleName' => Competition::$officialRoles[$comp->pivot->role] ?? $comp->pivot->role,
                'added' => $comp->pivot->created_at,
            ]);

        $submissions = $user->violations()
            ->selectRaw('count(*) as total')
            ->selectRaw('sum(case when resolved is null then 1 else 0 end) as pending')
            ->selectRaw('sum(case when resolved = 1 then 1 else 0 end) as accepted')
            ->selectRaw('sum(case when resolved = 0 then 1 else 0 end) as rejected')
            ->toBase()
            ->first();

        $lastActive = $user->logins()->withExpired()->max('last_activity_at');

        return view('admin.users.show', [
            'user' => $user,
            'competitions' => $competitions,
            'organisations' => $organisations,
            'officiating' => $officiating,
            'submissions' => $submissions,
            // admin-made official accounts stay unverified until they first sign in with a PIN
            'neverSignedIn' => $user->isJudgeOnly() && !$user->hasVerifiedEmail(),
            'lastActive' => $lastActive ? Carbon::parse($lastActive) : null,
        ]);
    }
}
