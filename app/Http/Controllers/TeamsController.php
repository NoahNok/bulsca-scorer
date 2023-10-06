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


        $all_clubs = [];

        foreach ($comp->getCompetitionTeams()->reorder('team')->get() as $team) {
            $club_name = $team->getClubName();
            $club_teams = $all_clubs[$club_name] ?? [];



            array_push($club_teams, ["team" => $team->team, "time" => $team->getSwimTowTimeForDefault(), "league" => $team->league]);


            $all_clubs[$club_name] = $club_teams;
        }

        $final = [];
        foreach ($all_clubs as $name => $club) {
            array_push($final, ['name' => $name, 'teams' => $club]);
        }



        return view('competition.teams.edit', ['comp' => $comp, 'currentTeams' => json_encode($final)]);
    }

    public function editPost(Competition $comp, Request $request)
    {

        $json = json_decode($request->input('json'));

        $teamIdsToKeep = [];

        foreach ($json as $json_club) {
            $club = Club::firstOrCreate(['name' => $json_club->name]);
            foreach ($json_club->teams as $json_team) {


                $team = CompetitionTeam::firstOrNew(['club' => $club->id, 'team' => $json_team->team]);

                $team->competition = $comp->id;
                $team->league = $json_team->league;




                $timeParts = explode(":", $json_team->time);
                $seconds = $timeParts[0] * 60 + $timeParts[1];
                $team->st_time = $seconds;
                $team->save();

                array_push($teamIdsToKeep, $team->id);

                if ($team->wasRecentlyCreated) {
                    // If they are a new team, add them to all the current events
                    foreach ($comp->getSpeedEvents as $event) {
                        $sr = new SpeedResult();
                        $sr->competition_team = $team->id;
                        $sr->event = $event->id;
                        $sr->save();
                    }
                }
            }
        }

        // Now remove any deleted teams - these won't be present in the ids to keep

        CompetitionTeam::where('competition', $comp->id)->whereNotIn('id', $teamIdsToKeep)->delete();



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
