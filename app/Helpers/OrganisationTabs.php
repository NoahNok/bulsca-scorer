<?php

namespace App\Helpers;

use App\Models\Organisation\Organisation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * An organisation's page tabs, for whoever's signed in, which OrganisationLayout.svelte draws.
 */
class OrganisationTabs
{
    /**
     * Each tab's label, route, the access it needs, the route-name prefixes that mark it current, and
     * whether it's a Svelte page (so the Svelte tabs can visit it without a full page load).
     */
    private const TABS = [
        ['Overview', 'manage.organisations.show', '*', ['manage.organisations.show'], true],
        ['Championships', 'manage.organisations.championships', 'admin', ['manage.organisations.championships'], true],
        ['Accounts', 'manage.organisations.accounts', 'admin', ['manage.organisations.accounts'], true],
        ['Officials', 'manage.organisations.officials', 'admin', ['manage.organisations.officials'], true],
        ['Scoring', 'manage.organisations.scoring', 'admin', ['manage.organisations.scoring'], true],
        ['Violations', 'manage.organisations.violations', 'admin', ['manage.organisations.violations'], true],
        ['Settings', 'manage.organisations.settings', 'admin', ['manage.organisations.settings'], true],
    ];

    /**
     * @return array<int, array{label: string, url: string, current: bool, svelte: bool}>
     */
    public static function for(Organisation $org): array
    {
        $routeName = Route::currentRouteName() ?? '';

        return collect(self::TABS)
            ->filter(fn (array $tab) => Gate::allows('access', [$org, $tab[2]]))
            ->map(fn (array $tab) => [
                'label' => $tab[0],
                'url' => route($tab[1], $org),
                'current' => Str::startsWith($routeName, $tab[3]),
                'svelte' => $tab[4],
            ])
            ->values()
            ->all();
    }
}
