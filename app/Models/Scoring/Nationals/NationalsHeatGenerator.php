<?php


namespace App\Models\Scoring\Nationals;

use App\Models\Competition;
use App\Models\Heat;
use App\Models\League;
use App\Models\Scoring\IHeatGenerator;

class NationalsHeatGenerator implements IHeatGenerator
{

    public function generate(Competition $comp): void
    {

        $pool1Brackets = collect(['Junior Girls Pairs', 'Junior Open Pairs', 'Masters Ladies 50-59', 'Masters Open 50-59', 'Masters Ladies 60-69', 'Masters Open 60-69', 'Masters Ladies 70+', 'Masters Open 70+']); // junior open, junior girls, masters both 50, masters both 60, masters both 70
        $pool2Brackets = collect(['Senior Girls Pairs', 'Senior Open Pairs', 'Adult Ladies Pairs', 'Adult Open Pairs', 'Ladies', 'Open', 'Masters Ladies 30-39', 'Masters Open 30-39', 'Masters Ladies 40-49', 'Masters Open 40-49']); // senior both, adult both, ladies, open, master both 30, master both 40



        $pool1Brackets = $pool1Brackets->map(fn($name) => League::where('name', $name)->first());
        $pool2Brackets = $pool2Brackets->map(fn($name) => League::where('name', $name)->first());


        $brackets = [$pool1Brackets, $pool2Brackets];

        dump($brackets);

        Heat::where('competition', $comp->id)->delete();

        foreach ($comp->getSpeedEvents as $cse) {

            if ($cse->getName() == "Rope Throw") {
                $this->generateRopethrow($comp, $brackets, $cse);
            } else {
                $this->generateSwimAndTow($comp, $brackets, $cse);
            }
        }
    }

    private function generateRopethrow($comp, $brackets, $cse)
    {

        $insertable = [];

        $heatNumber = 1;
        foreach ($brackets[0] as $pool1Bracket) {
            $data = $this->generateBracket($pool1Bracket, $comp);
            if (!$data) continue;
            $isPair = $data['pair'];
            $heats = $data['heats'];


            foreach ($heats as $heat) {

                $heat = $this->heatMap($heat, 8);

                foreach ($heat as $lane => $competitor) {
                    $insertable[] = ['competition' => $comp->id, 'team' => $competitor->id, 'heat' => $heatNumber, 'lane' => $lane, 'event' => $cse->id];
                }

                $heatNumber += 2;
            }
        }

        $heatNumber = 2;
        foreach ($brackets[1] as $pool2Bracket) {
            $data = $this->generateBracket($pool2Bracket, $comp);
            if (!$data) continue;
            $isPair = $data['pair'];
            $heats = $data['heats'];


            foreach ($heats as $heat) {

                $heat = $this->heatMap($heat, 8);

                foreach ($heat as $lane => $competitor) {
                    $insertable[] = ['competition' => $comp->id, 'team' => $competitor->id, 'heat' => $heatNumber, 'lane' => $lane, 'event' => $cse->id];
                }

                $heatNumber += 2;
            }
        }



        Heat::insert($insertable);
    }

    private function generateSwimAndTow($comp, $brackets, $cse)
    {
        $insertable = [];

        $heatNumber = 1;
        foreach ($brackets[0] as $pool1Bracket) {
            $data = $this->generateBracket($pool1Bracket, $comp);
            if (!$data) continue;
            $isPair = $data['pair'];
            $heats = $data['heats'];



            if ($isPair) {

                $tmp = $heats[1];
                $heats[1] = $heats[2];
                $heats[2] = $tmp;

                foreach ($heats as $heat) {

                    $heat = $this->heatMap($heat, 8);

                    foreach ($heat as $lane => $competitor) {
                        $insertable[] = ['competition' => $comp->id, 'team' => $competitor->id, 'heat' => $heatNumber, 'lane' => $lane, 'event' => $cse->id];
                    }

                    $heatNumber += 2;
                }
            } else {

                foreach ($heats as $heat) {

                    $heat = $this->heatMap($heat, 8);

                    foreach ($heat as $lane => $competitor) {
                        $insertable[] = ['competition' => $comp->id, 'team' => $competitor->id, 'heat' => $heatNumber, 'lane' => $lane, 'event' => $cse->id];
                    }

                    $heatNumber += 2;
                }
            }
        }

        $heatNumber = 2;
        foreach ($brackets[1] as $pool2Bracket) {
            $data = $this->generateBracket($pool2Bracket, $comp);
            if (!$data) continue;
            $isPair = $data['pair'];
            $heats = $data['heats'];


            if ($isPair) {

                if (count($heats) > 2) {
                    $tmp = $heats[1];
                    $heats[1] = $heats[2];
                    $heats[2] = $tmp;
                }




                foreach ($heats as $heat) {

                    $heat = $this->heatMap($heat, 8);

                    foreach ($heat as $lane => $competitor) {
                        $insertable[] = ['competition' => $comp->id, 'team' => $competitor->id, 'heat' => $heatNumber, 'lane' => $lane, 'event' => $cse->id];
                    }

                    $heatNumber += 2;
                }
            } else {

                foreach ($heats as $heat) {

                    $heat = $this->heatMap($heat, 8);

                    foreach ($heat as $lane => $competitor) {
                        $insertable[] = ['competition' => $comp->id, 'team' => $competitor->id, 'heat' => $heatNumber, 'lane' => $lane, 'event' => $cse->id];
                    }

                    $heatNumber += 2;
                }
            }
        }



        Heat::insert($insertable);
    }


    private function generateBracket(object $bracket, $comp)
    {

        $isPair = false;
        $heats = [];


        $bracket = $comp->getCompetitionTeams()->where('league', $bracket->id)->get();
        if (count($bracket) == 0) return null;


        $clubs = $bracket->groupBy('club');

        if (count($clubs->first()) > 1) { // pair - at most 4 heats
            $isPair = true;
            foreach ($clubs->chunk(8) as $pairs) {

                $firstOfPairs = $pairs->map(fn($pair) => $pair[0]);
                $secondOfPairs = $pairs->map(fn($pair) => $pair[1]);

                $heats = [...$heats, $firstOfPairs, $secondOfPairs];
            }
        } else { // individual - at most 2 heats
            foreach ($clubs->chunk(8) as $individuals) {
                $individuals = $individuals->map(fn($indv) => $indv[0]);
                $heats = [...$heats, $individuals];
            }
        }

        return ['pair' => $isPair, 'heats' => $heats];
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
