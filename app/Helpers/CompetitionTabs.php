<?php

namespace App\Helpers;

use App\Models\Competition;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * A competition's page tabs, for whoever's signed in: the Blade layout (layouts/competition.blade.php)
 * and the Svelte one (manage/layout/CompetitionLayout.svelte) both draw these, so they always agree.
 */
class CompetitionTabs
{
    /**
     * Each tab's label, route, the access it needs, the route-name prefixes that mark it current, and
     * whether it's a Svelte page (so the Svelte tabs can visit it without a full page load).
     */
    private const TABS = [
        ['Overview', 'comps.view', 'view', ['comps.view'], true],
        ['Entries & Leagues', 'manage.competitions.entries', 'teams', ['manage.competitions.entries', 'manage.competitions.leagues'], true],
        ['Events', 'comps.events', 'serc|speed|serc_writer', ['comps.events'], false],
        ['Heats & Draws', 'comps.heats_and_draws', 'heats_and_draws', ['comps.heats_and_draws'], false],
        ['Printables', 'comps.printables', 'printables', ['comps.printables'], false],
        ['Results', 'comps.results', 'results', ['comps.results'], false],
        ['Officials', 'comps.officials', 'admin', ['comps.officials'], false],
        ['Settings', 'manage.competitions.settings', 'admin', ['manage.competitions.settings'], true],
    ];

    /**
     * @return array<int, array{label: string, url: string, current: bool, svelte: bool}>
     */
    public static function for(Competition $comp): array
    {
        $routeName = Route::currentRouteName() ?? '';

        return collect(self::TABS)
            ->filter(fn (array $tab) => Gate::allows('access', [$comp, $tab[2]]))
            ->map(fn (array $tab) => [
                'label' => $tab[0],
                'url' => route($tab[1], $comp),
                'current' => Str::startsWith($routeName, $tab[3]),
                'svelte' => $tab[4],
            ])
            ->values()
            ->all();
    }
}
