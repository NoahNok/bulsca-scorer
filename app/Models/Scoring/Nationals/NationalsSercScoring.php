<?php

namespace App\Models\Scoring\Nationals;

use App\Models\Competitor;
use App\Models\Interfaces\IEvent;
use App\Models\League;
use App\Models\Scoring\IScoring;
use Illuminate\Support\Facades\DB;

class NationalsSercScoring implements IScoring {
    public function getResults(IEvent $event): array
    {

        $query = $this->getResultQuery($event);
        
        $leagueCond = "";

        if (request()->has("bracket") && request()->get("bracket") != "") {

            $league = League::find(request()->get("bracket"));

            if ($league == null) {
                return [];
            }

            $leagueCond = "AND ct.league=" . $league->id;
        }

        $query = str_replace(":LEAGUE_COND:", $leagueCond, $query);

        $results = DB::select($query); // ordered 

        $currentPlace = 0;
        $previousCid = -1;
        $previousScore = -1;
        $previousObject = null;
        $skipBy = 0;

        foreach ($results as $result) {
        

        

            // Check if result belongs to a pair, if so add the pairs name here
            $swimmers = Competitor::where('club', $result->cid)->get();

            if (count($swimmers) > 1) {
                $pair = $swimmers->where('id', "!=", $result->tid)->first(); // get the other swimmer by finding the other swimmer with not the current id
                $result->pair = $pair->team;
            }


    


            
            if ($result->score == $previousScore) { // same results given same place
        
                $skipBy++;
            } else {
                $currentPlace++;

                if ($skipBy > 0) {
                    $currentPlace += $skipBy;
                    $skipBy = 0;
                }
            }
            $previousScore = $result->score;


            $result->place = $currentPlace;
        

        }
        return $results;
    }

    public function getResultQuery(IEvent $event): string
    {
        return str_replace("?", $event->id, "SELECT *, RANK() OVER (ORDER BY points) place FROM (SELECT *, (SELECT id FROM serc_disqualifications WHERE serc=? AND team=tid ) AS disqualification, IF(EXISTS (SELECT * FROM serc_disqualifications WHERE serc=? AND team=tid) , 0, (score/max)*1000) AS points FROM (WITH tbl AS (SELECT ct.team AS team, c.name AS club_name, c.region AS club_region, c.id AS cid, sr.team AS tid, ct.club AS club, (SELECT name FROM leagues WHERE id=ct.league) AS league, SUM(result*weight) as score FROM serc_results sr INNER JOIN serc_marking_points mp ON marking_point=mp.id INNER JOIN competition_teams ct ON ct.id=sr.team INNER JOIN clubs c ON c.id=ct.club INNER JOIN leagues l on l.id=ct.league WHERE mp.serc=? :LEAGUE_COND: GROUP BY team, tid) SELECT *, (SELECT MAX(score) FROM tbl) AS max FROM tbl) AS t) AS f;");
        
    
    }


}