<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Organisation\Organisation;
use Illuminate\Http\Request;

class LandingController extends Controller
{

    public function showOrganisation(Organisation $organisation)
    {
        return view('landing.organisation', ['org' => $organisation, 'ongoing' => $organisation->getOngoingCompetition()]);
    }

    public function showCompetition(Competition $comp)
    {
        return view('landing.competition.live', ['comp' => $comp]);
    }

    public function showHeatsAndDraws(Competition $comp)
    {
        return view('landing.competition.heats-draws', ['comp' => $comp]);
    }

    public function showResults(Competition $comp)
    {
        return view('landing.competition.results', ['comp' => $comp]);
    }
}
