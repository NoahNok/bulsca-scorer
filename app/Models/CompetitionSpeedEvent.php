<?php

namespace App\Models;

use App\DTO\RankedResult;
use App\DTO\ResolvedResult;
use App\DTO\Result;
use App\Models\AbstractClasses\Entity;
use App\Models\AbstractClasses\Event;
use App\Models\Event\Disqualification;
use App\Models\Event\Penalty;

use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompetitionSpeedEvent extends Event
{
    use HasFactory, Cloneable;

    public function getRankedResults(): array
    {
        $resolvedResults = collect($this->getResolvedResults());

        $sortedResults = $resolvedResults->sortBy(function ($result) {
            return [
                count($result->disqualifications) > 0 ? 1 : 0,
                $result->resolvedResult
            ];
        })->values();

        $rankedResults = collect();
        $previousScore = null;
        $previousRank = 0;
        $position = 1;

        foreach ($sortedResults as $result) {
            $currentScore = count($result->disqualifications) > 0 ? 'DQ' : $result->resolvedResult;

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

        return $rankedResults->toArray();
    }

    public function getResolvedResults(): array
    {
        /**
         * @param ResolvedResult[] $resolvedResults
         */
        $resolvedResults = [];
        $results = collect($this->getRawResults());

        foreach ($results as $result) {
            $resolvedResult = $result->result;

            // Apply DQ/Pen. Apply DQ last as it overrides
            if (count($result->disqualifications) > 0) {
                $resolvedResult = 0;
            }

            $resolvedResults[] = new ResolvedResult(
                $result->id,
                $resolvedResult,
                $result->result,
                $result->entity,
                $result->event,
                $result->disqualifications,
                $result->penalties
            );
        }

        return $resolvedResults;
    }

    public function getRawResults(bool $withEmpty = false): array
    {

        $query = $this->results;

        if (!$withEmpty) {

            $query = $query->whereNotNull('result');
        }

        return $query->map(function ($result) {
            return $result->transformToResult();
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

    public function getTeams()
    {
        return $this->getCompetition->getCompetitionTeams();
    }


    // CHANGE TO BE A EVENT BASED TOGGLE
    public function hasPenalties()
    {
        return $this->hasOne(SpeedEvent::class, 'id', 'event')->first()->has_penalties;
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
}
