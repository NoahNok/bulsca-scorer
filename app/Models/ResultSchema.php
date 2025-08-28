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

        foreach ($this->getEvents as $event) {

            $scoringEngine = ScoringSchema::engine();


            $eventResults = collect($event->getActualEvent->getRankedResults());

            $eventResults = $eventResults->map(function ($result) {
                return ScaledResult::fromRanked($result, -1);
            });




            $eventResults = $scoringEngine->process($this->schema, $eventResults, 'adjustedPoints');

            // mutiply by event weight
            $eventResults = $eventResults->map(function ($result) use ($event) {
                $result->adjustedPoints = $result->adjustedPoints * $event->weight;

                if ($result->isDisqualified()) {
                    $result->adjustedPoints = 0;
                }

                return $result;
            });







            $allResults = $allResults->merge($eventResults);
        }



        $grouped = $allResults->groupBy(function ($result) {
            return $result->entity->id;
        });

        $preRanked = $grouped->map(function ($results, $entityId) {
            $entity = $results->first()->entity;
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

        $preRanked = $preRanked->sortByDesc(fn($result) => $result->totalPoints)->values();

        $rankedResults = collect();
        $previousScore = null;
        $previousRank = 0;
        $position = 1;

        foreach ($preRanked as $result) {
            $currentScore = $result->totalPoints;

            if ($currentScore === $previousScore) {
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
