<?php

namespace App\Http\Controllers\DigitalJudge\Event;

use App\DigitalJudge\DigitalJudge;
use App\Http\Controllers\Controller;
use App\Http\Requests\DigitalJudge\Event\StoreHeatOOFRequest;
use App\Http\Requests\DigitalJudge\Event\StoreHeatTimesRequest;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\EventOOF;
use App\Models\SpeedResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;



class EventJudgeController extends Controller
{
    public function selectTimeHeat(Competition $competition, CompetitionSpeedEvent $event)
    {

        $heats = [];

        foreach ($event->getHeats()->with('entity.speedResults')->get()->sortBy('heat')->groupBy('heat') as $heat => $lanes) {


            $missingResult = false;
            // Code that checks if each team has a reuslt for the event
            foreach ($lanes as $team) {
                $sr = $team->entity->speedResults->where('event', $event->id)->first();

                if ($sr == null || $sr->result == null) {
                    $missingResult = true;
                    break;
                }
            }

            $heats[] = [
                'heat' => $heat,
                'complete' => !$missingResult
            ];
        }

        return Inertia::render('Judge/Competition/Speed/Time/SelectHeat', [
            'competition' => $competition->only(['id', 'name']),
            'event' => $event->jsonable(),
            'heats' => $heats,

        ]);
    }

    public function markTime(Competition $competition, CompetitionSpeedEvent $event, int $heat)
    {

        $laneData = [];
        $existingTimes = []; // This will literally be the entity_id => value

        $event->getHeats()->where('heat', $heat)->orderBy('lane')->get()->each(function ($lane) use (&$existingTimes, &$laneData, $event) {
            $sr = SpeedResult::whereMorphedTo('entity', $lane->entity)->where('event', $event->id)->first();

            if ($sr != null && $sr->result != null) {
                $existingTimes[$lane->entity->id] = $sr->getJudgeFormattedResult();
            }

            $laneData[] = [
                'lane' => $lane->lane,
                'entity' => $lane->entity->jsonable()
            ];
        });

        // Only a referee can change Times, so a fully timed heat has nothing left for anyone else
        if (!DigitalJudge::isClientHeadJudge($competition) && count($laneData) > 0 && count($existingTimes) == count($laneData)) {
            Inertia::flash('toast', ['variant' => 'error', 'title' => 'All lanes have a time — ask a referee to change it.']);
            return to_route('judge.competition.event.time.select-heat', ['competition' => $competition->getSlug(), 'event' => $event]);
        }

        return Inertia::render('Judge/Competition/Speed/Time/MarkTime', [
            'competition' => $competition->only(['id', 'name']),
            'event' => $event->jsonable(),
            'heat' => ['heat' => $heat, 'complete' => false, 'lanes' => $laneData],
            'existingTimes' => $existingTimes
        ]);
    }

    public function storeTime(Competition $competition, CompetitionSpeedEvent $event, int $heat, StoreHeatTimesRequest $request)
    {
        $data = $request->validated();

        $marks = $data['mark']; // this is a key value array of entity_id -> time

        // Check every entity is in this heat and has a result row before writing anything, so a bad
        // or stale id can't fail partway through and leave the heat half saved
        $lanes = $event->getHeats()->where('heat', $heat)->with('entity')->get()->keyBy('entity_id');
        $isReferee = DigitalJudge::isClientHeadJudge($competition);
        $results = [];

        foreach ($marks as $entity_id => $value) {
            $entity = $lanes->get($entity_id)?->entity;
            $sr = $entity ? SpeedResult::whereMorphedTo('entity', $entity)->where('event', $event->id)->first() : null;

            abort_if($sr == null, 422, 'That team isn\'t in this heat.');

            // The page sends existing Times back as it showed them, so only a different value is a change
            abort_if(
                !$isReferee && $sr->result != null && $value !== (string) $sr->getJudgeFormattedResult(),
                409,
                'That lane already has a time — ask a referee to change it.'
            );

            $results[$entity_id] = $sr;
        }

        DB::transaction(fn() => $this->saveTimes($competition, $event, $heat, $marks, $results));

        return response()->json(['hasNextHeat' => $heat < $event->getMaxHeats()]);
    }

    /**
     * @param array<int, string> $marks entity_id => time entered
     * @param array<int, SpeedResult> $results entity_id => its result for this event
     */
    private function saveTimes(Competition $competition, CompetitionSpeedEvent $event, int $heat, array $marks, array $results): void
    {
        foreach ($marks as $entity_id => $value) {
            $sr = $results[$entity_id];

            $from = $sr->getHistoryTime();

            if (!$this->applyTime($event, $sr, $value)) continue;

            $to = $sr->getHistoryTime();

            if ($from === $to) continue;

            $sr->recordActivity('SPEED_RESULT_MARKED', "{$sr->entity->getName($competition)}: {$from} → {$to}", context: [
                'heat' => $heat,
                'changes' => ['result' => ['old' => $from, 'new' => $to]],
            ], related: [$event, $sr->entity, $competition]);
        }
    }

