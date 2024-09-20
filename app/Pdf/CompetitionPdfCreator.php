<?php

namespace App\Pdf;

use App\Models\Brands\Brand;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
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


        $poolNames = ['Main Pool - Scoreboard End', 'Main Pool - Diving Pit End'];
        $eventNames = $this->comp->getSpeedEvents->map(fn($event) => $event->getName());
        $heats = $this->comp->getHeats();
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
                foreach ($this->comp->getSercTanks()->groupBy('serc_tank') as $ind => $tank) {
                    $uniqueBrackets = $tank->unique('league')->pluck('league')->join(', ');
                    $tank = $tank->map(function ($t) {
                        return $this->scoringType === 'rlss-nationals' ? "$t->serc_order. $t->team ($t->region)" : "$t->serc_order. $t->club $t->team";
                    });
                    $data[] = ['name' => "Tank $ind ($uniqueBrackets)", 'data' => $tank];
                }
                $type = strtoupper($type);
                break;
            case 'speed':
                foreach ($this->comp->getHeats()->groupBy('heat') as $ind => $heat) {
                    $uniqueBrackets = $heat->unique('league')->pluck('league')->join(', ');
                    $heat = $heat->sortBy('lane')->map(function ($l) {
                        return $this->scoringType === 'rlss-nationals' ?  "Lane $l->lane: $l->team ($l->region)" : "Lane $l->lane: $l->club $l->team";
                    });

                    $data[] = ['name' => "Heat $ind ($uniqueBrackets)", 'data' => $heat];
                }
        }


        return view("pdfs.marshalling:$this->scoringType", ['brand' => $this->brand, 'location' => $this->comp->where, 'data' => $data, 'comp' => $this->comp, 'type' => $type]);
    }
}
