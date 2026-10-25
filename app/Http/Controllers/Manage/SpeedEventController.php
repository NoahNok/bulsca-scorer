<?php

namespace App\Http\Controllers\Manage;

use App\DTO\Result;
use App\Helpers\CompetitionHeader;
use App\Http\Controllers\Controller;
use App\Http\Requests\Event\UpdateScoringSettings;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\Event\ScoringSchema;
use App\Models\League;
use App\Models\SpeedResult;
use App\Services\HeatService;
use App\Services\ScoringSchemas;
use App\Services\SpeedResultEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

/**
 * A speed event's pages (Svelte, in CompetitionLayout): its ranked results, a page with the grid to
 * enter them, and its settings (setup, scoring and deleting it) in a SideMenu, each saved on its own.
 */
class SpeedEventController extends Controller
{
    public function show(Competition $comp, CompetitionSpeedEvent $event, Request $request, HeatService $heats)
    {
        $league = $comp->getLeagues()->find($request->query('league'));
        $schema = $event->scoringSchema;

        return Inertia::render('Manage/Competition/SpeedEvent', [
            ...$this->shared($comp, $event),
            'leagues' => $comp->getLeagues()->orderBy('name')->get(['id', 'name']),
            'league' => $league?->id,
            // Null until the event has scoring set up
            'results' => $schema ? fn () => $this->rankedResults($comp, $event, $league) : null,
            'conflicts' => fn () => collect($heats->anyTimeAndOofConflicts($event))
                ->map(fn ($rows, $heat) => [
                    'heat' => $heat,
                    'rows' => collect($rows)->map(fn ($row) => collect($row)->only('lane', 'entity_name', 'time', 'oof')),
                ])->values(),
            'eventsUrl' => route('manage.competitions.events', $comp),
            'printUrl' => route('comps.view.events.speeds.printResults', [$comp, $event]),
        ]);
    }

    public function editResults(Competition $comp, CompetitionSpeedEvent $event)
    {
        return Inertia::render('Manage/Competition/SpeedEventResults', [
            ...$this->shared($comp, $event),
            'grid' => $this->grid($comp, $event),
        ]);
    }

    public function settings(Competition $comp, CompetitionSpeedEvent $event)
    {
        $schema = $event->scoringSchema;

        return Inertia::render('Manage/Competition/SpeedEventSettings', [
            ...$this->shared($comp, $event),
            'scoring' => [
                'schema' => $schema ? ScoringSchemas::withDefaults($schema->schema) : null,
                'options' => ScoringSchemas::availableFor(Auth::user(), $comp->getOrganisation)
                    ->map(fn ($option) => [...$option, 'schema' => ScoringSchemas::withDefaults($option['schema'])]),
            ],
        ]);
    }

    /**
     * The competition and the event, as every one of its pages takes them
     */
    private function shared(Competition $comp, CompetitionSpeedEvent $event): array
    {
        return [
            'competition' => CompetitionHeader::for($comp),
            'event' => [
                'id' => $event->id,
                'name' => $event->getName(),
                'target' => $event->scorable_entity,
                'complete' => $event->isComplete(),
                'viewable' => (bool) $event->viewable,
                'hasPenalties' => (bool) $event->hasPenalties(),
                'digitalJudge' => (bool) $event->digitalJudgeEnabled,
                'ropeThrow' => $event->getName() == 'Rope Throw',
                'heats' => $event->getHeats()->count(),
            ],
        ];
    }

    /**
     * The ranked results as the event's page shows them. A combined result (one row standing for
     * several Entities) lists each one's name, time and DQ, with the total time beside them.
     */
    private function rankedResults(Competition $comp, CompetitionSpeedEvent $event, ?League $league): array
    {
        $showDqPoints = $event->scoringSchema->schema['allow_disqualified_to_rank'] ?? false;
        $eventHeats = $event->digitalJudgeEnabled ? $event->getHeats()->with('oofs')->get() : collect();

        return collect($event->getRankedResults($league))->map(function ($result) use ($comp, $event, $showDqPoints, $eventHeats) {
            $parts = $result->isCombined() ? $result->combined : collect([$result]);
            $heat = $result->isCombined() ? null : $eventHeats
                ->where('entity_id', $result->entity->id)
                ->where('entity_type', $result->entity->getMorphClass())
                ->first();

            return [
                'key' => $parts->map(fn (Result $part) => $part->entity->getMorphClass() . ':' . $part->entity->id)->implode(','),
                'names' => $parts->map(fn (Result $part) => $part->entity->getName($comp))->values(),
                'times' => $result->isCombined() ? $parts->map(fn (Result $part) => SpeedResult::prettyTime($part->result))->values() : [],
                'dqs' => $parts->map(fn (Result $part) => $part->getDisqualificationsString())->values(),
                'time' => SpeedResult::prettyTime($result->resolvedResult),
                'was' => !$result->isCombined() && $result->resolvedResult != $result->result ? SpeedResult::prettyTime($result->result) : null,
                'oof' => !$event->digitalJudgeEnabled || $result->isCombined() ? null
                    : ($heat ? "H{$heat->heat}L{$heat->lane}: " . ($heat->getOOF($event->id)?->oof ?: '-') : '-'),
                'penalties' => $result->getPenaltiesString(),
                'points' => $result->isDisqualified() && !$showDqPoints ? 'DQ' : (round($result->points, 1) ?: null),
                'position' => $result->position ?? null,
            ];
        })->values()->all();
    }

