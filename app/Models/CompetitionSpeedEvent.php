<?php

namespace App\Models;

use App\DTO\RankedResult;
use App\DTO\ResolvedResult;
use App\DTO\Result;
use App\Models\AbstractClasses\Entity;
use App\Models\AbstractClasses\Event;
use App\Models\Event\Disqualification;
use App\Models\Event\Penalty;
use App\Models\Event\ScoringEngine;
use App\Models\Event\ScoringSchema;
use App\Models\Orders\EntityEventSeed;
use App\Models\Orders\Heat;
use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompetitionSpeedEvent extends Event
{
    use HasFactory, Cloneable;

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
            $currentScore = (count($result->disqualifications) > 0 && !$allowDisqualifiedToRank) ? 'DQ' : $result->points;

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
        /**
         * @param ResolvedResult[] $resolvedResults
         */
        $resolvedResults = [];
        $results = collect($this->getRawResults(league: $league));

        $resolvedResults = $this->scoringSchema->applyViolations($results);



        return $this->applyGrouping($resolvedResults)->toArray();
    }

    public function getRawResults(bool $withEmpty = false, ?League $league = null): array
    {

        $query = $this->results();

        if (!$withEmpty) {

            $query = $query->whereNotNull('result');
        }

        if ($league !== null) {
            $query = $query->whereHas('entity', function ($q) use ($league) {

                $q->whereHas('leagues', function ($qq) use ($league) {
                    $qq->where('leagues.id', $league->id);
                });
            });
        }

        $query = $query->with(['entity', 'getEvent']);


        $penalties = $this->penalties()->get();
        $disqualifications = $this->disqualifications()->get();

        $seeds = $this->getCompetition->getSpeedEvents->first()->seeds()->with('entity')->get();
        $usingSeeds = $seeds->count() > 0;


        return $query->get()->map(function ($result) use ($penalties, $disqualifications, $seeds, $usingSeeds) {
            $r = $result->transformToResult();

            if ($usingSeeds) {
                $r->seed = $seeds->first(function ($item) use ($r) {
                    return $item->entity->id == $r->entity->id;
                })?->seed ?? 0;
            }

            $r->penalties = $penalties->where('entity_id', $result->entity->id);
            $r->disqualifications = $disqualifications->where('entity_id', $result->entity->id);

            return $r;
        })->toArray();
    }

    public function results()
    {
        return $this->hasMany(SpeedResult::class, 'event');
    }


    public function getName(): string
    {
        return $this->hasOne(SpeedEvent::class, 'id', 'event')->first()->name;
    }

    public function getCompetition()
    {
        return $this->belongsTo(Competition::class, 'competition', 'id');
    }

    public function heats()
    {
        return $this->hasMany(Heat::class, 'speed_event');
    }

    public function getHeats()
    {

        if ($this->getCompetition->heats_per_event) {
            return $this->heats();
        }

        return CompetitionSpeedEvent::where('competition', $this->competition)->orderBy('id')->first()->heats();
    }

    public function getMaxHeats(): int
    {
        return $this->getHeats()->max('heat') ?? 1;
    }

    public function seeds()
    {
        return $this->hasMany(EntityEventSeed::class, 'speed_event');
    }


    // CHANGE TO BE A EVENT BASED TOGGLE
    public function hasPenalties()
    {
        return $this->has_penalties;
    }


    public function getType(): string
    {
        return 'speed';
    }

    public function getDataAsJson()
    {
        $data = [];


        foreach ($this->getSimpleResults as $result) {
            $team = ['name' => $result->getTeam->getFullname(), 'id' => $result->id, 'result' => $result->getResultAsString(), 'disqualification' => $result->disqualification, 'penalties' => $result->getPenaltiesAsString()];
            $data[] = $team;
        }

        return $data;
    }



    public function getBaseEvent()
    {
        return $this->hasOne(SpeedEvent::class, 'id', 'event')->first();
    }

    public function checkCompletion(): bool
    {
        $results = $this->results()->whereNotNull('result')->count();
        $possibleResults = $this->getScorableEntities()->count();

        return $results >= $possibleResults;
    }
}
