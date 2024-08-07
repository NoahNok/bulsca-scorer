<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    //

    public function index()
    {
        $c = Competition::orderBy('when')->paginate(12);


        if (!auth()->user()->isAdmin()) return back();


        return view('competitions', ['comps' => $c]);
    }

    public function view(Competition $comp, Request $request)
    {
        $request->session()->put('ac', $comp);

        return view('competition.view', ['comp' => $comp]);
    }

    public function events(Competition $comp, Request $request)
    {
        $request->session()->put('ac', $comp);

        return view('competition.events', ['comp' => $comp]);
    }


    public function teams(Competition $comp, Request $request)
    {
        $request->session()->put('ac', $comp);

        return view('competition.teams', ['comp' => $comp]);
    }

    public function competitors(Competition $comp, Request $request)
    {
        $request->session()->put('ac', $comp);

        return view('competition.competitors', ['comp' => $comp]);
    }

    public function createCompetitionStats(Competition $comp) {
        
        $comp->generateStats();

        return back()->with('success', 'Stats created');


    }

    public function settings(Competition $comp) {
        return view('competition.settings', ['comp' => $comp]);
    }

    public function updateCompetitionSettings(Competition $comp, Request $request) {
        $comp->max_lanes = $request->input('lanes', $comp->max_lanes);
        $newDateTime = $request->input('serc_start_time', $comp->serc_start_time);
        $utcDate = Carbon::parse($newDateTime, 'BST');
        $utcDate->setTimezone('UTC');
   
        $comp->serc_start_time = $utcDate;
        $comp->can_be_live = $request->has('can_be_live');

        $comp->save();

        return redirect()->route('comps.view', $comp)->with('success', 'Settings updated');
    }
}
