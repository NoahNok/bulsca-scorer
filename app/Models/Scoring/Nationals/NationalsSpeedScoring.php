<?php

namespace App\Models\Scoring\Nationals;

use App\Models\Interfaces\IEvent;
use App\Models\League;
use App\Models\Scoring\IScoring;
use Illuminate\Support\Facades\DB;

class NationalsSpeedScoring implements IScoring {

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
        $previousResult = -1;
        $previousObject = null;
        $skipBy = 0;

        foreach ($results as $result) {
        

        

            if ($result->cid == $previousCid && $previousObject != null) { // pairs combined into one row
        
                $previousObject->pair = new \stdClass();
                $previousObject->pair->name = $result->team;
                $previousObject->pair->result = $result->result;
                $previousObject->pair->disqualification = $result->disqualification;
                $previousObject->pair->base_result = $result->base_result;

                // remove this result from the array
                $result->skip = true;
                
            
                continue;
            }


            $previousCid = $result->cid;
            $previousObject = $result;



            
            if ($result->result == $previousResult) { // same results given same place
        
                $skipBy++;
            } else {
                $currentPlace++;

                if ($skipBy > 0) {
                    $currentPlace += $skipBy;
                    $skipBy = 0;
                }
            }
            $previousResult = $result->result;

            $result->points = $currentPlace;

            $result->place = $currentPlace;
        

        }

        // Special case for events with less than 4 competitors
        if (count($results) < 4) {
            foreach ($results as $result) {
                if ($result->disqualification != null) {
                    $result->points = 4;
                    $result->place = 4;
                }
            }
        }
        return $results;



    }

    public function getResultQuery(IEvent $event): string
    {
        $query = "";

        if ($event->getName() == "Rope Throw") {
            $query = 'WITH results AS (SELECT sr.result AS base_result, IF((SELECT COUNT(*) FROM competition_teams WHERE club=c.id) > 1, (SELECT SUM(IF(disqualification IS NULL,  IF(result <= 45000, result, 120000), 120000)) FROM speed_results sr INNER JOIN competition_teams ct on ct.id=sr.competition_team WHERE ct.club=c.id AND sr.event=:EVENT:), IF(disqualification IS NULL, IF(result <= 45000, result, 120000), 120000)) AS result, CONCAT(ct.team, " - ", c.name, " (", c.region, ")") AS team, (SELECT name FROM leagues WHERE id=ct.league) AS league, sr.disqualification, c.id AS cid, ct.id AS tid FROM speed_results sr INNER JOIN competition_teams ct ON sr.competition_team=ct.id INNER JOIN clubs c ON ct.club=c.id WHERE sr.event=:EVENT: AND sr.result IS NOT NULL :LEAGUE_COND:) SELECT * FROM results ORDER BY CAST(result AS UNSIGNED), cid;';
        } else {
            $query = 'WITH results AS (SELECT sr.result AS base_result, IF((SELECT COUNT(*) FROM competition_teams WHERE club=c.id) > 1, (SELECT SUM(IF(disqualification IS NULL,  result, 600000)) FROM speed_results sr INNER JOIN competition_teams ct on ct.id=sr.competition_team WHERE ct.club=c.id AND sr.event=:EVENT:), IF(disqualification IS NULL, result, 600000)) AS result, CONCAT(ct.team, " - ", c.name, " (", c.region, ")") AS team, (SELECT name FROM leagues WHERE id=ct.league) AS league, sr.disqualification, c.id AS cid, ct.id AS tid FROM speed_results sr INNER JOIN competition_teams ct ON sr.competition_team=ct.id INNER JOIN clubs c ON ct.club=c.id WHERE sr.event=:EVENT: AND sr.result IS NOT NULL :LEAGUE_COND:) SELECT * FROM results ORDER BY CAST(result AS UNSIGNED), cid;';
        }

        return str_replace(":EVENT:", $event->id, $query);
        
    }
    

}