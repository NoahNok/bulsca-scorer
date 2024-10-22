<?php

namespace App\Pdf;

use App\Models\Brands\Brand;
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
    private ?Brand $brand = null;
    private string $scoringType;

    public function __construct(Competition $comp)
    {
        $this->comp = $comp;


        if ($comp->getBrand != null) {
            $this->brand = $comp->getBrand;
        }

        $this->scoringType = $comp->scoring_type;
    }

    public function test()
    {
        return view("pdfs.heats.chief-timekeeper:$this->scoringType");
    }

    public function chiefTimekeeper()
    {


        $poolNames = ['Main Pool - Diving Pit End', 'Main Pool - Scoreboard End'];
        $eventNames = $this->comp->getSpeedEvents->map(fn($event) => $event->getName());
        $heats = [];
        foreach ($this->comp->getSpeedEvents as $event) {

            $targetId = null;
            if ($this->comp->scoring_type == 'rlss-nationals') {
                $targetId = $event->id;
            }


            $heats[$event->getName()] = $this->comp->getHeats($targetId);
        }


        return view("pdfs.heats.chief-timekeeper:$this->scoringType", ['brand' => $this->brand, 'location' => $this->comp->where, 'poolNames' => $poolNames, 'eventNames' => $eventNames, 'heats' => $heats, 'comp' => $this->comp]);
    }

    public function sercMarking()
    {

        $events = $this->comp->getSERCs;
        $tanks = $this->comp->getSercTanks();
        return view("pdfs.sercs.serc-marking:$this->scoringType", ['brand' => $this->brand, 'location' => $this->comp->where, 'events' => $events, 'tanks' => $tanks, 'comp' => $this->comp]);
    }

    public function marshalling(string $type)
    {

        $data = [];

        switch ($type) {
            case 'serc':
                $hd = [];
                foreach ($this->comp->getSercTanks()->groupBy('serc_tank') as $ind => $tank) {

                    $uniqueBrackets = $tank->unique('league')->pluck('league')->join(', ');
                    $tank = $tank->map(function ($t) {

                        if ($this->scoringType === 'rlss-nationals') {

                            $c = Competitor::find($t->tid);
                            $name = $c->getFullname();
                            return "$t->serc_order. $name ($t->region)";
                        } else {
                            return  "$t->serc_order. $t->club $t->team";
                        }
                    });
                    $hd[] = ['name' => "Tank $ind ($uniqueBrackets)", 'data' => $tank, 'number' => $ind];
                }
                $data[] = ['event' => "SERC", 'heats' => $hd];
                $type = strtoupper($type);
                break;
            case 'speed':
                foreach ($this->comp->getSpeedEvents as $event) {
                    $hd = [];
                    $targetId = null;
                    if ($this->comp->scoring_type == 'rlss-nationals') {
                        $targetId = $event->id;
                    }
                    foreach ($this->comp->getHeats($targetId)->groupBy('heat') as $ind => $heat) {



                        $uniqueBrackets = $heat->unique('league')->pluck('league')->join(', ');
                        $heat = $heat->sortBy('lane')->map(function ($l) {
                            return $this->scoringType === 'rlss-nationals' ?  "Lane $l->lane: $l->team ($l->region)" : "Lane $l->lane: $l->club $l->team";
                        });

                        $hd[] = ['name' => "Heat $ind ($uniqueBrackets)", 'data' => $heat, 'number' => $ind];
                    }

                    $data[] = ['event' => $event->getName(), 'heats' => $hd];
                }
        }

        $poolNames = ['Main Pool - Diving Pit End', 'Main Pool - Scoreboard End'];
        return view("pdfs.marshalling:$this->scoringType", ['brand' => $this->brand, 'location' => $this->comp->where, 'data' => $data, 'poolNames' => $poolNames, 'comp' => $this->comp, 'type' => $type]);
    }
}
