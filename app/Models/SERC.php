<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SERC extends Model
{
    use HasFactory;

    protected $table = 'sercs';

    public function getJudges()
    {
        return $this->hasMany(SERCJudge::class, 'serc', 'id');
    }

    public function getTeams()
    {
        return CompetitionTeam::where('competition', $this->competition)->get();
    }

    public function getResults()
    {
        // Raw query
        // SELECT *, RANK() OVER (ORDER BY points DESC) place FROM (SELECT *, (score/max)*1000 AS points FROM (WITH tbl AS (SELECT CONCAT(c.name, ' ', ct.team) AS team, sr.team AS tid, SUM(result*weight) as score FROM serc_results sr INNER JOIN serc_marking_points mp ON marking_point=mp.id INNER JOIN competition_teams ct ON ct.id=sr.team INNER JOIN clubs c ON c.id=ct.club WHERE mp.serc=? GROUP BY team, tid) SELECT *, (SELECT MAX(score) FROM tbl) AS max FROM tbl) AS t) AS f; 

        $results = DB::select("SELECT *, RANK() OVER (ORDER BY points DESC) place FROM (SELECT *, (score/max)*1000 AS points FROM (WITH tbl AS (SELECT CONCAT(c.name, ' ', ct.team) AS team, sr.team AS tid, SUM(result*weight) as score FROM serc_results sr INNER JOIN serc_marking_points mp ON marking_point=mp.id INNER JOIN competition_teams ct ON ct.id=sr.team INNER JOIN clubs c ON c.id=ct.club WHERE mp.serc=? GROUP BY team, tid) SELECT *, (SELECT MAX(score) FROM tbl) AS max FROM tbl) AS t) AS f; ", [$this->id]);

        return $results;
    }
}
