<?php

namespace App\Http\Controllers\Manage;

use App\DTO\Result;
use App\Helpers\CompetitionHeader;
use App\Http\Controllers\Controller;
use App\Http\Requests\Event\UpdateScoringSettings;
use App\Http\Requests\SERC\SaveSERCSetupRequest;
use App\Models\AbstractClasses\Entity;
use App\Models\Competition;
use App\Models\League;
use App\Models\SERC;
use App\Models\SERC\MarkingPointTemplate;
use App\Models\SERCJudge;
use App\Models\SERCMarkingPoint;
use App\Models\SERCResult;
use App\Services\ScoringSchemas;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

/**
 * A SERC's pages (Svelte, in CompetitionLayout): its ranked results and every Entity in draw order, a
 * page to edit its setup (also used to add one), and its settings (scoring and deleting it).
 * A SERC Writer sees its results and edits its setup; the rest needs SERC access.
 */
class SercEventController extends Controller
{
    public function show(Competition $comp, SERC $serc, Request $request)
    {
        $league = $comp->getLeagues()->find($request->query('league'));
        $marking = $this->marking($comp, $serc);

        return Inertia::render('Manage/Competition/Serc', [
            ...$this->shared($comp, $serc),
            'leagues' => $comp->getLeagues()->orderBy('name')->get(['id', 'name']),
            'league' => $league?->id,
            'marking' => [
                'marked' => $marking->sum('marked'),
                'possible' => $marking->sum('possible'),
            ],
            // Null until the SERC has scoring set up
            'results' => $serc->scoringSchema ? fn () => $this->rankedResults($comp, $serc, $league, $marking) : null,
            'entities' => $marking->values(),
            'drawn' => $serc->getDraw()->exists(),
            'eventsUrl' => route('manage.competitions.events', $comp),
            'printUrl' => route('comps.view.events.sercs.printResults', [$comp, $serc]),
            // Mark sheets are a printable, which needs its own access
            'sheetsUrl' => Gate::allows('access', [$comp, 'printables']) ? route('comps.printables.serc-sheets', [$comp, $serc]) : null,
        ]);
    }

    public function create(Competition $comp)
    {
        return Inertia::render('Manage/Competition/SercSetup', [
            'competition' => CompetitionHeader::for($comp),
            'event' => null,
            ...$this->editor($comp, null),
        ]);
    }

    public function setup(Competition $comp, SERC $serc)
    {
        return Inertia::render('Manage/Competition/SercSetup', [
            ...$this->shared($comp, $serc),
            ...$this->editor($comp, $serc),
        ]);
    }

    public function settings(Competition $comp, SERC $serc)
    {
        return Inertia::render('Manage/Competition/SercSettings', [
            ...$this->shared($comp, $serc),
            'scoring' => ScoringSchemas::editorFor($serc, Auth::user(), $comp->getOrganisation),
        ]);
    }

    /**
     * The competition and the SERC, as every one of its pages takes them
     */
    private function shared(Competition $comp, SERC $serc): array
    {
        return [
            'competition' => CompetitionHeader::for($comp),
            'event' => [
                'id' => $serc->id,
                'name' => $serc->getName(),
                'type' => $serc->type,
                'target' => $serc->scorable_entity,
                'complete' => $serc->isComplete(),
                'viewable' => (bool) $serc->viewable,
                'leagueObjectives' => (bool) $serc->use_restricted_judges,
                // A SERC Writer only sees its results and edits its setup
                'canManage' => Gate::allows('access', [$comp, 'serc']),
            ],
        ];
    }

    /**
     * What the setup editor starts from: the SERC's settings and Objectives (blank for a new one),
     * the competition's leagues and the marking point templates
     */
    private function editor(Competition $comp, ?SERC $serc): array
    {
        return [
            'setup' => [
                'name' => $serc?->getName() ?? '',
                'type' => $serc?->type ?? 'DRY',
                'target' => $serc?->scorable_entity ?? 'team',
                'leagueObjectives' => (bool) $serc?->use_restricted_judges,
                'objectives' => $serc?->getSERCConfiguration() ?? [],
            ],
            'leagues' => $comp->getLeagues()->orderBy('name')->get(['id', 'name']),
            'templates' => MarkingPointTemplate::orderBy('default', 'desc')->orderBy('name')->get(['id', 'name']),
            'defaultTemplate' => MarkingPointTemplate::where('default', true)->value('id'),
        ];
    }

