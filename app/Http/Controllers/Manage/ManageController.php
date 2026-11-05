<?php

namespace App\Http\Controllers\Manage;

use App\Helpers\ManagedCompetitions;
use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionVisit;
use App\Models\Organisation\Organisation;
use App\Models\Organisation\OrganisationUserAccess;
use App\Models\User;
use App\Models\UserCompetitionAccess;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ManageController extends Controller
{
    /** Competitions in "Recent" */
    private const RECENT_SHOWN = 6;

    /**
     * The Management app's home (Svelte), at / on app.
     */
    public function home()
    {
        /** @var User $user */
        $user = Auth::user();

        $orgs = $user->getOrganisations()->orderBy('name')->get();
        $orgIds = $orgs->pluck('id');

        $directIds = UserCompetitionAccess::where('user', $user->id)->where('access_to', '!=', 'none')->select('competition');

        // Every competition in your organisations, and any others you were given access to, each once
        [$upcoming, $past] = ManagedCompetitions::upcomingThenPast(Competition::whereIn('id', $directIds)->orWhereIn('organisation', $orgIds)->get());

        // Admins can open any competition, so theirs all stay
        $recent = CompetitionVisit::where('user_id', $user->id)
            ->unless($user->isAdmin(), fn ($query) => $query->whereHas('competition', fn ($comp) => $comp
                ->whereIn('id', $directIds)->orWhereIn('organisation', $orgIds)))
            ->orderByDesc('visited_at')
            ->with('competition')->limit(self::RECENT_SHOWN)->get()->pluck('competition');

        // Everything an Organiser with full access can do, looked up once rather than per row
        $fullComps = UserCompetitionAccess::where('user', $user->id)->whereIn('access_to', ['admin', 'owner'])->pluck('competition');
        $fullOrgs = OrganisationUserAccess::where('user', $user->id)->whereIn('access_to', ['admin', 'owner'])->pluck('organisation');
        $row = fn (Competition $comp) => ManagedCompetitions::row($comp, limited: !$user->isAdmin()
            && !$fullComps->contains($comp->id) && !$fullOrgs->contains($comp->organisation));

        return Inertia::render('Manage/Home', [
            'recent' => $recent->map($row),
            // Yours, then ones you were only invited into a competition of, which have no page you can open
            'organisations' => $orgs->concat(Organisation::whereIn('id', $upcoming->concat($past)->pluck('organisation')->filter())
                ->whereNotIn('id', $orgIds)->orderBy('name')->get())
                ->map(fn (Organisation $org) => [
                    'id' => $org->id,
                    'name' => $org->name,
                    'logo' => $org->getLogo() ?: null,
                    'url' => $orgIds->contains($org->id) ? route('manage.organisations.show', $org) : null,
                ]),
            // All of them: the page filters by organisation (none is Personal) and shows the upcoming and latest past until "Show all"
            'competitions' => $upcoming->concat($past)->map(fn (Competition $comp) => [...$row($comp), 'organisation' => $comp->organisation]),
        ]);
    }
}
