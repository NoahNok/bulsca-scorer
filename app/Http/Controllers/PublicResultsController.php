<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\SERC;
use Illuminate\Http\Request;

class PublicResultsController extends Controller
{
    public function index()
    {

        $compsWithViewAbleResults = Competition::where('public_results', true)->get();

        return view('public-results.index', ['comps' => $compsWithViewAbleResults]);
    }

    public function viewComp(Competition $comp_slug)
    {
        return view('public-results.view-comp', ['comp' => $comp_slug]);
    }

    public function viewSpeed(Competition $comp_slug, CompetitionSpeedEvent $event)
    {
        return view('public-results.view-speed', ['comp' => $comp_slug, 'event' => $event]);
    }

    public function viewSerc(Competition $comp_slug, SERC $event)
    {
        return view('public-results.view-serc', ['comp' => $comp_slug, 'event' => $event]);
    }
}
