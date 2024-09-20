<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\SERC;
use App\Pdf\CompetitionPdfCreator;
use Illuminate\Http\Request;

class PrintableController extends Controller
{

    public function index(Competition $comp)
    {
        return view('competition.printables.index', ['comp' => $comp]);
    }

    public function printCTP(Competition $comp)
    {
        $pdfCreator = new CompetitionPdfCreator($comp);

        return $pdfCreator->chiefTimekeeper();
    }

    public function printSMS(Competition $comp)
    {
        $pdfCreator = new CompetitionPdfCreator($comp);

        return $pdfCreator->sercMarking();
    }

    public function sercSheets(Competition $comp, SERC $serc)
    {
        return view('competition.printables.serc-sheet', ['comp' => $comp, 'serc' => $serc]);
    }

    public function printMarshalling(Competition $comp, Request $request)
    {
        $pdfCreator = new CompetitionPdfCreator($comp);

        return $pdfCreator->marshalling($request->input('type', 'speed'));
    }
}
