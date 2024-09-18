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

    public function __construct(Competition $comp)
    {
        $this->comp = $comp;


        if ($comp->getBrand != null) {
            $this->brand = $comp->getBrand;
        }
    }

    public function test()
    {
        return view('pdfs.heats.chief-timekeeper');
    }

    public function chiefTimekeeper()
    {


        $poolNames = ['Main Pool - Scoreboard End', 'Main Pool - Diving Pit End'];
        $eventNames = $this->comp->getSpeedEvents->map(fn($event) => $event->getName());
        $heats = $this->comp->getHeats();
        return view('pdfs.heats.chief-timekeeper', ['brand' => $this->brand, 'location' => $this->comp->where, 'poolNames' => $poolNames, 'eventNames' => $eventNames, 'heats' => $heats, 'comp' => $this->comp]);
    }

    public function sercMarking()
    {

        $events = $this->comp->getSERCs;
        $tanks = $this->comp->getSercTanks();
        return view('pdfs.sercs.serc-marking', ['brand' => $this->brand, 'location' => $this->comp->where, 'events' => $events, 'tanks' => $tanks, 'comp' => $this->comp]);
    }

    public function marshalling(string $type)
    {

        $data = [];

        switch ($type) {
            case 'serc':
                foreach ($this->comp->getSercTanks()->groupBy('serc_tank') as $ind => $tank) {
                    $uniqueBrackets = $tank->unique('league')->pluck('league')->join(', ');
                    $tank = $tank->map(function ($t) {
                        return "$t->serc_order. $t->team ($t->region)";
                    });
                    $data[] = ['name' => "Tank $ind ($uniqueBrackets)", 'data' => $tank];
                }
                $type = strtoupper($type);
                break;
            case 'speed':
                foreach ($this->comp->getHeats()->groupBy('heat') as $ind => $heat) {
                    $uniqueBrackets = $heat->unique('league')->pluck('league')->join(', ');
                    $heat = $heat->sortBy('lane')->map(function ($l) {
                        return "Lane $l->lane: $l->team ($l->region)";
                    });

                    $data[] = ['name' => "Heat $ind ($uniqueBrackets)", 'data' => $heat];
                }
        }


        return view('pdfs.marshalling', ['brand' => $this->brand, 'location' => $this->comp->where, 'data' => $data, 'comp' => $this->comp, 'type' => $type]);
    }
}
