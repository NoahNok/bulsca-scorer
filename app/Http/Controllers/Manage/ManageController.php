<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionVisit;
use App\Models\Organisation\Organisation;
use App\Models\Organisation\OrganisationUserAccess;
use App\Models\User;
use App\Models\UserCompetitionAccess;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ManageController extends Controller
{
    /** Past competitions shown per section before "View all" */
    private const PAST_SHOWN = 3;

    /** Competitions in "Recent" */
    private const RECENT_SHOWN = 6;

    /**
     * The Management app's home (Svelte), at / on app.
     */
    public function home()
    {
        /** @var User $user */
        $user = Auth::user();

        $orgs = $user->getOrganisations()->with('getCompetitions')->orderBy('name')->get();
        $orgIds = $orgs->pluck('id');

        $directIds = UserCompetitionAccess::where('user', $user->id)->where('access_to', '!=', 'none')->select('competition');

        // Direct access to a competition one of your organisations already covers shows under the organisation
        [$upcoming, $past] = $this->upcomingThenPast(Competition::whereIn('id', $directIds)
            ->where(fn ($query) => $query->whereNull('organisation')->orWhereNotIn('organisation', $orgIds))
            ->get());

        // Admins can open any competition, so theirs all stay
        $recent = CompetitionVisit::where('user_id', $user->id)
            ->unless($user->isAdmin(), fn ($query) => $query->whereHas('competition', fn ($comp) => $comp
                ->whereIn('id', $directIds)->orWhereIn('organisation', $orgIds)))
            ->orderByDesc('visited_at')
            ->with('competition')->limit(self::RECENT_SHOWN)->get()->pluck('competition');

        // Everything an Organiser with full access can do, looked up once rather than per row
        $fullComps = UserCompetitionAccess::where('user', $user->id)->whereIn('access_to', ['admin', 'owner'])->pluck('competition');
        $fullOrgs = OrganisationUserAccess::where('user', $user->id)->whereIn('access_to', ['admin', 'owner'])->pluck('organisation');
        $row = fn (Competition $comp) => $this->row($comp, limited: !$user->isAdmin()
            && !$fullComps->contains($comp->id) && !$fullOrgs->contains($comp->organisation));

        return Inertia::render('Manage/Home', [
            'recent' => $recent->map($row),
            'organisations' => $orgs->map(function (Organisation $org) use ($row) {
                [$upcoming, $past] = $this->upcomingThenPast($org->getCompetitions);

                return [
                    'name' => $org->name,
                    'logo' => $org->getLogo() ?: null,
                    'url' => route('orgs.show', $org),
                    'competitions' => $upcoming->concat($past->take(self::PAST_SHOWN))->map($row),
                    'more' => $past->count() > self::PAST_SHOWN,
                ];
            }),
            // All of them, as there's no page to link to: the page shows as many as an organisation, then expands in place
            'competitions' => $upcoming->concat($past)->map($row),
            'competitionsShown' => $upcoming->count() + min($past->count(), self::PAST_SHOWN),
        ]);
    }

    private function row(Competition $comp, bool $limited): array
    {
        $today = $comp->when->isToday();

        return [
            'id' => $comp->id,
            'name' => $comp->name,
            'when' => $comp->when->toDateString(),
            'where' => $comp->where,
            'url' => route('comps.view', $comp),
            'past' => $comp->when->lt(today()),
            // From today, so it agrees with past and today whatever the browser's clock says
            'days' => (int) round(today()->diffInDays($comp->when->copy()->startOfDay())),
            'today' => $today,
            // No flag says a competition is running, so Live is today with the live site turned on
            'live' => $today && (bool) $comp->can_be_live,
            'private' => !$comp->show_competition,
            'limited' => $limited,
        ];
    }

    /**
     * Splits competitions into those today or later (soonest first) and past ones (latest first).
     */
    private function upcomingThenPast(Collection $comps): array
    {
        [$upcoming, $past] = $comps->partition(fn (Competition $comp) => $comp->when->gte(today()));

        return [$upcoming->sortBy('when')->values(), $past->sortByDesc('when')->values()];
    }
}
