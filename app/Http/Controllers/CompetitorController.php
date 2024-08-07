<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Competition;
use App\Models\Competitor;
use App\Models\League;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompetitorController extends Controller
{
    public function edit(Competition $comp) {

        
        $curRegions = ['All Ireland', 'East Midlands', 'East', 'North East', 'North West', 'Scotland', 'South East', 'South', 'South West', 'Wales', 'West Midlands', 'West', 'Yorkshire'];
        // Can also include duplicates ammended with B for places allowed to send a 2nd place
        $dbBrackets = League::where('scoring_type', 'rlss-nationals')->get();
        $brackets = [];

        foreach ($dbBrackets as $bracket) {
            $b = new \stdClass();
            $b->name = $bracket->name;
            $b->pairs = str_contains($bracket->name, 'Pairs');
            $b->id = $bracket->id;

            $brackets[] = $b;
        }


        $data = [
            'availRegions' => $curRegions,
            'availBrackets' => $brackets
        ];

        // Now load existing data
        $regions = [];

        foreach ($curRegions as $region) {
            $teams = DB::select('SELECT ct.id, ct.team, c.id AS c_id, c.name AS c_name, l.id AS b_id, l.name AS b_name FROM competition_teams ct INNER JOIN clubs c ON ct.club=c.id INNER JOIN leagues l ON ct.league=l.id WHERE c.region=? AND ct.competition=?', [$region, $comp->id]);

            if (count($teams) == 0) {
                continue;
            }

            $brackets = [];

            foreach ($dbBrackets as $bracket) {
                $swimmers = [];
                $clubName = "";
                $clubId = -1;

                foreach ($teams as $team) {
                    if ($team->b_name == $bracket->name) {
                        $swimmers[] = ['name' => $team->team, 'id' => $team->id];

                        $clubName = $team->c_name;
                        $clubId = $team->c_id;
                        // remove team from list - avoid unecessary looping
                        unset($teams[array_search($team, $teams)]);
                    }
                }

                if (count($swimmers) == 0) {
                    continue;
                }

                $brackets[] = [
                    'name' => $bracket->name,
                    'id' => $bracket->id,
                    'competitors' => [
                        'club' => $clubName,
                        'id' => $clubId,
                        'swimmers' => $swimmers
                    ],
                    'hide' => false
                ];
            }

            $regions[] = [
                'name' => $region,
                'brackets' => $brackets
            ];


        }




        return view('competition.competitors.edit', compact('comp', 'data', 'regions'));
    }

    public function save(Competition $comp, Request $request) {


        $data = json_decode($request->input('json'));

        foreach ($data as $region) { // $region is object
            foreach ($region->brackets as $bracket) { // $bracket: {name: , id: , competitors: }
                $clubName = $bracket->competitors->club;
                $swimmers = $bracket->competitors->swimmers; // [{name: , id?:}]

             

                // Find club by id or create a new one
                $club = null;
                if (property_exists($bracket->competitors, 'id')) {
                    $club = Club::find($bracket->competitors->id);
                } else {
                    if ($clubName == "") {
                        continue;
                    }
                    $club = new Club(); // Dont reuse old clubs incase of region change, which would break results
                    $club->region = $region->name;
               
    
      
                }
                $club->name = $clubName;
                $club->save();

                if ($clubName == "") { // If club name is empty we are delete the club
                    $club->delete();
                    continue;
                }

                $addedAny = false;
                foreach ($swimmers as $swimmer) {

                    if ($swimmer->name == "") {
                        continue;
                    }

                    $swim = null;
                    if (property_exists($swimmer, 'id')) {
                        $swim = Competitor::find($swimmer->id);
                    } else {
                        $swim = new Competitor();
                    }

               
                    $swim->team = $swimmer->name; // suing team as name
                    $swim->club = $club->id;
                    $swim->competition = $comp->id;
                    $swim->league = $bracket->id;
                    $swim->st_time = 0; // Not used
                    $swim->save();

                    $addedAny = true;
                }

                if (!$addedAny) {
                    $club->delete();
                }

      

            }
        }

        return;
    }
}
