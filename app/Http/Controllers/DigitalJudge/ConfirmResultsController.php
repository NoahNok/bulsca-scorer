<?php

namespace App\Http\Controllers\DigitalJudge;

use App\Http\Controllers\Controller;
use App\Http\Requests\DigitalJudge\ConfirmResultsRequest;
use App\Models\AbstractClasses\Event;
use App\Models\AbstractClasses\Violation;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\DigitalJudge\JudgeNote;
use App\Models\DigitalJudge\Violation\ViolationSubmission;
use App\Models\DQCode;
use App\Models\Event\Disqualification;
use App\Models\PenaltyCode;
use App\Models\SERC;
use App\Models\SERCResult;
use App\Models\SpeedResult;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Inertia\Inertia;

class ConfirmResultsController extends Controller
{
    // Submissions the head ref still has to deal with, so aren't in the results yet.
    // APPEALED is final: the appeal succeeded and the code was removed from the entity.
    private const PENDING_STATES = ['SUBMITTED'];

    // DNF, DNS and OOT are entered as the result itself, so show them in place of the time
    private const RESULT_DQ_CODES = [99915, 99904, 99901];

    // Per request cache of DQ/penalty code descriptions, keyed "DQ12" / "P3"
    private array $codeMessages = [];

    public function serc(Competition $competition, SERC $serc)
    {
        if ($serc->digitalJudgeConfirmed) {
            return $this->alreadyConfirmed($competition, $serc);
        }

        $judges = $serc->getJudges()->with('getMarkingPoints')->get();
        $markingPoints = $judges->flatMap->getMarkingPoints;

        return Inertia::render('Judge/Competition/ConfirmResults/SERC', [
            'competition' => $competition->only(['id', 'name']),
            'event' => $serc->jsonable(),
            'useTanks' => (bool) $competition->getScoringSettings?->use_tanks,
            'judges' => $judges->map(fn($judge) => [
                'id' => $judge->id,
                'name' => $judge->name,
                'markingPoints' => $judge->getMarkingPoints->map(fn($mp) => [
                    'id' => $mp->id,
                    'name' => $mp->name,
                    'weight' => $mp->weight,
                ])->values(),
            ])->values(),
            'summary' => fn() => $this->sercSummary($serc, $markingPoints->pluck('id')),
            'entities' => Inertia::scroll(fn() => $this->sercEntities($competition, $serc, $judges)),
        ]);
    }

    public function storeSerc(Competition $competition, SERC $serc, ConfirmResultsRequest $request)
    {
        return $this->confirm($serc);
    }

    public function event(Competition $competition, CompetitionSpeedEvent $event)
    {
        if ($event->digitalJudgeConfirmed) {
            return $this->alreadyConfirmed($competition, $event);
        }

        return Inertia::render('Judge/Competition/ConfirmResults/Event', [
            'competition' => $competition->only(['id', 'name']),
            'event' => $event->jsonable(),
            'isRopeThrow' => $event->getName() == 'Rope Throw',
            'summary' => fn() => $this->eventSummary($event),
            'heats' => Inertia::scroll(fn() => $this->eventHeats($competition, $event)),
        ]);
    }

    public function storeEvent(Competition $competition, CompetitionSpeedEvent $event, ConfirmResultsRequest $request)
    {
        return $this->confirm($event);
    }

    private function confirm(Event $event)
    {
        if ($event->digitalJudgeConfirmed) {
            return response()->json(['message' => "{$event->getName()} has already been confirmed."], 409);
        }

        $event->digitalJudgeConfirmed = true;
        $event->save();

        return response()->json(['confirmed' => true]);
    }

    private function alreadyConfirmed(Competition $competition, Event $event)
    {
        Inertia::flash('toast', ['variant' => 'success', 'title' => "{$event->getName()} results are already confirmed"]);

        return to_route('judge.competition', $competition);
    }

    // SERC

    private function sercSummary(SERC $serc, Collection $markingPointIds): array
    {
        $entities = $serc->getDraw()->count();

        // Entities with a mark for every marking point
        $complete = SERCResult::query()->without('getSerc')
            ->whereIn('marking_point', $markingPointIds)
            ->whereNotNull('result')
            ->groupBy('entity_type', 'entity_id')
            ->havingRaw('COUNT(*) >= ?', [$markingPointIds->count()])
            ->select('entity_type', 'entity_id')
            ->get()
            ->count();

        return [
            'entities' => $entities,
            'missing' => $markingPointIds->isEmpty() ? 0 : max($entities - $complete, 0),
            'pending' => $this->pendingSubmissionsQuery($serc)->count(),
        ];
    }