    /**
     * Saves a time, or a DNS, DNF or OOT entered in its place. False when the value can't be read.
     */
    private function applyTime(CompetitionSpeedEvent $event, SpeedResult $sr, string $value): bool
    {
        $entity = $sr->entity;

        if (str_starts_with($value, "DN")) {
            $event->clearEntityDisqualifications($entity);
            $code = str_starts_with($value, 'DNS') ? 99904 : 99915;
            $event->addEntityDisqualification($sr->entity, $code);
            $sr->result = 3599000;
            $sr->save();
            return true;
        }

        if (str_starts_with($value, "OOT")) {
            $event->clearEntityDisqualifications($entity);
            $event->addEntityDisqualification($sr->entity, 99901);
            $sr->result = 3599000;
            $sr->save();
            return true;
        }

        $minSecSplit = explode(":", $value);

        if ($event->getName() == "Rope Throw" && count($minSecSplit) == 1) {
            $sr->result = $minSecSplit[0];
        } else {

            if (count($minSecSplit) != 2) {
                return false;
            }

            $min = $minSecSplit[0];
            $secMillisSplit = explode(".", $minSecSplit[1]);
            if (count($secMillisSplit) != 2) {
                return false;
            }

            # Judge side no uses centiseconds, so multiply by 10 for millis
            if (strlen($secMillisSplit[1]) == 2) {
                $secMillisSplit[1] = $secMillisSplit[1] * 10;
            }

            $totalMillis = $min * 60000 + $secMillisSplit[0] * 1000 + $secMillisSplit[1];


            $sr->result = $totalMillis;
        }

        $sr->save();

        return true;
    }

    // ORDER OF FINISH
    public function selectOOFHeat(Competition $competition, CompetitionSpeedEvent $event)
    {

        $heats = [];

        $raw = $event->getHeats()->with([
            'oofs' => fn($q) => $q->where('event', $event->id)
        ])->orderBy('heat')->orderBy('lane')->get()->groupBy('heat');

        foreach (
            $raw as $heat => $lanes
        ) {


            $hasResult = $lanes->contains(
                fn($lane) =>
                $lane->oofs->first()?->oof !== null
            );

            $heats[] = [
                'heat' => $heat,
                'complete' => $hasResult
            ];
        }

        return Inertia::render('Judge/Competition/Speed/OOF/SelectHeat', [
            'competition' => $competition->only(['id', 'name']),
            'event' => $event->jsonable(),
            'heats' => $heats,

        ]);
    }

    /**
     * Whether the heat's Places have been saved. They're saved together, so once any lane has one
     * only a referee can change them.
     */
    private function hasPlaces($lanes): bool
    {
        return $lanes->contains(fn($lane) => $lane->oofs->first()?->oof !== null);
    }

    public function markOOF(Competition $competition, CompetitionSpeedEvent $event, int $heat)
    {
        $lanes = $event->getHeats()->with(['oofs' => fn($q) => $q->where('event', $event->id)])->where('heat', $heat)->orderBy('lane')->get();

        if (!DigitalJudge::isClientHeadJudge($competition) && $this->hasPlaces($lanes)) {
            Inertia::flash('toast', ['variant' => 'error', 'title' => 'This heat has places — ask a referee to change them.']);
            return to_route('judge.competition.event.oof.select-heat', ['competition' => $competition->getSlug(), 'event' => $event]);
        }

        return Inertia::render('Judge/Competition/Speed/OOF/MarkOOF', [
            'competition' => $competition->only(['id', 'name']),
            'event' => $event->jsonable(),
            'heat' => ['heat' => $heat, 'complete' => false, 'lanes' => $lanes->map(function ($lane) {

                return [
                    'lane' => $lane->lane,
                    'entity' => $lane->entity->jsonable(),
                    'oof' => $lane->oofs->first()?->oof
                ];
            })]
        ]);
    }

    public function storeOOF(Competition $competition, CompetitionSpeedEvent $event, int $heat, StoreHeatOOFRequest $request)
    {
        $data = $request->validated();

        // Check every entity is in this heat before writing anything, so a bad or stale id
        // can't fail partway through and leave the heat half saved
        $lanes = $event->getHeats()->where('heat', $heat)->with(['entity', 'oofs' => fn($q) => $q->where('event', $event->id)])->get()->keyBy('entity_id');

        foreach ($data as $res) {
            abort_if($lanes->get($res['entity']['id'])?->entity == null, 422, 'That team isn\'t in this heat.');
        }

        abort_if(
            !DigitalJudge::isClientHeadJudge($competition) && $this->hasPlaces($lanes),
            409,
            'This heat has places — ask a referee to change them.'
        );

        DB::transaction(fn() => $this->savePlaces($competition, $event, $heat, $data, $lanes));

        return response()->json(['hasNextHeat' => $heat < $event->getMaxHeats()]);
    }

    /**
     * @param array<int, array{entity: array{id: int}, oof?: ?int}> $data the lanes as sent
     * @param \Illuminate\Support\Collection $lanes this heat's lanes, keyed by entity id
     */
    private function savePlaces(Competition $competition, CompetitionSpeedEvent $event, int $heat, array $data, $lanes): void
    {
        $changes = [];
        $changedEntities = [];

        foreach ($data as $res) {
            $heatlane = $lanes->get($res['entity']['id']);
            $entity = $heatlane->entity;

            $eOof = EventOOF::firstOrNew(['heat_lane' => $heatlane->id, 'event' => $event->id]);
            $was = $eOof->oof;

            // A lane sent without a place has been cleared, so drop any place
            // saved previously rather than keeping it alongside the new ones
            if (!isset($res['oof']) || !is_numeric($res['oof'])) {
                EventOOF::where(['heat_lane' => $heatlane->id, 'event' => $event->id])->delete();
                $now = null;
            } else {
                $eOof->oof = $res['oof'];
                $eOof->save();
                $now = $eOof->oof;
            }

            if ($was != $now) {
                $changes[] = ['entity' => $entity->id, 'name' => $entity->getName($competition), 'old' => $was ?? '—', 'new' => $now ?? '—'];
                $changedEntities[] = $entity;
            }
        }

        if (count($changes) > 0) {
            $event->recordActivity('OOF_MARKED', "Heat {$heat}: " . count($changes) . (count($changes) > 1 ? ' places changed' : ' place changed'), context: [
                'heat' => $heat,
                'changes' => $changes,
            ], related: [$event, ...$changedEntities, $competition]);
        }
    }
}
