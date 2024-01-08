<?php

namespace App\Models;

use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        return DB::select("SELECT DISTINCT team FROM competition_teams WHERE club=? ORDER BY team", [$this->id]);
    }

    public function getClubRecords()
    {

        $minimal = [];
        foreach (SpeedEvent::all() as $event) {
            $minimal[$event->id]['result'] =  99999999999999999;
            $minimal[$event->id]['se'] =  $event->name;
        }

        $results = DB::select(' SELECT result, ct.team, se.name AS se, se.id, cp.name AS comp_name, cp.when, c.name FROM speed_results sr INNER JOIN competition_teams ct on ct.id=sr.competition_team INNER JOIN clubs c ON c.id=ct.club INNER JOIN competition_speed_events cse ON cse.id=sr.event INNER JOIN speed_events se ON se.id=cse.event INNER JOIN competitions cp ON cp.id=cse.competition WHERE c.id=? AND result IS NOT NULL AND result>4 AND cp.isLeague = true;', [$this->id]);

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

    public function getBestSerc()
    {
        return DB::select('SELECT sr.team, smp.serc, SUM(result*weight) AS total, SUM(10*weight) AS max, s.name AS serc_name, c.name, ct.team FROM serc_results sr INNER JOIN serc_marking_points smp ON smp.id=sr.marking_point INNER JOIN competition_teams ct ON ct.id=sr.team INNER JOIN sercs s ON s.id=smp.serc INNER JOIN competitions c ON c.id=s.competition WHERE ct.club=? AND c.isLeague = true GROUP BY smp.serc,sr.team ORDER BY total DESC LIMIT 1;', [$this->id]);
    }
}
