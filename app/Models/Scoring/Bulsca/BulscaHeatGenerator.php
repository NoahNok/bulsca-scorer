<?php


namespace App\Models\Scoring\Bulsca;

use App\Models\Competition;
use App\Models\Heat;
use App\Models\Scoring\IHeatGenerator;

class BulscaHeatGenerator implements IHeatGenerator
{

    public function generate(Competition $comp): void
    {
        $teams = $comp->getCompetitionTeams()->get()->sortByDesc('st_time');
        //dump($teams->pluck('st_time'));
        $heats = [];
        $maxHeats = ceil($teams->count() / $comp->max_lanes);




        // Creates the default heats based on swim tow times!
        for ($i = $maxHeats; $i > 0; $i--) {
            $heatTeams = $teams->pop($comp->max_lanes); // Ordered slowest to fastest


            $orderedTeams = $this->heatMap($heatTeams->reverse()->toArray(), $comp->max_lanes);

            $heats[$i] = $orderedTeams;
        }



        $databaseInsertable = [];

        for ($i = $maxHeats; $i > 0; $i--) {
            $heat = $heats[$i];
            foreach (array_keys($heat) as $l) {


                $d = ['competition' => $comp->id, 'team' => $heat[$l]['id'], 'heat' => $i, 'lane' => $l];;
                array_push($databaseInsertable, $d);
            }
        }


        Heat::where('competition', $comp->id)->delete();
        Heat::insert($databaseInsertable);
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
