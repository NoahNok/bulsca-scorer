<?php


namespace App\Models\Scoring\Nationals;

use App\Models\Competition;
use App\Models\Heat;
use App\Models\Scoring\IHeatGenerator;

class NationalsHeatGenerator implements IHeatGenerator
{

    public function generate(Competition $comp): void
    {
        $brackets = $comp->getCompetitionTeams->sortBy('league')->groupBy('league');

        $heats = [];

        $pool = 1;
        foreach ($brackets as $bracket) {
            $heats = $this->generateBracket($bracket, $heats, $pool);
            $pool = ($pool == 1 ? 2 : 1);
        }





        $databaseInsertable = [];
        $heatNumber = 1;
        foreach ($heats[1] as $heat) {


            $heat = $this->heatMap($heat, 8);

            foreach ($heat as $lane => $competitor) {
                $d = ['competition' => $comp->id, 'team' => $competitor->id, 'heat' => $heatNumber, 'lane' => $lane];
                $lane++;
                array_push($databaseInsertable, $d);
            }
            $heatNumber += 2;
        }

        $heatNumber = 2;
        foreach ($heats[2] as $heat) {
            $heat = $this->heatMap($heat, 8);
            foreach ($heat as $lane => $competitor) {
                $d = ['competition' => $comp->id, 'team' => $competitor->id, 'heat' => $heatNumber, 'lane' => $lane];
                $lane++;
                array_push($databaseInsertable, $d);
            }
            $heatNumber += 2;
        }


        $heatValues = array_unique(array_column($databaseInsertable, 'heat'));
        rsort($heatValues);


        if ($heatValues[0] - 1 > $heatValues[1]) {
            $newFinalHeatNumber = $heatValues[0] - 1;;

            foreach ($databaseInsertable as &$insertable) {
                if ($insertable['heat'] == $heatValues[0]) {
                    $insertable['heat'] = $newFinalHeatNumber;
                }
            }
        }



        Heat::where('competition', $comp->id)->delete();
        Heat::insert($databaseInsertable);
    }


    private function generateBracket(object $bracket, $heats, $pool)
    {
        $clubs = $bracket->groupBy('club');

        if (count($clubs->first()) > 1) { // pair
            $even = [];
            $odd = [];

            foreach ($clubs->chunk(8) as $pairs) {
                $even[] = $pairs->map(fn($pair) => $pair[0]);
                $odd[] = $pairs->map(fn($pair) => $pair[1]);
            }

            $h = [...$even, ...$odd];

            $heats[$pool] = [...($heats[$pool] ?? []), ...$h];
        } else { // individual
            foreach ($clubs->chunk(8) as $individuals) {
                $individuals = $individuals->map(fn($indv) => $indv[0]);
                $heats[$pool] = [...($heats[$pool] ?? []), $individuals];
            }
        }

        return $heats;
    }


    private function heatMap($in, int $maxLanes): array
    {
        #           1,2,3,4,5,6,7,8
        $middleLane = ceil($maxLanes / 2);
        $offset = 1;

        $allocatedHeat = [];
        $allocatedHeat[$middleLane] = $in->pop();



        $popped = null;
        while (($popped = $in->pop()) != null) {
            $allocatedHeat[$middleLane + $offset] = $popped;

            if ($offset > 0) $offset = $offset * -1;
            else if ($offset < 0) $offset = ($offset * -1) + 1;
        }






        return $allocatedHeat;
    }
}
