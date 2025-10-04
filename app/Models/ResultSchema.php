<?php

namespace App\Models;

use App\DTO\ScaledResult;
use App\Helpers\ClassHelpers;
use App\Models\Event\ScoringEngine;
use App\Models\Event\ScoringSchema;
use App\Models\ResultSchemas\ClubSERCResultSchema;
use App\Models\ResultSchemas\HighestSumResultSchema;
use App\Models\ResultSchemas\NationalsResultSchema;
use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ResultSchema extends Model
{
    use HasFactory, Cloneable;

    protected $casts = [
        'schema' => 'array',
    ];

    public function getResults(): Collection
    {

        $allResults = collect();

        $league = null;
        if (array_key_exists('league', $this->schema)) {
            $league = League::find($this->schema['league']);
        }

        $ignore_dq = $this->schema['ignore_dq'] ?? false;
        $group_on = $this->schema['group_on'] ?? 'team';
        $group_class = Relation::getMorphedModel($group_on);
        $rank_higher = $this->schema['rank_higher'] ?? true;

        $break_ties = $this->schema['break_ties'] ?? []; // Should be a list of event ids to break ties on, or empty if not breaking

        foreach ($this->getEvents as $event) {

            $scoringEngine = ScoringSchema::engine();

            $eventResults = null;


            $eventResults = collect($event->getActualEvent->getRankedResults($league));



            $eventResults = $eventResults->map(function ($result) {
                return ScaledResult::fromRanked($result, -1);
            });




            $eventResults = $scoringEngine->process($this->schema, $eventResults, 'adjustedPoints');

            // mutiply by event weight
            $eventResults = $eventResults->map(function ($result) use ($event, $ignore_dq) {
                $result->adjustedPoints = $result->adjustedPoints * $event->weight;

                if (!$ignore_dq && $result->isDisqualified()) {
                    $result->adjustedPoints = 0;
                }

                return $result;
            });


            $allResults = $allResults->merge($eventResults);
        }

        $grouped = $allResults->groupBy(function ($result) use ($group_on) {
            return $result->entity->getGrouping()->{"{$group_on}_id"};
        });


        $preRanked = $grouped->map(function ($results) use ($group_class) {
            $entity = $results->filter(function ($result) use ($group_class) {
                return $result->entity instanceof $group_class;
            })->first()->entity;
            $totalPoints = 0;
            foreach ($results as $result) {

                $totalPoints += $result->adjustedPoints;
            }
            return new \App\DTO\OverallResult(
                events: $results,
                entity: $entity,
                totalPoints: $totalPoints,
                position: 0
            );
        });




        $sortFunction = null;

        if ($break_ties) {
            $sortFunction = function ($result) use ($break_ties) {
                $options = [$result->totalPoints];

                foreach ($break_ties as $bt) {
                    $eventPosition = $result->events->where('event.id', $bt)->first()->position;
                    $options[] = $eventPosition;
                }

                return $options;
            };
        } else {
            $sortFunction = function ($result) {
                return [$result->totalPoints];
            };
        }



        if ($rank_higher) {
            $preRanked = $preRanked->sortByDesc(fn($result) => $sortFunction($result))->values();
        } else {
            $preRanked = $preRanked->sortBy(fn($result) => $sortFunction($result))->values();
        }





        $rankedResults = collect();
        $previousScore = null;
        $previousRank = 0;
        $position = 1;

        foreach ($preRanked as $result) {
            $currentScore = $result->totalPoints;

            if (!$break_ties && $currentScore === $previousScore) {
                $rank = $previousRank;
            } else {
                $rank = $position;
                $previousScore = $currentScore;
                $previousRank = $rank;
            }

            $result->position = $rank;
            $rankedResults->push($result);
            $position++;
        }

        return $rankedResults;
    }



    public function getEvents()
    {
        return $this->hasMany(ResultSchemaEvent::class, 'schema', 'id');
    }

    public function getCompetition()
    {
        return $this->hasOne(Competition::class, 'id', 'competition');
    }
}
