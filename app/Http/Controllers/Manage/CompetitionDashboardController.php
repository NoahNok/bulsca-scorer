<?php

namespace App\Http\Controllers\Manage;

use App\Helpers\CompetitionHeader;
use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\SERC;
use App\Models\User;
use App\Models\UserCompetitionAccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

/**
 * A competition's Svelte pages (Overview and Settings), inside CompetitionLayout. Every page in that
 * layout sends the `competition` prop from CompetitionHeader::for().
 */
class CompetitionDashboardController extends Controller
{
    public function overview(Competition $comp)
    {
        $can = fn (string $access) => Gate::allows('access', [$comp, $access]);

        $events = collect();

        if ($can('speed')) {
            $events = $events->concat($comp->getSpeedEvents->map(fn (CompetitionSpeedEvent $event) => [
                'key' => 'sp:' . $event->id,
                'name' => $event->getName(),
                'serc' => false,
                'url' => route('manage.competitions.events.speeds.show', [$comp, $event]),
                'digitalJudge' => (bool) $event->digitalJudgeEnabled,
            ]));
        }

        if ($can('serc|serc_writer')) {
            $events = $events->concat($comp->getSERCs->map(fn (SERC $serc) => [
                'key' => 'se:' . $serc->id,
                'name' => $serc->getName(),
                'serc' => true,
                'url' => route('manage.competitions.events.sercs.show', [$comp, $serc]),
                'digitalJudge' => (bool) $serc->digitalJudgeEnabled,
            ]));
        }

        return Inertia::render('Manage/Competition/Overview', [
            'competition' => CompetitionHeader::for($comp),
            // Null when you can't open the Events tab at all, so there's no list or "Add event"
            'events' => $can('serc|speed|serc_writer') ? $events->values() : null,
            'addEventUrl' => route('manage.competitions.events', $comp),
            'digitalJudge' => $can('admin') ? [
                'enabled' => (bool) $comp->digitalJudgeEnabled,
                'pin' => $comp->digitalJudgePin,
                'judgeUrl' => route('judge.index'),
                'officialsUrl' => route('manage.competitions.officials', $comp),
                'qrUrl' => route('dj.qrs', $comp),
                'toggleUrl' => route('dj.toggle', $comp),
                'releaseUrl' => route('dj.sercs-release', $comp),
                'sercsReleasedAt' => $comp->sercs_released_at?->toIso8601String(),
            ] : null,
            'links' => $can('admin') ? [
                'settingsUrl' => route('manage.competitions.settings', $comp),
                'activityLogUrl' => route('comps.activityLog', $comp),
            ] : null,
        ]);
    }

    public function settings(Competition $comp)
    {
        $scoring = $comp->getScoringSettings;
        $you = Auth::user();

        $accounts = UserCompetitionAccess::where('competition', $comp->id)->get()->groupBy('user')
            ->map(function ($access, $userId) use ($you) {
                $user = User::find($userId);
                $keys = $access->pluck('access_to');

                return $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'access' => $keys->reject(fn ($key) => $key === 'owner')->values(),
                    'owner' => $keys->contains('owner'),
                    'you' => $user->is($you),
                ] : null;
            })
            ->filter()->sortBy('name')->values();

        return Inertia::render('Manage/Competition/Settings', [
            'competition' => CompetitionHeader::for($comp),
            'settings' => [
                'name' => $comp->name,
                'when' => $comp->when->toDateString(),
                'where' => $comp->where,
                'max_lanes' => $comp->max_lanes,
                // UTC: the page shows it in the browser's time, and sends its timezone back with it
                'serc_start_time' => $comp->serc_start_time?->toIso8601String(),
                'show_competition' => (bool) $comp->show_competition,
                'can_be_live' => (bool) $comp->can_be_live,
                'select_league_first' => (bool) $comp->select_league_first,
                'use_tanks' => (bool) $scoring->use_tanks,
                'use_seeds' => (bool) $comp->use_seeds,
                'heats_per_event' => (bool) $comp->heats_per_event,
                'seed_per_event' => (bool) $comp->seed_per_event,
                'use_pools' => (bool) $comp->use_pools,
                // Turning pools off deletes these, so the page asks first
                'pooled_heats' => $comp->use_pools ? $comp->pooledHeats()->count() : 0,
                'team_format' => $comp->team_format,
                'competitor_format' => $comp->competitor_format,
                'status_message' => $comp->status_message,
            ],
            'digitalJudge' => [
                'enabled' => (bool) $comp->digitalJudgeEnabled,
                'showTeams' => (bool) $comp->show_teams_to_judges,
                'events' => $comp->getSpeedEvents->map(fn (CompetitionSpeedEvent $event) => [
                    'key' => 'sp:' . $event->id,
                    'name' => $event->getName(),
                    'serc' => false,
                    'enabled' => (bool) $event->digitalJudgeEnabled,
                ])->concat($comp->getSERCs->map(fn (SERC $serc) => [
                    'key' => 'se:' . $serc->id,
                    'name' => $serc->getName(),
                    'serc' => true,
                    'enabled' => (bool) $serc->digitalJudgeEnabled,
                ]))->values(),
            ],
            'accounts' => $accounts,
            'accessTypes' => Competition::$accessTypes,
        ]);
    }
}
