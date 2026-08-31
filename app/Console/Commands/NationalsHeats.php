<?php

namespace App\Console\Commands;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\League;
use App\Models\Orders\Heat;
use Illuminate\Console\Command;

class NationalsHeats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'heats-and-draws:nationals-heats {competition_id} {--confirm : Confirm removing existing heats}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $compId = $this->argument('competition_id');

        $comp = Competition::find(($compId));

        if (!$comp) {
            $this->error('No competition found with that id');
            return;
        }

        $this->info("Using: {$comp->name}");

        if ($this->option('confirm')) {
            $continue = true;
        } elseif (app()->runningInConsole()) {
            $continue = $this->confirm("Continuing will remove any existing heats");
        } else {
            $this->error('Pass --confirm when running this command from the web console.');
            return Command::FAILURE;
        }

        if (!$continue) {
            $this->error('aborting');
            return;
        }


        $this->generate($comp);
    }

    public function generate(Competition $comp): void
    {

        $pool1Brackets = collect(['Junior Girls Pairs', 'Junior Open Pairs', 'Masters Ladies 50-59', 'Masters Open 50-59', 'Masters Ladies 60-69', 'Masters Open 60-69', 'Masters Ladies 70+', 'Masters Open 70+']); // junior open, junior girls, masters both 50, masters both 60, masters both 70
        $pool2Brackets = collect(['Senior Girls Pairs', 'Senior Open Pairs', 'Adult Ladies Pairs', 'Adult Open Pairs', 'Ladies', 'Open', 'Masters Ladies 30-39', 'Masters Open 30-39', 'Masters Ladies 40-49', 'Masters Open 40-49']); // senior both, adult both, ladies, open, master both 30, master both 40



        $pool1Brackets = $pool1Brackets->map(fn($name) => League::where('name', $name)->where('competition', $comp->id)->first());
        $pool2Brackets = $pool2Brackets->map(fn($name) => League::where('name', $name)->where('competition', $comp->id)->first());


        $brackets = [$pool1Brackets, $pool2Brackets];




        foreach ($comp->getSpeedEvents as $cse) {

            $cse->heats()->delete();
            $this->generateRopethrow($comp, $brackets, $cse);
        }
    }

    private function generateRopethrow($comp, $brackets, $cse)
    {

        $insertable = [];

        $heatNumber = 1;
        foreach ($brackets[0] as $pool1Bracket) {
            $data = $this->generateBracket($pool1Bracket, $cse);
            if (!$data) continue;
            $isPair = $data['pair'];
            $heats = $data['heats'];


            foreach ($heats as $heat) {

                $heat = $this->heatMap($heat, 8);

                foreach ($heat as $lane => $competitor) {
                    $insertable[] = ['entity_id' => $competitor->id, 'entity_type' => 'competitor', 'heat' => $heatNumber, 'lane' => $lane, 'speed_event' => $cse->id];
                }

                $heatNumber += 2;
            }
        }

        $heatNumber = 2;
        foreach ($brackets[1] as $pool2Bracket) {
            $data = $this->generateBracket($pool2Bracket, $cse);
            if (!$data) continue;
            $isPair = $data['pair'];
            $heats = $data['heats'];


            foreach ($heats as $heat) {

                $heat = $this->heatMap($heat, 8);

                foreach ($heat as $lane => $competitor) {
                    $insertable[] = ['entity_id' => $competitor->id, 'entity_type' => 'competitor', 'heat' => $heatNumber, 'lane' => $lane, 'speed_event' => $cse->id];
                }

                $heatNumber += 2;
            }
        }



        Heat::insert($insertable);
    }




    private function generateBracket(object $bracket, $event)
    {

        $isPair = false;
        $heats = [];


        $bracket = $event->getScorableEntities()->where('league', $bracket->id);
        if (count($bracket) == 0) return null;


        $clubs = $bracket->groupBy('team');

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