    private function sercEntities(Competition $competition, SERC $serc, Collection $judges)
    {
        $draws = $serc->getDraw()->reorder()->orderBy('tank')->orderBy('draw')->paginate(8);

        $entities = $draws->getCollection()->pluck('entity')->filter();
        $markingPoints = $judges->flatMap->getMarkingPoints;

        $results = SERCResult::query()->without('getSerc')
            ->whereIn('marking_point', $markingPoints->pluck('id'))
            ->where(fn($q) => $this->whereEntities($q, $entities))
            ->get(['marking_point', 'entity_type', 'entity_id', 'result'])
            ->groupBy(fn($r) => $this->entityKey($r->entity_type, $r->entity_id));

        $notes = JudgeNote::whereIn('judge', $judges->pluck('id'))
            ->where(fn($q) => $this->whereEntities($q, $entities))
            ->get()
            ->groupBy(fn($n) => $this->entityKey($n->entity_type, $n->entity_id));

        $violations = $this->violationsFor($competition, $serc, $entities);

        return $draws->through(function ($draw) use ($judges, $results, $notes, $violations) {
            $entity = $draw->entity;
            $key = $entity ? $this->entityKey($entity->getMorphClass(), $entity->id) : null;
            $entityResults = $results->get($key, collect())->keyBy('marking_point');

            $marks = [];
            $judgeTotals = [];
            $missing = 0;

            foreach ($judges as $judge) {
                $judgeTotals[$judge->id] = 0;

                foreach ($judge->getMarkingPoints as $mp) {
                    $mark = $entityResults->get($mp->id)?->result;
                    $mark = $mark === null ? null : (float) $mark;
                    $marks[$mp->id] = $mark;

                    if ($mark === null) {
                        $missing++;
                        continue;
                    }

                    $judgeTotals[$judge->id] += $mark * $mp->weight;
                }
            }

            return [
                'tank' => $draw->tank,
                'draw' => $draw->draw,
                'entity' => $entity?->jsonable(),
                'marks' => (object) $marks,
                'judgeTotals' => (object) $judgeTotals,
                'total' => array_sum($judgeTotals),
                'missing' => $missing,
                'notes' => $notes->get($key, collect())
                    ->filter(fn($n) => filled($n->note))
                    ->map(fn($n) => [
                        'judge' => $judges->firstWhere('id', $n->judge)?->name,
                        'note' => $n->note,
                    ])->values(),
                ...$violations($key),
            ];
        });
    }

    // Speed events

    private function eventSummary(CompetitionSpeedEvent $event): array
    {
        $lanes = $event->getHeats()->count();
        $heats = $event->getHeats()->distinct()->count('heat');

        $heatsWithOof = $event->getHeats()
            ->whereHas('oofs', fn($q) => $q->where('event', $event->id)->whereNotNull('oof'))
            ->distinct()
            ->count('heat');

        $withResult = SpeedResult::where('event', $event->id)->whereNotNull('result')->count();

        return [
            'entities' => $lanes,
            'missing' => max($lanes - $withResult, 0),
            'missingOof' => max($heats - $heatsWithOof, 0),
            'pending' => $this->pendingSubmissionsQuery($event)->count(),
        ];
    }

