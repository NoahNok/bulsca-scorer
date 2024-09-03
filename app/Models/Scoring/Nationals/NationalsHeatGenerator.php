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
            $lane = 1;
            foreach ($heat as $competitor) {
                $d = ['competition' => $comp->id, 'team' => $competitor->id, 'heat' => $heatNumber, 'lane' => $lane];
                $lane++;
                array_push($databaseInsertable, $d);
            }
            $heatNumber += 2;
        }

        $heatNumber = 2;
        foreach ($heats[2] as $heat) {
            $lane = 1;
            foreach ($heat as $competitor) {
                $d = ['competition' => $comp->id, 'team' => $competitor->id, 'heat' => $heatNumber, 'lane' => $lane];
                $lane++;
                array_push($databaseInsertable, $d);
            }
            $heatNumber += 2;
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

        }

        return $heats;
    }
}
