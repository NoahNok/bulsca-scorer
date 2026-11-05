<?php

namespace App\Helpers;

use App\Models\Competition;
use Illuminate\Support\Collection;

/**
 * Competitions as the Svelte CompetitionTable lists them (ManagedCompetition in manage/types.ts): the
 * Management home's and an organisation's Overview and Championships.
 */
class ManagedCompetitions
{
    /**
     * One row. `limited` is an Organiser with only some permissions, e.g. results.
     */
    public static function row(Competition $comp, bool $limited = false): array
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
     *
     * @return array{0: Collection, 1: Collection}
     */
    public static function upcomingThenPast(Collection $comps): array
    {
        [$upcoming, $past] = $comps->partition(fn (Competition $comp) => $comp->when->gte(today()));

        return [$upcoming->sortBy('when')->values(), $past->sortByDesc('when')->values()];
    }
}
