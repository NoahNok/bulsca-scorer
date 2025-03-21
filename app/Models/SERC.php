<?php

namespace App\Models;

use App\Models\DigitalJudge\JudgeNote;
use App\Models\Interfaces\IEvent;
use App\Models\Interfaces\IPenalisable;
use App\Models\Scoring\Bulsca\BulscaSercScoring;
use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SERC extends IEvent implements IPenalisable
{
    use HasFactory, Cloneable;

    protected $table = 'sercs';




    public function getJudges()
    {
        return $this->hasMany(SERCJudge::class, 'serc', 'id');
    }

    public function getTeams()
    {



        return match ($this->getCompetition->scoring_type) {
            'bulsca', 'rlss-cs' => CompetitionTeam::where('competition', $this->competition)->orderBy('serc_order')->get(),
            'rlss-nationals' => Competitor::where('competition', $this->competition)->orderBy('serc_order')->get()->unique('club'),
        };
    }

    public function getName(): string
    {
        return $this->name;
    }



    public function getTeamDQ(CompetitionTeam $team)
    {
        return SERCDisqualification::where(['team' => $team->id, 'serc' => $this->id])->first();
    }

    public function getTeamPenalties(CompetitionTeam $team)
    {
        return SERCPenalty::where(['team' => $team->id, 'serc' => $this->id])->first();
    }


    public function getType(): string
    {
        return 'serc';
    }

    public function getMaxMark()
    {
        $result = DB::select(" SELECT SUM(weight*10) AS total FROM serc_marking_points WHERE serc=? AND weight>0;", [$this->id]);
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

    public function getSERCData()
    {

        $dbData = DB::select('SELECT j.name AS judge_name, smp.name AS mp_name, smp.weight AS mp_weight , sr.marking_point AS mp_id, sr.team, result FROM serc_results sr INNER JOIN serc_marking_points smp ON smp.id=sr.marking_point INNER JOIN serc_judges j ON j.id=smp.judge WHERE smp.serc=? ORDER BY j.id,smp.id;', [$this->id]);


        $judges = [];
        $results = [];



        foreach ($dbData as $row) {

            if (!in_array($row->mp_id, $judges[$row->judge_name] ?? [])) {
                $judges[$row->judge_name][$row->mp_id]['name'] = $row->mp_name;
                $judges[$row->judge_name][$row->mp_id]['weight'] = $row->mp_weight;
            }

            $results[$row->team]['results'][$row->mp_id] = $row->result;
        }

        $placeResults = $this->getResults();


        foreach ($placeResults as $placeResult) {
            $results[$placeResult->tid]['place'] = $placeResult->place;
            $results[$placeResult->tid]['points'] = $placeResult->points;

            $results[$placeResult->tid]['team'] = $placeResult->team;
            $results[$placeResult->tid]['raw'] = $placeResult->score;
            $results[$placeResult->tid]['tid'] = $placeResult->tid;
            $results[$placeResult->tid]['disqualification'] = $placeResult->disqualification;
        }


        // Remove any results that don't have a place key
        // Usually occurs when filtering by bracket
        $results = array_filter($results, function ($item) {
            return array_key_exists('place', $item);
        });

        usort($results, function ($item1, $item2) {
            return $item1['place'] > $item2['place'];
        });



        return compact('judges', 'results');
    }


    public function getOverallJudgeNotes()
    {
        $notes = DB::select('SELECT ojn.note AS note, j.name AS judge FROM overall_judge_notes ojn INNER JOIN serc_judges j ON j.id=ojn.judge WHERE j.serc=?;', [$this->id]);

        return $notes;
    }
}
