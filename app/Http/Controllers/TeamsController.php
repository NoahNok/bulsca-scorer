<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\League;
use App\Models\SpeedResult;
use Illuminate\Http\Request;
use Mockery\Undefined;

class TeamsController extends Controller
{
    //

    public function edit(Competition $comp)
    {
        return view('competition.teams.edit', ['comp' => $comp]);
    }

    public function editPost(Competition $comp, Request $request)
    {

        $json = json_decode($request->input('data'));
        foreach ($json as $row) {
            $team = null;
            $isNew = false;
            if ($row->id == "null") {
                $team = new CompetitionTeam();
                $isNew = true;
            } else {
                $team = CompetitionTeam::find($row->id);
            }

            $team->club = Club::firstOrCreate(['name' => $row->values->club])->id;
            $team->team = $row->values->team;

            $team->competition = $comp->id;
            $team->league = $row->values->league;




            $timeParts = explode(":", $row->values->st_time);
            $seconds = $timeParts[0] * 60 + $timeParts[1];
            $team->st_time = $seconds;

            $team->save();



            if ($isNew) {
                // If they are a new team, add them to all the current events
                foreach ($comp->getSpeedEvents as $event) {
                    $sr = new SpeedResult();
                    $sr->competition_team = $team->id;
                    $sr->event = $event->id;
                    $sr->save();
                }
            }
        }

        return;

        //return redirect()->back();
    }


    public function delete(Request $request)
    {
        $ct = CompetitionTeam::find($request->input('ctid'));
        $ct->delete();

        return redirect()->back();
    }
}
