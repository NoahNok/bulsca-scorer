<?php

namespace App\Http\Controllers;

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
        $teams = $comp->getCompetitionTeams()->orderBy('st_time', 'desc')->get();

        $heats = [];
        $maxHeats = ceil($teams->count() / $comp->max_lanes);




        // Creates the default heats based on swim tow times!
        for ($i = $maxHeats; $i > 0; $i--) {
            $heatTeams = $teams->pop($comp->max_lanes); // Ordered slowest to fastest

            $orderedTeams = $this->heatMap($heatTeams->reverse()->toArray(), $comp->max_lanes);

            $heats[$i] = $orderedTeams;
        }



        $databaseInsertable = [];

        for ($i = $maxHeats; $i > 0; $i--) {
            $heat = $heats[$i];
            foreach (array_keys($heat) as $l) {


                $d = ['competition' => $comp->id, 'team' => $heat[$l]['id'], 'heat' => $i, 'lane' => $l];;
                array_push($databaseInsertable, $d);
            }
        }


        Heat::where('competition', $comp->id)->delete();
        Heat::insert($databaseInsertable);


        return redirect()->route('comps.view.heats', $comp);
    }

    private function heatMap(array $in, int $maxLanes): array
    {
        #           1,2,3,4,5,6,7,8
        $middleLane = ceil($maxLanes / 2);
        $offset = 1;

        $allocatedHeat = [];
        $allocatedHeat[$middleLane] = array_pop($in);

        $popped = null;
        while (($popped = array_pop($in)) != null) {
            $allocatedHeat[$middleLane + $offset] = $popped;

            if ($offset > 0) $offset = $offset * -1;
            else if ($offset < 0) $offset = ($offset * -1) + 1;
        }




        return $allocatedHeat;
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

        foreach ($comp->getCompetitionTeams as $team) {
            $team->serc_order = $index;
            $team->save();
            $index++;
        }
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
