<?php

namespace App\Models;

use App\DTO\RankedResult;
use App\DTO\ResolvedResult;
use App\DTO\Result;
use App\Models\AbstractClasses\Entity;
use App\Models\AbstractClasses\Event;
use App\Models\Event\Disqualification;
use App\Models\Event\Penalty;
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
        $resolvedResults = collect($this->getResolvedResults(league: $league));


        $scoringSchema = $this->scoringSchema;
        // $scoringSchema = ScoringSchema::where('name', 'BULSCA Rope Throw')->first();

        $sortedResults = $scoringSchema->applyToResults($resolvedResults)->sortBy(function ($result) {
            return [
                count($result->disqualifications) > 0 ? 1 : 0,
                -$result->points
            ];
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


        return $rankedResults->toArray();
    }

    public function getResolvedResults(?League $league = null): array
    {
        /**
         * @param ResolvedResult[] $resolvedResults
         */
        $resolvedResults = [];
        $results = collect($this->getRawResults(league: $league));

        return $this->scoringSchema->applyViolations($results)->toArray();
    }

    public function getRawResults(bool $withEmpty = false, ?League $league = null): array
    {

        $query = $this->results();

        if (!$withEmpty) {

            $query = $query->whereNotNull('result');
        }

        if ($league !== null) {
            $query = $query->whereHas('entity', function ($q) use ($league) {
                $q->where('league', $league->id);
            });
        }

        return $query->get()->map(function ($result) {
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

    public function heats()
    {
        return $this->hasMany(Heat::class, 'speed_event');
    }

    public function getHeats()
    {
        return CompetitionSpeedEvent::where('competition', $this->competition)->orderBy('id')->first()->heats();
    }

    public function getMaxHeats(): int
    {
        return $this->heats()->max('heat') ?? 1;
    }

    public function seeds()
    {
        return $this->hasMany(EntityEventSeed::class, 'speed_event');
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
