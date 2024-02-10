<?php

namespace App\Stats;

use App\Models\Club;
use App\Models\SpeedEvent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StatsTeam
{

    private $club;
    private $teamLetter;

    public function __construct(Club $club, $teamLetter)
    {
        $this->club = $club;
        $this->teamLetter = $teamLetter;
    }


    public function getPlacings($league = 'O')
    {

        // Going to cheat and use the existing getPlacings() method from the Club model and just filter for the team we want
        $placings = $this->club->getPlacings($league);
        $filtered = [];
        foreach ($placings as $key => $placing) {

            if (Str::lower($key) == $this->teamLetter) {
                array_push($filtered, $placing);
            }
        }

        return $filtered;
    }

    public function getTeamRecords()
    {
        $minimal = [];
        foreach (SpeedEvent::all() as $event) {
            $minimal[$event->id]['result'] =  99999999999999999;
            $minimal[$event->id]['se'] =  $event->name;
        }

        $results = DB::select(' SELECT result, ct.team, se.name AS se, se.id, cp.name AS comp_name, cp.id AS comp_id, cp.when, c.name, ct.st_time AS st_time, (SELECT COUNT(*) FROM speed_result_penalties WHERE speed_result=sr.id) AS pens FROM speed_results sr INNER JOIN competition_teams ct on ct.id=sr.competition_team INNER JOIN clubs c ON c.id=ct.club INNER JOIN competition_speed_events cse ON cse.id=sr.event INNER JOIN speed_events se ON se.id=cse.event INNER JOIN competitions cp ON cp.id=cse.competition WHERE c.id=? AND result IS NOT NULL AND result>4 AND disqualification IS NULL AND cp.isLeague = true AND ct.team=?;', [$this->club->id, Str::upper($this->teamLetter)]);


        

        foreach ($results as $result) {
            $result = (array) $result;

            $actualResult = (float) $result['result'];

            if ($result['se'] === "Rope Throw" && $result['pens'] > 0) {
                $pensToApply = $result['pens'];

                // If you had a time
                if ($actualResult > 3) {
                    // Drop to 3 in
                    $actualResult = 3;
                    $pensToApply--;
                }

                // Now sub pens from actual result with a min of 0
                $actualResult = max(0, $actualResult - $pensToApply);

            } 
            if ($result['se'] === "Swim & Tow") {
                $pensToApply = $result['pens'];
                

                // If you have > 5 non P900 skip as thats a DQ
                if ($pensToApply > 5) {
                    continue;
                }
                
          
                // Otherwise lets see if they are 10% slower than submitted
                if ($actualResult > $result['st_time']*1000*1.1){
                    $pensToApply++;
                    # Lets work out how many more amount of 15s they were out by
                    $diff = $actualResult - ($result['st_time']*1000*1.1) - 15000;
                   
                    $pensToApply += floor($diff/15000.0);
                }
          

                // Add 15s for each pen
                $actualResult += $pensToApply * 15000;

            }
            $result['result'] = $actualResult;

            if ($actualResult <= $minimal[$result['id']]['result']) {
                // Skip over any rope throw results taht werent in
                if ($actualResult < 4) {
                    continue;
                }

                $minimal[$result['id']] = $result;
            }

      
        }

        return $minimal;
    }


    public function getBestSercs()
    {


        return Cache::remember('stats_club.' . $this->club->id . '.bestSercs.' . $this->teamLetter, 60 * 60 * 24, function () {
            return DB::select($this->club->bestSercBase("cl.id=? AND ct.team=?", "total/max DESC LIMIT 5"), [$this->club->id, Str::upper($this->teamLetter)]);
        });
    }

    public function getCompetitionsCompetedAt()
    {
        return DB::select('SELECT c.name, c.id FROM competitions c INNER JOIN competition_teams ct ON ct.competition=c.id INNER JOIN clubs cl ON cl.id=ct.club WHERE cl.id=? AND c.isLeague = true AND ct.team=? AND c.public_results=true GROUP BY c.id ORDER BY c.when;', [$this->club->id, Str::upper($this->teamLetter)]);
    }

    public function getTeamLetter()
    {
        return Str::upper($this->teamLetter);
    }

    public function getTeamName()
    {
        return $this->club->name . ' ' . $this->getTeamLetter();
    }

    public function getTeamSlug()
    {
        return $this->club->name . '.' . $this->getTeamLetter();
    }
}