    /**
     * Every Entity the SERC scores, in draw order (tank by tank when the competition uses tanks; those
     * not in the draw last), with how many of its marks are in. With League Objectives on, an Entity
     * is marked only against its League's Objective, so `possible` is that Objective's marking points,
     * and null when its Leagues give it none or several.
     *
     * @return Collection<string, array{key: string, id: int, name: string, draw: ?string, marked: int, possible: ?int}>
     */
    private function marking(Competition $comp, SERC $serc): Collection
    {
        $objectives = $serc->getJudges()->with('restrictedLeagues:id', 'getMarkingPoints:id,judge')->get();
        $markingPoints = $objectives->sum(fn (SERCJudge $objective) => $objective->getMarkingPoints->count());

        $marked = SERCResult::whereIn('marking_point', $serc->getMarkingPoints()->select('serc_marking_points.id'))
            ->selectRaw('entity_type, entity_id, count(*) as marks')
            ->groupBy('entity_type', 'entity_id')
            ->get()
            ->mapWithKeys(fn ($row) => ["{$row->entity_type}:{$row->entity_id}" => (int) $row->marks]);

        $useTanks = $comp->getScoringSettings?->use_tanks;
        $draw = $serc->getDraw()->get()->sortBy([['tank', 'asc'], ['draw', 'asc']])
            ->mapWithKeys(fn ($draw) => [
                "{$draw->entity_type}:{$draw->entity_id}" => ($useTanks ? "Tank {$draw->tank}-" : '') . $draw->draw,
            ]);
        $order = $draw->keys()->flip();

        $entities = $serc->getScorableEntities();
        if ($serc->use_restricted_judges) {
            $entities->load('leagues:id');
        }

        return $entities
            ->map(function (Entity $entity) use ($comp, $serc, $objectives, $markingPoints, $marked, $draw) {
                $key = $entity->getMorphClass() . ':' . $entity->id;
                $possible = $markingPoints;

                if ($serc->use_restricted_judges) {
                    $leagues = $entity->leagues->pluck('id');
                    $its = $objectives->filter(fn (SERCJudge $objective) => $objective->restrictedLeagues->pluck('id')->intersect($leagues)->isNotEmpty());
                    $possible = $its->count() == 1 ? $its->first()->getMarkingPoints->count() : null;
                }

                return [
                    'key' => $key,
                    'id' => $entity->id,
                    'name' => $entity->getName($comp),
                    'draw' => $draw->get($key),
                    'marked' => $marked->get($key, 0),
                    'possible' => $possible,
                ];
            })
            ->sortBy(fn ($entity) => $order->get($entity['key'], PHP_INT_MAX))
            ->keyBy('key');
    }

    /**
     * The ranked results as the SERC's page shows them, each with its Entity's marking. A combined
     * result (one row standing for several Entities) lists each one's name.
     */
    private function rankedResults(Competition $comp, SERC $serc, ?League $league, Collection $marking): array
    {
        return collect($serc->getRankedResults($league))->map(function ($result) use ($comp, $marking) {
            $parts = $result->isCombined() ? $result->combined : collect([$result]);
            $key = $parts->map(fn (Result $part) => $part->entity->getMorphClass() . ':' . $part->entity->id)->implode(',');

            return [
                'key' => $key,
                'names' => $parts->map(fn (Result $part) => $part->entity->getName($comp))->values(),
                'dq' => $result->getDisqualificationsString(),
                'mark' => round($result->resolvedResult, 1),
                'points' => round($result->points ?? 0, 1),
                'disqualified' => $result->isDisqualified(),
                'position' => $result->position ?? null,
            ];
        })->values()->all();
    }

    public function store(Competition $comp, SaveSERCSetupRequest $request)
    {
        $serc = new SERC();
        $serc->competition = $comp->id;

        $this->saveSetupOf($comp, $serc, $request);

        return redirect()->route('manage.competitions.events.sercs.show', [$comp, $serc])->with('success', "{$serc->getName()} added");
    }

    public function saveSetup(Competition $comp, SERC $serc, SaveSERCSetupRequest $request)
    {
        $this->saveSetupOf($comp, $serc, $request);

        return back()->with('success', 'Setup saved');
    }