    private function eventHeats(Competition $competition, CompetitionSpeedEvent $event)
    {
        // Grouped rather than distinct so the paginator counts heats, not lanes
        $heats = $event->getHeats()->select('heat')->groupBy('heat')->orderBy('heat')->paginate(4);

        $lanes = $event->getHeats()
            ->whereIn('heat', $heats->getCollection()->pluck('heat'))
            ->with(['entity', 'oofs' => fn($q) => $q->where('event', $event->id)])
            ->orderBy('heat')
            ->orderBy('lane')
            ->get();

        $entities = $lanes->pluck('entity')->filter();

        $results = SpeedResult::where('event', $event->id)
            ->where(fn($q) => $this->whereEntities($q, $entities))
            ->get(['entity_type', 'entity_id', 'result'])
            ->keyBy(fn($r) => $this->entityKey($r->entity_type, $r->entity_id));

        $violations = $this->violationsFor($competition, $event, $entities);

        $isRopeThrow = $event->getName() == 'Rope Throw';
        $maxLanes = $competition->max_lanes;
        $lanesByHeat = $lanes->groupBy('heat');

        return $heats->through(function ($row) use ($lanesByHeat, $results, $violations, $isRopeThrow, $maxLanes) {
            $heatLanes = $lanesByHeat->get($row->heat, collect())->keyBy('lane');
            $laneCount = max($maxLanes, $heatLanes->keys()->max() ?? 0);

            $laneData = [];

            for ($l = 1; $l <= $laneCount; $l++) {
                $lane = $heatLanes->get($l);
                $entity = $lane?->entity;

                if (!$entity) {
                    $laneData[] = ['lane' => $l, 'entity' => null];
                    continue;
                }

                $key = $this->entityKey($entity->getMorphClass(), $entity->id);
                $laneViolations = $violations($key);

                // DNF/DNS/OOT replace the time, so pull them out of the DQ list
                $resultDq = collect($laneViolations['dqs'])->first(fn($dq) => in_array($dq['code'], self::RESULT_DQ_CODES));
                $laneViolations['dqs'] = collect($laneViolations['dqs'])->reject(fn($dq) => $dq === $resultDq)->values();

                $laneData[] = [
                    'lane' => $l,
                    'entity' => $entity->jsonable(),
                    'oof' => $lane->oofs->first()?->oof,
                    'result' => $resultDq
                        ? $resultDq['label']
                        : SpeedResult::confirmFormattedResult($results->get($key)?->result, $isRopeThrow),
                    'resultIsDq' => (bool) $resultDq,
                    ...$laneViolations,
                ];
            }

            return [
                'heat' => $row->heat,
                'lanes' => $laneData,
            ];
        });
    }

    // Shared

    /**
     * Load the applied DQs/penalties and pending submissions for a page of entities in
     * one go, returning a lookup from entity key to that entity's violations.
     */
    private function violationsFor(Competition $competition, Event $event, Collection $entities): \Closure
    {
        $organisation = $competition->getOrganisation;

        $describe = fn(Violation $v) => [
            'code' => $v->code,
            'label' => (string) $v,
            'message' => $this->codeMessage($v, $organisation),
        ];

        $dqs = $event->disqualifications()
            ->where(fn($q) => $this->whereEntities($q, $entities))
            ->get()
            ->groupBy(fn($v) => $this->entityKey($v->entity_type, $v->entity_id));

        $penalties = $event->penalties()
            ->where(fn($q) => $this->whereEntities($q, $entities))
            ->get()
            ->groupBy(fn($v) => $this->entityKey($v->entity_type, $v->entity_id));

        $pending = $this->pendingSubmissionsQuery($event)
            ->where(fn($q) => $this->whereEntities($q, $entities))
            ->with('submitted')
            ->get()
            ->groupBy(fn($s) => $this->entityKey($s->entity_type, $s->entity_id));

        return fn(?string $key) => [
            'dqs' => $dqs->get($key, collect())->map($describe)->values(),
            'penalties' => $penalties->get($key, collect())->map($describe)->values(),
            'pending' => $pending->get($key, collect())->map(fn($s) => [
                'id' => $s->id,
                'label' => $s->code(),
                'status' => $s->status,
                'message' => $s->submitted?->description,
            ])->values(),
        ];
    }

    private function codeMessage(Violation $violation, $organisation): string
    {
        // DNF/DNS/OOT messages are fixed, so don't need a lookup
        if ($violation instanceof Disqualification && in_array($violation->code, self::RESULT_DQ_CODES)) {
            return $violation->getMessage();
        }

        $label = (string) $violation;

        return $this->codeMessages[$label] ??= $violation instanceof Disqualification
            ? DQCode::message($violation->code, $organisation)
            : PenaltyCode::message($violation->code, $organisation);
    }

    private function pendingSubmissionsQuery(Event $event): Builder
    {
        return ViolationSubmission::whereMorphedTo('event', $event)->whereIn('status', self::PENDING_STATES);
    }

    /**
     * Constrain a query with an `entity` morph to the given entities.
     */
    private function whereEntities(Builder|Relation $query, Collection $entities): void
    {
        if ($entities->isEmpty()) {
            $query->whereRaw('1 = 0');
            return;
        }

        foreach ($entities->groupBy(fn($e) => $e->getMorphClass()) as $type => $group) {
            $query->orWhere(fn($q) => $q->where('entity_type', $type)->whereIn('entity_id', $group->pluck('id')));
        }
    }

    private function entityKey(string $type, int|string $id): string
    {
        return "{$type}:{$id}";
    }
}
