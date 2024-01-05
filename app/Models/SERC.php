<?php

namespace App\Models;

use App\Models\DigitalJudge\JudgeNote;
use App\Models\Interfaces\IPenalisable;
use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SERC extends Model implements IPenalisable
{
    use HasFactory, Cloneable;

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
        $rollingMarks = DB::select('SELECT AVG(result) OVER (ORDER BY serc_order ROWS BETWEEN 5 PRECEDING AND CURRENT ROW) AS count FROM (SELECT sr.id, result, serc_order FROM serc_results sr INNER JOIN competition_teams ct ON sr.team=ct.id WHERE marking_point=? ORDER BY ct.serc_order) AS b;', [$mpId]);

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

    public function getNotesForTeam(CompetitionTeam $team)
    {
        $allJudgeIds = $this->getJudges()->pluck('id')->toArray();

        return JudgeNote::whereIn('judge', $allJudgeIds)->where('team', $team->id)->get();
    }

    public function hasTeamFinished($team)
    {
        $c = DB::select('SELECT COUNT(*) AS count FROM serc_results INNER JOIN serc_marking_points smp ON smp.id=marking_point WHERE team=? AND serc=?', [$team->id, $this->id]);
        return $c[0]->count > 0;
    }

    public function getAverageTimeBetweenTeams()
    {

        //$res = DB::select('SELECT TIMESTAMPDIFF(SECOND, MIN(team_min), MAX(team_min))/(GREATEST((COUNT(team_min) - 1),1)) AS avg_time FROM (SELECT sr.team, MIN(sr.created_at) as team_min FROM serc_results sr INNER JOIN serc_marking_points smp ON smp.id=sr.marking_point WHERE smp.serc=? GROUP BY sr.team) AS t;', [$this->id]);

        // This new query takes into account larger outliers in seconds above the below threshold
        $outlierThreshold = 541; // Query use <, so this means any team time diff > 12m is an outlier
        $res = DB::select('WITH base AS (SELECT team, sr.created_at, serc, ROW_NUMBER() OVER (PARTITION BY smp.id) AS rn FROM serc_results sr INNER JOIN serc_marking_points smp ON sr.marking_point=smp.id WHERE serc=?) (SELECT SUM(IF(btw<?,btw,0))/GREATEST(COUNT(IF(btw<?,1,NULL)),1) AS avg_time FROM (SELECT TIMESTAMPDIFF(SECOND, b1.created_at, b2.created_at) AS btw FROM base b1 INNER JOIN base b2 ON b1.rn=b2.rn-1) AS t);', [$this->id, $outlierThreshold, $outlierThreshold]);


        $avgTime = $res[0]->avg_time;


        if ($avgTime <= 0) {
            // Try again with a bigger outlier thresh
            $res = DB::select('WITH base AS (SELECT team, sr.created_at, serc, ROW_NUMBER() OVER (PARTITION BY smp.id) AS rn FROM serc_results sr INNER JOIN serc_marking_points smp ON sr.marking_point=smp.id WHERE serc=?) (SELECT SUM(IF(btw<?,btw,0))/GREATEST(COUNT(IF(btw<?,1,NULL)),1) AS avg_time FROM (SELECT TIMESTAMPDIFF(SECOND, b1.created_at, b2.created_at) AS btw FROM base b1 INNER JOIN base b2 ON b1.rn=b2.rn-1) AS t);', [$this->id, $outlierThreshold * 2, $outlierThreshold * 2]);
            $avgTime = $res[0]->avg_time;

            if ($avgTime < 0) {
                $avgTime = 360;
            }
        }

        return $avgTime == 0 ? 360 : $avgTime;
    }

    public function getDataAsJson()
    {

        $data = [];
        $teams = [];
        $judges = [];

        foreach ($this->getJudges as $judge) {
            $judges[] = [
                'id' => $judge->id,
                'name' => $judge->name,
                'marking_points' => $judge->getMarkingPoints->toArray()
            ];
        }

        foreach ($this->getTeams() as $team) {
            $teams[] = [
                'name' => $team->getFullname(),
                'id' => $team->id,

            ];
        }

        usort($teams, function ($item1, $item2) {
            return $item2['name'] <= $item1['name'];
        });

        foreach ($this->getJudges as $judge) {
            foreach ($judge->getMarkingPoints as $mp) {
                foreach (SERCResult::where(['marking_point' => $mp->id])->get() as $result) {
                    $data[$mp->id][$result->team] = [
                        'result' => (int) $result->result,
                        'id' => $result->id
                    ];
                }
            }
        }


        return ['judges' => $judges, 'teams' => $teams, 'data' => $data];
    }


    public function addTeamPenalty($teamId, $code)
    {
        $penalty = SERCPenalty::firstOrNew(['team' => $teamId, 'serc' => $this->id]);

        $codes = explode(",", $penalty->codes);
        $codes[] = $code;
        $penalty->codes = implode(",", $codes);

        $penalty->save();
    }

    public function addTeamDQ($teamId, $code)
    {
        $dq = SERCDisqualification::firstOrNew(['team' => $teamId, 'serc' => $this->id]);
        $dq->code = $code;
        $dq->save();
    }
}
