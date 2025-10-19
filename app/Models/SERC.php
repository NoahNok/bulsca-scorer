<?php

namespace App\Models;

use App\DTO\RankedResult;
use App\DTO\ResolvedResult;
use App\DTO\Result;
use App\DTO\SERCResult as DTOSERCResult;
use App\Models\AbstractClasses\Entity;
use App\Models\SERCResult;
use App\Models\AbstractClasses\Event;
use App\Models\DigitalJudge\JudgeNote;
use App\Models\Event\Disqualification;
use App\Models\Event\Penalty;
use App\Models\Event\ScoringEngine;
use App\Models\Event\ScoringSchema;
use App\Models\Interfaces\IEvent;
use App\Models\Interfaces\IPenalisable;
use App\Models\Orders\Draw;
use App\Models\Scoring\Bulsca\BulscaSercScoring;
use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SERC extends Event
{
    use HasFactory, Cloneable;

    protected $table = 'sercs';

    public function getRankedResults(?League $league = null): array
    {
        if (!$this->scoringSchema) {
            return [];
        }

        $resolvedResults = collect($this->getResolvedResults(league: $league));
        $scoringSchema = $this->scoringSchema;
        $rankHigher = $scoringSchema->schema['rank_higher'] ?? true; // If we are ranking based on highest score or not
        $rankEquation = $scoringSchema->schema['rank_equation'] ?? null;
        $allowDisqualifiedToRank = $scoringSchema->schema['allow_disqualified_to_rank'] ?? false;


        $rankFunction = null;

        if ($allowDisqualifiedToRank) {
            $rankFunction = function ($result) use ($rankHigher) {
                return $result->points * ($rankHigher ? -1 : 1);
            };
        } else {
            $rankFunction = function ($result) use ($rankHigher) {
                return [
                    count($result->disqualifications) > 0 ? 1 : 0,
                    $result->points * ($rankHigher ? -1 : 1)
                ];
            };
        }


        $sortedResults = $scoringSchema->applyToResults($resolvedResults)->sortBy(function ($result) use ($rankFunction) {
            return $rankFunction($result);
        })->values();

        $rankedResults = collect();
        $previousScore = null;
        $previousRank = 0;
        $position = 1;

        foreach ($sortedResults as $result) {
            $currentScore = count($result->disqualifications) > 0 ? 'DQ' : $result->points;

            if ($currentScore === $previousScore) {
                $rank = $previousRank;
            } else {
                $rank = $position;
                $previousScore = $currentScore;
                $previousRank = $rank;
            }

            $rankedResults->push(RankedResult::fromResolved($result, $rank));
            $position++;
        }

        if ($rankEquation) {
            $totalResults = count($sortedResults);

            $rankEngine = new ScoringEngine();
            $rankedResults->each(function ($rankedResult) use ($rankEngine, $totalResults, $rankEquation) {
                $rankedResult->position = $rankEngine->evaluateInstance($rankEquation, $rankedResult, ['teams' => $totalResults, 'rank' => $rankedResult->position]);
            });
        }


        return $rankedResults->toArray();
    }

    public function getResolvedResults(?League $league = null): array
    {

        if (!$this->scoringSchema) {
            return [];
        }

        /**
         * @param ResolvedResult[] $resolvedResults
         */
        $resolvedResults = collect([]);
        $results = collect($this->getRawResults(league: $league));

        $penalties = $this->penalties()->get();
        $disqualifications = $this->disqualifications()->get();



        foreach ($results->groupBy(fn($result) => $result->entity->id) as $id => $entityResults) {

            $resultTotal = $entityResults->reduce(fn($acc, $result) => $acc += $result->result * $result->markingPoint->weight, 0);
            $resultData = $entityResults->first();

            $resolvedResults[] = new Result(
                $resultData->id,
                $resultTotal,
                $resultData->entity,
                $resultData->event,
                $disqualifications->where('entity_id', $id),
                $penalties->where('entity_id', $resultData->entity->id)
            );
        }

        $resolvedResults = $this->scoringSchema->applyViolations($resolvedResults);

        return $this->applyGrouping($resolvedResults)->toArray();
    }

    public function getRawResults(bool $withEmpty = false, ?League $league = null): array
    {
        $query = $this->results();

        if ($league !== null) {
            $query = $query->whereHas('entity', function ($q) use ($league) {
                $q->where('league', $league->id);
            });
        }

        $query = $query->with(['entity', 'getMarkingPoint', 'getMarkingPoint.getJudge']);

        $results = $query->get();

        if ($this->scorable_entity == 'team') {

            $entityIds = $results->pluck('entity')->filter()->unique('id')->pluck('id');

            $loadedEntities = CompetitionTeam::whereIn('id', $entityIds)->with('getCompetitors')->get();

            $teamMap = $loadedEntities->keyBy('id');


            $results->each(function ($draw) use ($teamMap) {

                $draw->entity = $teamMap[$draw->entity->id];
            });
        }



        return $results->map(function ($result) {
            return $result->transformToResult();
        })->toArray();
    }

    public function results()
    {
        return $this->hasManyThrough(SERCResult::class, SERCMarkingPoint::class, 'serc', 'marking_point', 'id', 'id');
    }

    public function draw()
    {
        // if using seperate draws per SERC, this is where that would be handled
        return $this->hasMany(Draw::class, 'serc');
    }

    public function getDraw()
    {
        // if using seperate draws per SERC, this is where that would be handled

        // OTHERWISE USE DRAW ROM SERC WITH LOWEST ID
        return SERC::where('competition', $this->competition)->orderBy('id')->first()->draw()->with('entity')->orderBy('draw');
    }

    public function getPositionInDraw(Entity $entity)
    {
        $draw = $this->getDraw()->whereMorphedTo('entity', $entity)->first();

        $use_tanks = $this->getCompetition->getScoringSettings->use_tanks;

        if (!$draw) {
            return -1;
        }

        if ($use_tanks) {
            return "Tank {$draw->tank}-{$draw->draw}";
        }

        return $draw->draw;
    }



    public function getTankDraw()
    {
        $comp = $this->getCompetition;

        return $this->draw()->with('entity')->orderBy('tank')->orderBy('draw')->get()->map(function ($draw) use ($comp) {
            return [
                'id' => $draw->id,
                'tank' => $draw->tank,
                'draw' => $draw->draw,
                'entity_name' => $draw->entity->getName($comp),
            ];
        })->groupBy('tank');
    }

    public function getJudges()
    {
        return $this->hasMany(SERCJudge::class, 'serc', 'id');
    }


    public function getName(): string
    {
        return $this->name;
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

    public function getNotesForEntity(Entity $entity)
    {
        $allJudgeIds = $this->getJudges()->pluck('id')->toArray();

        return JudgeNote::whereIn('judge', $allJudgeIds)->whereMorphedTo('entity', $entity)->get();
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

    public function getSERCData()
    {

        $comp = $this->getCompetition;

        $dbData = DB::select('SELECT j.name AS judge_name, smp.name AS mp_name, smp.weight AS mp_weight , sr.marking_point AS mp_id, sr.entity_type, sr.entity_id, result FROM serc_results sr INNER JOIN serc_marking_points smp ON smp.id=sr.marking_point INNER JOIN serc_judges j ON j.id=smp.judge WHERE smp.serc=? ORDER BY j.id,smp.id;', [$this->id]);


        $judges = [];
        $results = [];



        foreach ($dbData as $row) {

            if (!in_array($row->mp_id, $judges[$row->judge_name] ?? [])) {
                $judges[$row->judge_name][$row->mp_id]['name'] = $row->mp_name;
                $judges[$row->judge_name][$row->mp_id]['weight'] = $row->mp_weight;
            }

            $results[$row->entity_id]['results'][$row->mp_id] = $row->result;
        }

        $placeResults = $this->getRankedResults();


        foreach ($placeResults as $placeResult) {
            $results[$placeResult->entity->id]['place'] = $placeResult->position;
            $results[$placeResult->entity->id]['points'] = $placeResult->points;




            $results[$placeResult->entity->id]['team'] = $placeResult->entity->getName($comp);
            $results[$placeResult->entity->id]['raw'] = $placeResult->resolvedResult;
            $results[$placeResult->entity->id]['tid'] = $placeResult->entity->id;
            $results[$placeResult->entity->id]['disqualification'] = "{$placeResult->disqualifications->first()}";
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
