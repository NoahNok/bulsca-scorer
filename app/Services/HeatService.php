<?php

namespace App\Services;

use App\Exceptions\HeatException;
use App\Models\AbstractClasses\Entity;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\Orders\Heat;
use App\Models\Pool;
use App\Models\SpeedResult;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class HeatService
{
    /**
     * Lays out the Heats of every event that holds them, replacing any there are. All or nothing: an Entity
     * without a seed leaves every event's Heats as they were.
     *
     * @throws HeatException
     */
    public function generate(Competition $comp, ?CompetitionSpeedEvent $event = null): void
    {
        DB::transaction(function () use ($comp, $event) {
            foreach ($event ? [$event] : $comp->heatEvents() as $each) {
                $this->generateHeatsForEvent($each);
            }
        });

        $comp->clearHeatCache();
    }

    /**
     * Swaps two Entities' lanes
     */
    public function swapLanes(Heat $first, Heat $second): void
    {
        DB::transaction(function () use ($first, $second) {
            $from = ['heat' => $first->heat, 'lane' => $first->lane];
            $to = ['heat' => $second->heat, 'lane' => $second->lane];

            // Out of the way first, so the two never share a lane
            $first->update(['heat' => -1, 'lane' => -1]);
            $second->update($from);
            $first->update($to);
        });
    }

    /**
     * Swaps every lane of two Heats in a Pool
     */
    public function swapHeats(CompetitionSpeedEvent $event, ?Pool $pool, int $first, int $second): void
    {
        DB::transaction(function () use ($event, $pool, $first, $second) {
            $heats = fn () => $event->heats()->inPool($pool?->id);

            $heats()->where('heat', $first)->update(['heat' => -3]);
            $heats()->where('heat', $second)->update(['heat' => $first]);
            $heats()->where('heat', -3)->update(['heat' => $second]);
        });
    }

    /**
     * Puts an Entity in an open lane of a Pool, moving it from wherever it was
     */
    public function assign(CompetitionSpeedEvent $event, ?Pool $pool, Entity $entity, int $heat, int $lane): void
    {
        if ($pool && $lane > $pool->lanes) {
            throw ValidationException::withMessages(['heats' => "{$pool->name} only has {$pool->lanes} lanes."]);
        }

        DB::transaction(function () use ($event, $pool, $entity, $heat, $lane) {
            if ($event->heats()->where('heat', $heat)->where('lane', $lane)->inPool($pool?->id)->lockForUpdate()->exists()) {
                throw ValidationException::withMessages(['heats' => 'That lane is already taken.']);
            }

            // Moving it from wherever it was, which must be this Pool: heat numbers restart in each
            $current = $event->heats()->whereMorphedTo('entity', $entity)->get();
            if ($current->contains(fn (Heat $lane) => $lane->pool != $pool?->id)) {
                throw ValidationException::withMessages(['heats' => 'Entries can only move within their pool.']);
            }
            // One by one, so each lane's DQ submissions go with it
            $current->each->delete();
            $event->heats()->create([
                'entity_id' => $entity->id,
                'entity_type' => $entity->getMorphClass(),
                'heat' => $heat,
                'lane' => $lane,
                'pool' => $pool?->id,
            ]);
        });
    }

    /**
     * Opens an empty Heat at `position` in a Pool, moving that Heat and every later one up by one
     */
    public function insertHeat(CompetitionSpeedEvent $event, ?Pool $pool, int $position): void
    {
        $event->heats()->where('heat', '>=', $position)->inPool($pool?->id)->increment('heat');
    }

    /**
     * Removes a Heat from a Pool, unassigning its Entities and moving every later Heat down by one.
     * Returns how many Entities it unassigned.
     */
    public function deleteHeat(CompetitionSpeedEvent $event, ?Pool $pool, int $heat): int
    {
        return DB::transaction(function () use ($event, $pool, $heat) {
            // One by one, so each lane's DQ submissions go with it
            $lanes = $event->heats()->where('heat', $heat)->inPool($pool?->id)->get();
            $lanes->each->delete();
            $event->heats()->where('heat', '>', $heat)->inPool($pool?->id)->decrement('heat');

            return $lanes->count();
        });
    }

    public function generateHeatsForEvent(CompetitionSpeedEvent $event)
    {

        // Delete heats that exist
        $event->heats()->delete();

        // Get seeds
        $seeds = $event->seeds()->orderBy('seed', 'desc')->get();

        if (!$event->getCompetition->seed_per_event) {
            $seeds = $event->getCompetition->getSpeedEvents->first()->seeds()->orderBy('seed', 'desc')->get();
        }


        $comp = $event->getCompetition;

        $use_seeds = $comp->use_seeds;

        // Expect # seeds = # teams
        if ($use_seeds && $seeds->count() != $event->getScorableEntities()->count()) {
            throw new HeatException("# Seeds and # Entities do not match");
        }

        if (!$use_seeds) {
            $seeds = $event->getScorableEntities();
        }

        $max_lanes = $comp->max_lanes;
        $max_heats = ceil($seeds->count() / $max_lanes);

        $heats = [];

        for ($i = $max_heats; $i > 0; $i--) {
            $heatTeams = $seeds->pop($max_lanes); // Ordered slowest to fastest

            $orderedTeams = $this->heatMap($heatTeams->reverse()->all(), $max_lanes);
            $heats[$i] = $orderedTeams;
        }

        $heatsInsertable = [];

        $now = now();

        for ($i = $max_heats; $i > 0; $i--) {
            $heat = $heats[$i];
            foreach (array_keys($heat) as $l) {

                if ($use_seeds) {
                    $entity = $heat[$l]->entity;
                } else {
                    $entity = $heat[$l];
                }


                $d = [
                    'entity_id' => $entity->id,
                    'entity_type' => $entity->getMorphClass(),
                    'speed_event' => $event->id,
                    'heat' => $i,
                    'lane' => $l,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
                array_push($heatsInsertable, $d);
            }
        }


        $event->heats()->insert($heatsInsertable);
    }

    /**
     * The Pool whose Heats to show or edit: null without pools, the first when none is asked for, and a
     * 404 for one that isn't the competition's
     */
    public function pool(Competition $comp, ?int $id): ?Pool
    {
        if (!$comp->use_pools) {
            return null;
        }

        return $id ? $comp->getPools()->findOrFail($id) : $comp->getPools()->first();
    }

    /**
     * An event's Heats in one Pool (none without pools) as a lane × Heat grid: every Heat from 1 to the
     * highest, each with a cell per lane, null for an open one. Seeds are added when the competition
     * uses them.
     *
     * @return array{lanes: int, heats: array<int, array{number: int, lanes: array<int, ?array{id: int, entity: int, name: string, seed: ?string}>}>}
     */
    public function grid(Competition $comp, CompetitionSpeedEvent $event, ?Pool $pool): array
    {
        $laneCount = $pool?->lanes ?? $comp->max_lanes;
        $heats = $event->heats()->inPool($pool?->id)->with('entity')->get()->groupBy('heat');
        $seeds = $this->seeds($comp, $event);
        $highest = (int) $heats->keys()->max();

        return [
            'lanes' => $laneCount,
            'heats' => collect($highest > 0 ? range(1, $highest) : [])->map(function (int $number) use ($heats, $laneCount, $comp, $seeds) {
                $lanes = $heats->get($number, collect())->keyBy('lane');

                return [
                    'number' => $number,
                    'lanes' => collect(range(1, $laneCount))->map(function (int $lane) use ($lanes, $comp, $seeds) {
                        $allocation = $lanes->get($lane);

                        return $allocation ? [
                            'id' => $allocation->id,
                            'entity' => $allocation->entity_id,
                            'name' => $allocation->entity?->getName($comp) ?? 'No Entity',
                            'seed' => $seeds->get($allocation->entity_type . ':' . $allocation->entity_id),
                        ] : null;
                    })->all(),
                ];
            })->values()->all(),
        ];
    }

    /**
     * The Entities the event scores that aren't in any of its Heats, with their seeds
     *
     * @return array<int, array{id: int, name: string, seed: ?string}>
     */
    public function unassigned(Competition $comp, CompetitionSpeedEvent $event): array
    {
        $seeds = $this->seeds($comp, $event);
        $assigned = $event->heats()->pluck('entity_id');

        return $event->getScorableEntity()::where('competition', $comp->id)
            ->whereNotIn('id', $assigned)
            ->get()
            ->map(fn (Entity $entity) => [
                'id' => $entity->id,
                'name' => $entity->getName($comp),
                'seed' => $seeds->get($entity->getMorphClass() . ':' . $entity->id),
            ])
            ->values()
            ->all();
    }

    /**
     * Each Entity's seed for the event, keyed by `type:id`, as heats are generated from them: the event's
     * own with a seed per event, otherwise the first event's. Empty when the competition doesn't seed.
     */
    private function seeds(Competition $comp, CompetitionSpeedEvent $event): \Illuminate\Support\Collection
    {
        if (!$comp->use_seeds) {
            return collect();
        }

        $source = $comp->seed_per_event ? $event : $comp->getSpeedEvents()->orderBy('id')->first();

        return $source->seeds()->get()->mapWithKeys(fn ($seed) => [$seed->entity_type . ':' . $seed->entity_id => $seed->prettySeed()]);
    }

    private function heatMap(array $in, int $maxLanes): array
    {
        #           1,2,3,4,5,6,7,8
        $middleLane = ceil($maxLanes / 2);
        $offset = 1;

        $allocatedHeat = [];
        $allocatedHeat[$middleLane] = array_pop($in);

        $popped = null;
        while (($popped = array_pop($in)) != null) {
            $allocatedHeat[$middleLane + $offset] = $popped;

            if ($offset > 0) $offset = $offset * -1;
            else if ($offset < 0) $offset = ($offset * -1) + 1;
        }

        return $allocatedHeat;
    }

    /**
     * @param Collection<int, Heat> $heats
     */
    public function anyTimeAndOofConflicts(CompetitionSpeedEvent $event)
    {
        $heats = Heat::groupIntoHeats($event->getHeats()->with('oofs', 'entity', 'getPool')->orderBy('lane')->get())
            ->keyBy(fn($lanes) => Pool::heatLabel($lanes->first()->getPool, $lanes->first()->heat));

        $results = $event->results;

        // first lets map each heat into a [heat, lane, time, oof] array
        $heats = $heats->map(function ($heatGroup) use ($results, $event) {
            return $heatGroup->map(function (Heat $heat) use ($results, $event) {
                $result = $results->where('entity_id', $heat->entity_id)->where('entity_type', $heat->entity_type)->first();

                // A lane whose Entity has no result in this event (it scores other Entities) has no time to compare
                if (!$result) {
                    return null;
                }

                return [
                    'heat' => $heat->heat,
                    'lane' => $heat->lane,
                    // An entity can be in a heat without a result row for this event (e.g. imported entities)
                    'result' => (int) ($result?->result ?? -1),
                    'pretty_result' => $result ? SpeedResult::prettyTime($result->result) ?? 'N/A' : 'N/A',
                    'oof' => $heat->getOOF($event->id)?->oof ?? -1,
                    'entity' => $heat->entity
                ];
            })->filter(fn($item) => $item && $item['oof'] != -1);
        });

        $comparison = [];

        // want to check that the order of times and the order of oofs are the same in each heat
        foreach ($heats as $heatLabel => $heatGroup) {
            $times = $heatGroup->sortBy('result')->values();
            $oofs = $heatGroup->sortBy('oof')->values();

            // skips heats where all oof are -1
            if ($oofs->every(fn($item) => $item['oof'] == -1)) {
                continue;
            }

            $comparison_key = $heatLabel;

            // lets check they are the same length first
            if ($times->count() != $oofs->count()) {

                continue;
            }

            // now check the lanes match up
            $conflicts = [];
            for ($i = 0; $i < $times->count(); $i++) {
                if ($times[$i]['lane'] == $oofs[$i]['lane']) {
                    continue;
                }

                $offEquivalent = $oofs->firstWhere('lane', $times[$i]['lane']);


                $conflicts[] = [
                    'lane' => $times[$i]['lane'],
                    'entity_name' => $times[$i]['entity']->getName($event->getCompetition),
                    'time' => $times[$i]['pretty_result'],
                    'raw_time' => $times[$i]['result'],
                    'oof' => $offEquivalent['oof'],

                ];
            }

            if (count($conflicts) > 0) {
                // sort by raw_time
                usort($conflicts, function ($a, $b) {
                    return $a['raw_time'] <=> $b['raw_time'];
                });

                $comparison[$comparison_key] = $conflicts;
            }
        }

        return $comparison;
    }
}
