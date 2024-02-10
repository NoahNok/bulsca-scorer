<?php

namespace App\Models;

use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Club extends Model
{
    use HasFactory, Cloneable;

    protected $fillable = ['name'];

    public function getTeams()
    {
        return $this->hasMany(CompetitionTeam::class, 'club', 'id');
    }

    public function getDistinctTeams()
    {
        return DB::select("SELECT DISTINCT team FROM competition_teams ct INNER JOIN competitions c ON c.id=ct.competition  WHERE club=? AND c.isLeague=true ORDER BY team", [$this->id]);
    }

    public function getClubRecords()
    {

        $minimal = [];
        foreach (SpeedEvent::all() as $event) {
            $minimal[$event->id]['result'] =  99999999999999999;
            $minimal[$event->id]['se'] =  $event->name;
        }

        $results = DB::select(' SELECT result, ct.team, se.name AS se, se.id, cp.name AS comp_name, cp.id AS comp_id, cp.when, c.name, ct.st_time AS st_time, (SELECT COUNT(*) FROM speed_result_penalties WHERE speed_result=sr.id) AS pens FROM speed_results sr INNER JOIN competition_teams ct on ct.id=sr.competition_team INNER JOIN clubs c ON c.id=ct.club INNER JOIN competition_speed_events cse ON cse.id=sr.event INNER JOIN speed_events se ON se.id=cse.event INNER JOIN competitions cp ON cp.id=cse.competition WHERE c.id=? AND result IS NOT NULL AND result>4 AND disqualification IS NULL AND cp.isLeague = true; ', [$this->id]);

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
                
                dump($result['st_time']*1000*1.1);
                dump($actualResult);
                // Otherwise lets see if they are 10% slower than submitted
                if ($actualResult > $result['st_time']*1000*1.1){
                    $pensToApply++;
                    # Lets work out how many more amount of 15s they were out by
                    $diff = $actualResult - ($result['st_time']*1000*1.1) - 15000;
                    dump($diff/15000.0);
                    $pensToApply += floor($diff/15000.0);
                }
                dump($pensToApply);

                // Add 15s for each pen
                $actualResult += $pensToApply * 15000;

            }
            $result['result'] = $actualResult;

            if ($actualResult <= $minimal[$result['id']]['result']) {
                $minimal[$result['id']] = $result;
            }
        }

        return $minimal;
    }

    public function getCompetitionsCompetedAt()
    {
        return DB::select('SELECT c.name, c.id FROM competitions c INNER JOIN competition_teams ct ON ct.competition=c.id INNER JOIN clubs cl ON cl.id=ct.club WHERE cl.id=? AND c.isLeague = true AND c.public_results=true GROUP BY c.id ORDER BY c.when;', [$this->id]);
    }


    // Base SERC query producing table of results grouped by competition > serc > club > team > totalPoints
    // Need to replace :WHERE: with required bits if at all
    public function bestSercBase($where = "", $order = "")
    {
        $where = $where == "" ? "WHERE c.isLeague=true " : "WHERE c.isLeague=true AND " . $where;
        $order = $order == "" ? "" : "ORDER BY " . $order;
        return "SELECT c.name AS comp_name, c.id AS comp_id, serc.name AS serc_name, serc.id AS serc_id, cl.name AS club_name, ct.team, SUM(result*weight) AS total, SUM(10*weight) AS max FROM serc_results sr INNER JOIN serc_marking_points smp ON smp.id=sr.marking_point INNER JOIN sercs serc ON serc.id=smp.serc INNER JOIN competitions c ON c.id=serc.competition INNER JOIN competition_teams ct ON ct.id=sr.team INNER JOIN clubs cl ON cl.id=ct.club $where GROUP BY c.id, serc.id, cl.id, sr.team $order;";
    }


    public function getBestSercs()
    {


        return Cache::remember('stats_club.' . $this->id . '.bestSercs', 60 * 60 * 24, function () {
            return DB::select($this->bestSercBase("cl.id=?", "total/max DESC LIMIT 5"), [$this->id]);
        });
    }


    public function getPlacings($league = 'O')
    {



        $placings = [];

        $placings = Cache::rememberForever('stats_club.' . $this->id . '.placings.' . $league, function () use ($league) {
            $placings = [];
            foreach (Competition::where('isLeague', true)->where('public_results', true)->orderBy('when')->get() as $competition) {
                $overallSchema = $competition->getResultSchemas->where('league', $league)->first();

                if ($overallSchema == null) continue;

                $altQuery = "SELECT * FROM (" . rtrim($overallSchema->getRawQuery(), ';') . ") AS c1 WHERE c1.club=?;";



                $compPlacings = DB::select($altQuery, [$this->id]);

                foreach ($compPlacings as $placing) {
                    $placings[substr($placing->team, -1)][$competition->id]['place'] = $placing->place;
                    $placings[substr($placing->team, -1)][$competition->id]['name'] = $competition->name;
                }
            }
            return $placings;
        });
        return $placings;
    }
}
