<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Heat;
use Illuminate\Http\Request;

class HeatController extends Controller
{

    public function index(Competition $comp)
    {






        $heatEntries = $comp->getHeatEntries;

        return view('competition.heats-and-orders.index', ['comp' => $comp, 'heatEntries' => $heatEntries]);
    }

    public function createDefaultHeatsForComp(Competition $comp)
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



        return;
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
}
