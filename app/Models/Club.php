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

        $results = DB::select(' SELECT result, ct.team, se.name AS se, se.id, cp.name AS comp_name, cp.id AS comp_id, cp.when, c.name FROM speed_results sr INNER JOIN competition_teams ct on ct.id=sr.competition_team INNER JOIN clubs c ON c.id=ct.club INNER JOIN competition_speed_events cse ON cse.id=sr.event INNER JOIN speed_events se ON se.id=cse.event INNER JOIN competitions cp ON cp.id=cse.competition WHERE c.id=? AND result IS NOT NULL AND result>4 AND cp.isLeague = true;', [$this->id]);

        foreach ($results as $result) {
            $result = (array) $result;
            if ($result['result'] <= $minimal[$result['id']]['result']) {
                $minimal[$result['id']] = $result;
            }
        }

        return $minimal;
    }

    public function getCompetitionsCompetedAt()
    {
        return DB::select('SELECT c.name, c.id FROM competitions c INNER JOIN competition_teams ct ON ct.competition=c.id INNER JOIN clubs cl ON cl.id=ct.club WHERE cl.id=? AND c.isLeague = true GROUP BY c.id;', [$this->id]);
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
            foreach (Competition::where('isLeague', true)->orderBy('when')->get() as $competition) {
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
