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
        return CompetitionTeam::where('competition', $this->competition)->orderBy('serc_order')->get();
    }

    public function getName()
    {
        return $this->name;
    }

    public function getResults()
    {
        // Raw query
        // SELECT *, RANK() OVER (ORDER BY points DESC) place FROM (SELECT *, IF(EXISTS (SELECT * FROM serc_disqualifications WHERE serc=16 AND team=tid) , 0, (score/max)*1000) AS points FROM (WITH tbl AS (SELECT CONCAT(c.name, ' ', ct.team) AS team, sr.team AS tid, SUM(result*weight) as score FROM serc_results sr INNER JOIN serc_marking_points mp ON marking_point=mp.id INNER JOIN competition_teams ct ON ct.id=sr.team INNER JOIN clubs c ON c.id=ct.club WHERE mp.serc=16 GROUP BY team, tid) SELECT *, (SELECT MAX(score) FROM tbl) AS max FROM tbl) AS t) AS f;

        $results = DB::select("SELECT *, RANK() OVER (ORDER BY points DESC) place FROM (SELECT *, IF(EXISTS (SELECT * FROM serc_disqualifications WHERE serc=? AND team=tid) , 0, (score/max)*1000) AS points FROM (WITH tbl AS (SELECT CONCAT(c.name, ' ', ct.team) AS team, sr.team AS tid, SUM(result*weight) as score FROM serc_results sr INNER JOIN serc_marking_points mp ON marking_point=mp.id INNER JOIN competition_teams ct ON ct.id=sr.team INNER JOIN clubs c ON c.id=ct.club WHERE mp.serc=? GROUP BY team, tid) SELECT *, (SELECT MAX(score) FROM tbl) AS max FROM tbl) AS t) AS f;", [$this->id, $this->id]);

        return $results;
    }

    public function getTeamDQ(CompetitionTeam $team)
    {
        return SERCDisqualification::where(['team' => $team->id, 'serc' => $this->id])->first();
    }

    public function getTeamPenalties(CompetitionTeam $team)
    {
        return SERCPenalty::where(['team' => $team->id, 'serc' => $this->id])->first();
    }

    public function getResultQuery()
    {
        return str_replace("?", $this->id, "SELECT *, RANK() OVER (ORDER BY points DESC) place FROM (SELECT *, (SELECT id FROM serc_disqualifications WHERE serc=? AND team=tid ) AS disqualification, IF(EXISTS (SELECT * FROM serc_disqualifications WHERE serc=? AND team=tid) , 0, (score/max)*1000) AS points FROM (WITH tbl AS (SELECT CONCAT(c.name, ' ', ct.team) AS team, sr.team AS tid, SUM(result*weight) as score FROM serc_results sr INNER JOIN serc_marking_points mp ON marking_point=mp.id INNER JOIN competition_teams ct ON ct.id=sr.team INNER JOIN clubs c ON c.id=ct.club INNER JOIN leagues l on l.id=ct.league WHERE mp.serc=? :league_conds: GROUP BY team, tid) SELECT *, (SELECT MAX(score) FROM tbl) AS max FROM tbl) AS t) AS f;");
    }

    public function getType()
    {
        return 'serc';
    }

    public function getMaxMark()
    {
        $result = DB::select(" SELECT SUM(weight*10) AS total FROM serc_marking_points WHERE serc=?;", [$this->id]);
        return $result[0]->total;
    }

    public function getCompetition()
    {
        return $this->hasOne(Competition::class, 'id', 'competition');
    }

    // STATS METHODS
    public function getMarkDistribution()
    {
        $dist = DB::select('SELECT sr.result, COUNT(sr.result) AS count FROM serc_results sr INNER JOIN serc_marking_points smp ON sr.marking_point=smp.id WHERE smp.serc=? GROUP BY sr.result ORDER BY result', [$this->id]);
        $result = array_map(function ($value) {
            return (array)$value;
        }, $dist);


        $labels = [];
        $values = [];

        foreach ($result as $res) {
            $labels[] = $res['result'];
            $values[] = $res['count'];
        }

        return [
            'labels' => $labels,
            'values' => $values
        ];
    }

    public function getRollingAverageForMP($mpId)
    {
        $rawMarks = DB::select('SELECT result AS count FROM serc_results sr INNER JOIN competition_teams ct ON sr.team=ct.id WHERE marking_point=? ORDER BY ct.serc_order', [$mpId]);
        $rollingMarks = DB::select('SELECT AVG(result) OVER (ORDER BY id ROWS BETWEEN 5 PRECEDING AND CURRENT ROW) AS count FROM (SELECT sr.id, result FROM serc_results sr INNER JOIN competition_teams ct ON sr.team=ct.id WHERE marking_point=? ORDER BY ct.serc_order) AS b;', [$mpId]);

        $rawMarks = array_map(function ($value) {
            return $value->count;
        }, $rawMarks);

        $rollingMarks = array_map(function ($value) {
            return $value->count;
        }, $rollingMarks);

        return [
            'labels' => range(1, count($rawMarks)),
            'raw' => $rawMarks,
            'rolling' => $rollingMarks
        ];
    }
}
