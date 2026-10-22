<?php

namespace App\Http\Controllers;

use App\Http\Requests\League\CreateLeagueRequest;
use App\Models\Competition;
use App\Models\League;

/**
 * A competition's leagues, added, renamed and deleted from dialogs on its Entries & Leagues page.
 */
class LeagueController extends Controller
{
    public function store(Competition $comp, CreateLeagueRequest $request)
    {
        $comp->getLeagues()->create(['name' => $request->validated('name')]);

        return back()->with('success', 'League added');
    }

    public function update(Competition $comp, League $league, CreateLeagueRequest $request)
    {
        $league->update(['name' => $request->validated('name')]);

        return back()->with('success', 'League saved');
    }

    public function delete(Competition $comp, League $league)
    {
        $league->delete();

        return back()->with('success', 'League deleted');
    }
}