    /**
     * Every result as the grid edits it, in heat and lane order (those without a heat last), with
     * DNS, DNF and OOT shown in the time cell, where they're entered
     */
    private function grid(Competition $comp, CompetitionSpeedEvent $event): array
    {
        $heats = $event->getHeats()->get()->keyBy(fn ($heat) => $heat->entity_type . ':' . $heat->entity_id);

        return collect($event->getRawResults(true))
            ->map(function (Result $result) use ($comp, $heats) {
                $heat = $heats->get($result->entity->getMorphClass() . ':' . $result->entity->id);
                $dq = $result->getDisqualificationsString();
                $special = in_array($dq, ['DNS', 'DNF', 'OOT']);

                return [
                    'id' => $result->id,
                    'name' => $result->entity->getName($comp),
                    'heat' => $heat?->heat,
                    'lane' => $heat?->lane,
                    'result' => $special ? $dq : (string) (SpeedResult::prettyTime($result->result) ?? ''),
                    'disqualification' => $special ? '' : ($dq ?? ''),
                    'penalties' => $result->getPenaltiesString() ?? '',
                ];
            })
            ->sortBy([
                fn ($a, $b) => ($a['heat'] ?? PHP_INT_MAX) <=> ($b['heat'] ?? PHP_INT_MAX),
                fn ($a, $b) => ($a['lane'] ?? PHP_INT_MAX) <=> ($b['lane'] ?? PHP_INT_MAX),
            ])
            ->values()
            ->all();
    }

    public function saveResults(Competition $comp, CompetitionSpeedEvent $event, Request $request)
    {
        $rows = $request->validate([
            'rows' => ['required', 'array'],
            'rows.*' => ['array'],
            'rows.*.result' => ['nullable', 'string'],
            'rows.*.disqualification' => ['nullable', 'string'],
            'rows.*.penalties' => ['nullable', 'string'],
        ])['rows'];

        (new SpeedResultEntry($comp, $event))->save($rows);

        return back()->with('success', 'Results saved');
    }

    /**
     * What the event scores and whether it takes penalties. Scoring different Entities starts its
     * results again, with an empty one for each.
     */
    public function saveSetup(Competition $comp, CompetitionSpeedEvent $event, Request $request)
    {
        $data = $request->validate([
            'target_entity' => ['required', 'in:club,team,competitor'],
            'has_penalties' => ['required', 'boolean'],
        ]);

        if ($data['target_entity'] != $event->scorable_entity) {
            $event->scorable_entity = $data['target_entity'];
            $event->save();

            $event->results()->delete();
            foreach ($event->getScorableEntities() as $entity) {
                $result = new SpeedResult();
                $result->entity()->associate($entity);
                $result->event = $event->id;
                $result->save();
            }
        }

        $event->has_penalties = $data['has_penalties'];
        $event->save();

        return back()->with('success', 'Event saved');
    }

    public function visibility(Competition $comp, CompetitionSpeedEvent $event, Request $request)
    {
        $event->viewable = $request->validate(['viewable' => ['required', 'boolean']])['viewable'];
        $event->save();

        return back()->with('success', $event->viewable ? 'Shown in results' : 'Hidden from results');
    }

    public function saveScoring(Competition $comp, CompetitionSpeedEvent $event, UpdateScoringSettings $request)
    {
        $schema = $event->scoringSchema;

        if (!$schema) {
            $schema = new ScoringSchema();
            $schema->name = "Scoring Schema for {$event->getType()}:{$event->id}";
            $schema->schema = [];
            $schema->save();
            $event->scoring_schema = $schema->id;
            $event->save();
        }

        $schema->editFromRequest($request);

        return back()->with('success', 'Scoring saved');
    }

    /**
     * Deleting needs the event's name typed out
     */
    public function destroy(Competition $comp, CompetitionSpeedEvent $event, Request $request)
    {
        $name = $event->getName();

        if ($request->validate(['name' => ['required', 'string']])['name'] != $name) {
            throw ValidationException::withMessages(['name' => "That isn't the event's name."]);
        }

        $event->delete();

        return redirect()->route('manage.competitions.events', $comp)->with('success', "{$name} deleted");
    }
}
