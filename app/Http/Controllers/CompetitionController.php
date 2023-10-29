<?php

namespace App\Http\Controllers;

use App\Models\Competition;
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
}
