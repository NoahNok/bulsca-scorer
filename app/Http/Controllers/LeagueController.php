<?php

namespace App\Http\Controllers;

use App\Http\Requests\League\CreateLeagueRequest;
use App\Models\Competition;
use App\Models\League;
use Illuminate\Http\Request;

class LeagueController extends Controller
{
    public function create(Competition $comp)
    {
        return view('competition.leagues.create', compact('comp'));
    }

    public function store(Competition $comp, CreateLeagueRequest $request)
    {
        $validated = $request->validated();


        $comp->getLeagues()->create([
            'name' => $validated['name']
        ]);

        return redirect()->route('comps.teams', $comp);
    }

    public function view(Competition $comp, League $league)
    {
        return view('competition.leagues.view', compact('comp', 'league'));
    }

    public function update(Competition $comp, League $league, CreateLeagueRequest $request)
    {
        $validated = $request->validated();

        $league->name = $validated['name'];
        $league->save();

        return redirect()->route('comps.teams', $comp)->with('success', 'Updated league');
    }

    public function delete(Competition $comp, League $league)
    {
        $league->delete();

        return redirect()->route('comps.teams', $comp)->with('success', 'League deleted');
    }
}
