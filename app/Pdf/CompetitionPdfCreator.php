<?php

namespace App\Pdf;

use App\Models\Brands\Brand;
use App\Models\Competition;
use App\Models\Interfaces\IEvent;

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

        $location = 'John Charles Centre for Sport, Leeds';
        $poolNames = ['Main Pool - Scoreboard End', 'Main Pool - Diving Pit End'];
        $eventNames = $this->comp->getSpeedEvents->map(fn($event) => $event->getName());
        $heats = $this->comp->getHeats();
        return view('pdfs.heats.chief-timekeeper', ['brand' => $this->brand, 'location' => $location, 'poolNames' => $poolNames, 'eventNames' => $eventNames, 'heats' => $heats, 'comp' => $this->comp]);
    }
}
