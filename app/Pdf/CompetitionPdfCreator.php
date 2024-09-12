<?php

namespace App\Pdf;

use App\Models\Competition;

class CompetitionPdfCreator
{

    private Competition $comp;

    public function __construct(Competition $comp)
    {
        $this->comp = $comp;
    }

    public function test()
    {
        return view('pdfs.heats.chief-timekeeper');
    }

    public function chiefTimekeeper()
    {
        $heats = $this->comp->getHeats();
        return view('pdfs.heats.chief-timekeeper', ['heats' => $heats, 'comp' => $this->comp]);
    }
}
