<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Organisation\Organisation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LandingController extends Controller
{

    public function explore()
    {

        $comps = Competition::orderBy('when', 'desc')->paginate(12);
        $orgs = Organisation::orderBy('name')->paginate(12, ['*'], 'orgs_page');
        $ongoing = Competition::whereDate('when', Carbon::today())->first();


        return view('landing.explore', compact(['comps', 'orgs', 'ongoing']));
    }

    public function showOrganisation(Organisation $organisation)
    {
        return view('landing.organisation', ['org' => $organisation, 'ongoing' => $organisation->getOngoingCompetition()]);
    }

    public function showCompetition(Competition $comp)
    {
        return view('landing.competition.overview', ['comp' => $comp]);
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
