<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\SERC;
use Illuminate\Http\Request;

class PrintableController extends Controller
{
    public function sercSheets(Competition $comp, SERC $serc)
    {
        return view('competition.printables.serc-sheet', ['comp' => $comp, 'serc' => $serc]);
    }
}
