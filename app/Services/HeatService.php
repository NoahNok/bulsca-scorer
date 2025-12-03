<?php

namespace App\Services;

use App\Exceptions\HeatException;
use App\Models\AbstractClasses\Entity;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\Orders\Heat;
use App\Models\SpeedResult;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class HeatService
{

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
        $heats = $event->getHeats()->with('oofs', 'entity')->orderBy('heat')->orderBy('lane')->get()->groupBy('heat');

        $results = $event->results;

        // first lets map each heat into a [heat, lane, time, oof] array
        $heats = $heats->map(function ($heatGroup) use ($results, $event) {
            return $heatGroup->map(function (Heat $heat) use ($results, $event) {
                $result = $results->where('entity_id', $heat->entity_id)->where('entity_type', $heat->entity_type)->first();

                return [
                    'heat' => $heat->heat,
                    'lane' => $heat->lane,
                    'result' => (int) $result?->result ?? -1,
                    'pretty_result' => SpeedResult::prettyTime($result->result) ?? 'N/A',
                    'oof' => $heat->getOOF($event->id)?->oof ?? -1,
                    'entity' => $heat->entity
                ];
            })->filter(fn($item) => $item['oof'] != -1);
        });

        $comparison = [];

        // want to check that the order of times and the order of oofs are the same in each heat
        foreach ($heats as $heatNumber => $heatGroup) {
            $times = $heatGroup->sortBy('result')->values();
            $oofs = $heatGroup->sortBy('oof')->values();

            // skips heats where all oof are -1
            if ($oofs->every(fn($item) => $item['oof'] == -1)) {
                continue;
            }

            $comparison_key = $heatNumber;

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
