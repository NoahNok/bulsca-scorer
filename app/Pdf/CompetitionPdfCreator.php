<?php

namespace App\Pdf;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\Competitor;
use App\Models\Interfaces\IEvent;
use App\Models\SERC;
use PhpParser\Node\Stmt\Foreach_;

class CompetitionPdfCreator
{

    private Competition $comp;

    private string $scoringType;

    public function __construct(Competition $comp)
    {
        $this->comp = $comp;
    }

    public function test()
    {
        return view("pdfs.heats.chief-timekeeper");
    }

    public function chiefTimekeeper()
    {


        $poolNames = ['Main Pool - Diving Pit End', 'Main Pool - Scoreboard End'];
        $eventNames = $this->comp->getSpeedEvents->map(fn($event) => $event->getName());
        $heats = $this->comp->getHeats();


        return view("pdfs.heats.chief-timekeeper", ['location' => $this->comp->where, 'poolNames' => $poolNames, 'eventNames' => $eventNames, 'heats' => $heats, 'comp' => $this->comp]);
    }

    public function sercMarking()
    {

        $events = $this->comp->getSERCs;
        $tanks = $this->comp->getDraws();
        return view("pdfs.sercs.serc-marking", ['location' => $this->comp->where, 'events' => $events, 'tanks' => $tanks, 'comp' => $this->comp]);
    }

    public function marshalling(string $type)
    {

        $data = [];

        switch ($type) {
            case 'serc':

                foreach ($this->comp->getDraws() as $heatevent) {

                    $hd = [];

                    foreach ($heatevent['draws'] as $tank_no => $draw) {
                        $draw_data = $draw->map(function ($lane) use ($heatevent) {
                            $comp = $this->comp;
                            return "{$lane->draw}: {$lane->entity?->getName($comp)}";
                        });

                        $uniqueLeagues = $draw->map(function ($lane) use ($heatevent) {
                            return $lane->entity?->getLeague->name;
                        })->unique()->values()->implode(', ');


                        $tank_no += 1;

                        $hd[] = ['name' => "Tank {$tank_no} ($uniqueLeagues)", 'data' => $draw_data, 'number' => $tank_no];
                    }


                    $data[] = ['event' => $heatevent['serc']->getName(), 'heats' => $hd];
                }
                break;
            case 'speed':

                foreach ($this->comp->getHeats() as $heatevent) {

                    $hd = [];

                    foreach ($heatevent['heats'] as $heat_no => $lanes) {
                        $lane_data = $lanes->map(function ($lane) use ($heatevent) {
                            $comp = $this->comp;
                            return "Lane {$lane->lane}: {$lane->entity?->getName($comp)}";
                        });

                        $uniqueLeagues = $lanes->map(function ($lane) use ($heatevent) {
                            return $lane->entity?->getLeague->name;
                        })->unique()->values()->implode(', ');

                        $hd[] = ['name' => "Heat $heat_no ($uniqueLeagues)", 'data' => $lane_data, 'number' => $heat_no];
                    }


                    $data[] = ['event' => $heatevent['event']->getName(), 'heats' => $hd];
                }
        }

        $type = strtoupper($type);

        $poolNames = ['Main Pool - Diving Pit End', 'Main Pool - Scoreboard End'];
        return view("pdfs.marshalling", ['location' => $this->comp->where, 'data' => $data, 'poolNames' => $poolNames, 'comp' => $this->comp, 'type' => $type]);
    }
}
