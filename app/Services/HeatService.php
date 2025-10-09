<?php

namespace App\Services;

use App\Models\AbstractClasses\Entity;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\Orders\Heat;
use Exception;

class HeatService
{

    public function generateHeatsForEvent(CompetitionSpeedEvent $event)
    {

        // Delete heats that exist
        $event->heats()->delete();

        // Get seeds
        $seeds = $event->seeds()->orderBy('seed', 'desc')->get();

        $comp = $event->getCompetition;

        $use_seeds = $comp->use_seeds;

        // Expect # seeds = # teams
        if ($use_seeds && $seeds->count() != $event->getScorableEntities()->count()) {
            throw new Exception("# Seeds and # Entities do not match");
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
}
