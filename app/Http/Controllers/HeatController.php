<?php

namespace App\Http\Controllers;

use App\Helpers\ScoringHelper;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\Heat;
use Illuminate\Http\Request;

class HeatController extends Controller
{

    public function index(Competition $comp)
    {






        $heatEntries = $comp->getHeatEntries;

        return view('competition.heats-and-orders.index', ['comp' => $comp, 'heatEntries' => $heatEntries]);
    }

    public function createDefaultHeatsForComp(Competition $comp, Request $request)
    {
        ScoringHelper::resolve($comp->scoring_type, 'heat')->generate($comp);


        return redirect()->route('comps.view.heats', $comp);
    }



    public function edit(Competition $comp)
    {
        $heatEntries = $comp->getHeatEntries;

        return view('competition.heats-and-orders.heats.edit', ['comp' => $comp, 'heatEntries' => $heatEntries]);
    }

    public function editPost(Competition $comp, Request $request)
    {
        $team = $request->input('team', -1);
        $lane = $request->input('target-lane');
        $heat = $request->input('target-heat');

        // Check if a team exists as the target location, if so we need to swap teams
        $foundHeat = Heat::where('competition', $comp->id)->where('lane', $lane)->where('heat', $heat)->first();

        if ($foundHeat == null) {
            // No team at target location simple update
            $theat = Heat::where('team', $team)->first();
            $theat->lane = $lane;
            $theat->heat = $heat;
            $theat->save();
            return redirect()->route('comps.view.heats.edit', $comp);
        }

        $theat = Heat::where('team', $team)->first();
        $toriglane = $theat->lane;
        $torigheat = $theat->heat;

        $theat->lane = $lane;
        $theat->heat = $heat;
        $theat->save();

        $foundHeat->lane = $toriglane;
        $foundHeat->heat = $torigheat;
        $foundHeat->save();

        return redirect()->route('comps.view.heats.edit', $comp);
    }


    private function createDefaultSERCorderForComp(Competition $comp)
    {
        $index = 1;

        foreach ($comp->getCompetitionTeams->shuffle() as $team) {
            $team->serc_order = $index;
            $team->save();
            $index++;
        }
    }

    public function regenSERCOrder(Competition $comp)
    {
        $this->createDefaultSERCorderForComp($comp);
        return redirect()->route('comps.view.heats', $comp);
    }

    public function editSERCOrder(Competition $comp)
    {
        return view('competition.heats-and-orders.serc-order.edit', ['comp' => $comp]);
    }

    public function editSERCOrderPost(Competition $comp, Request $request)
    {
        $teamFrom = CompetitionTeam::find($request->input('teamFrom'));
        $teamTo = CompetitionTeam::find($request->input('teamTo'));

        if ($teamFrom->serc_order == 0 || $teamTo->serc_order == 0) {
            $this->createDefaultSERCorderForComp($comp);
        }

        $temp = $teamFrom->serc_order;

        $teamFrom->serc_order = $teamTo->serc_order;
        $teamTo->serc_order = $temp;

        $teamFrom->save();
        $teamTo->save();

        return redirect()->route('comps.view.serc-order.edit', $comp);
    }
}