    /**
     * Saves the SERC's settings and its Objectives with their marking points, creating, changing and
     * deleting them to match what was sent
     */
    private function saveSetupOf(Competition $comp, SERC $serc, SaveSERCSetupRequest $request): void
    {
        $validated = $request->validated();

        // every Objective and marking point id sent must already belong to this SERC, so one
        // competition can't delete or take over another's (a new SERC has none yet)
        $judgeIds = collect($validated['judges'])->pluck('id')
            ->merge($validated['deleted']['judges'] ?? [])->filter()->unique();
        $markingPointIds = collect($validated['judges'])->pluck('marking_points')->flatten(1)->pluck('id')
            ->merge($validated['deleted']['marking_points'] ?? [])->filter()->unique();

        if (SERCJudge::where('serc', $serc->id ?? 0)->whereIn('id', $judgeIds)->count() != $judgeIds->count()) {
            throw ValidationException::withMessages(['judges' => 'That Objective isn\'t on this SERC.']);
        }
        if (SERCMarkingPoint::where('serc', $serc->id ?? 0)->whereIn('id', $markingPointIds)->count() != $markingPointIds->count()) {
            throw ValidationException::withMessages(['judges' => 'That marking point isn\'t on this SERC.']);
        }

        // changing which Objective a League is marked against strands the marks already entered,
        // so the Organiser has to confirm it once they know how many marks are affected
        $affectedMarks = $this->marksAffectedByLeagueObjectiveChanges($serc, $validated);
        if ($affectedMarks > 0 && !($validated['confirm_affected_marks'] ?? false)) {
            throw ValidationException::withMessages([
                'confirm_affected_marks' => ($affectedMarks == 1 ? '1 mark has' : "{$affectedMarks} marks have")
                    . ' already been entered against the current League Objectives. After this change, the Entities they belong to show as unmarked against their new Objective.',
            ]);
        }

        SERCJudge::destroy($validated['deleted']['judges'] ?? []);
        SERCMarkingPoint::destroy($validated['deleted']['marking_points'] ?? []);

        $serc->name = $validated['name'];
        $serc->type = $validated['type'];
        $serc->scorable_entity = $validated['target_entity'];
        if (array_key_exists('use_restricted_judges', $validated)) {
            $serc->use_restricted_judges = $validated['use_restricted_judges'];
        }
        $serc->save();

        foreach ($validated['judges'] as $judge) {
            $objective = $judge['id'] == null ? new SERCJudge() : SERCJudge::find($judge['id']);
            $objective->name = $judge['name'];
            $objective->description = $judge['hint'] ?? '';
            $objective->serc = $serc->id;
            $objective->save();

            if (array_key_exists('restricted_leagues', $judge)) {
                $objective->restrictedLeagues()->sync($judge['restricted_leagues']);
            }

            foreach ($judge['marking_points'] as $markingPoint) {
                $mp = $markingPoint['id'] == null ? new SERCMarkingPoint() : SERCMarkingPoint::find($markingPoint['id']);
                $mp->name = $markingPoint['description'];
                $mp->weight = $markingPoint['weight'];
                $mp->judge = $objective->id;
                $mp->serc = $serc->id;
                $mp->marking_point_template_id = $markingPoint['template_id'] ?? null;
                $mp->save();
            }
        }

        if ($affectedMarks > 0) {
            $serc->recordActivity('SERC_LEAGUE_OBJECTIVES_CHANGED', "League Objectives changed for '{$serc->getName()}', affecting {$affectedMarks} " . ($affectedMarks == 1 ? 'mark' : 'marks'), context: ['affected_marks' => $affectedMarks], related: [$serc, $comp]);
        }
    }

    /**
     * How many Marks would be marked against a different Objective after this save: all of the
     * SERC's Marks when League Objectives is switched on or off, otherwise the Marks on each
     * Objective whose Leagues change while it's on.
     */
    private function marksAffectedByLeagueObjectiveChanges(SERC $serc, array $validated): int
    {
        if (!$serc->exists) {
            return 0;
        }

        $wasOn = (bool) $serc->use_restricted_judges;
        $isOn = array_key_exists('use_restricted_judges', $validated) ? (bool) $validated['use_restricted_judges'] : $wasOn;

        if ($wasOn != $isOn) {
            return SERCResult::whereIn('marking_point', $serc->getMarkingPoints()->select('serc_marking_points.id'))->count();
        }

        if (!$isOn) {
            return 0;
        }

        $changedObjectives = collect($validated['judges'])
            ->filter(fn ($judge) => $judge['id'] != null && array_key_exists('restricted_leagues', $judge))
            ->filter(function ($judge) {
                $current = SERCJudge::find($judge['id'])->restrictedLeagues()->pluck('leagues.id')->sort()->values()->all();

                return $current != collect($judge['restricted_leagues'])->map(fn ($id) => (int) $id)->sort()->values()->all();
            })
            ->pluck('id');

        return SERCResult::whereIn('marking_point', SERCMarkingPoint::whereIn('judge', $changedObjectives)->select('id'))->count();
    }

    public function visibility(Competition $comp, SERC $serc, Request $request)
    {
        $serc->viewable = $request->validate(['viewable' => ['required', 'boolean']])['viewable'];
        $serc->save();

        return back()->with('success', $serc->viewable ? 'Shown in results' : 'Hidden from results');
    }

    public function saveScoring(Competition $comp, SERC $serc, UpdateScoringSettings $request)
    {
        ScoringSchemas::save($serc, $request);

        return back()->with('success', 'Scoring saved');
    }

    /**
     * Deleting needs the SERC's name typed out
     */
    public function destroy(Competition $comp, SERC $serc, Request $request)
    {
        $name = $serc->getName();

        if ($request->validate(['name' => ['required', 'string']])['name'] != $name) {
            throw ValidationException::withMessages(['name' => "That isn't the SERC's name."]);
        }

        $serc->delete();

        return redirect()->route('manage.competitions.events', $comp)->with('success', "{$name} deleted");
    }
}
