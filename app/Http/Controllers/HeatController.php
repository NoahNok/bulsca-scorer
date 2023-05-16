<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use Illuminate\Http\Request;

class HeatController extends Controller
{
    public function createDefaultHeatsForComp(Competition $comp, int $maxLanes)
    {
        $teams = $comp->getCompetitionTeams()->orderBy('st_time', 'desc')->get();

        $heats = [];
        $maxHeats = ceil($teams->count() / $maxLanes);


        // Creates the default heats based on swim tow times!
        for ($i = $maxHeats; $i > 0; $i--) {
            $heatTeams = $teams->pop($maxLanes); // Ordered slowest to fastest

            $orderedTeams = $this->heatMap($heatTeams->reverse()->toArray(), $maxLanes);

            $heats[$i] = $orderedTeams;
        }

        dump($heats);
        return;
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
