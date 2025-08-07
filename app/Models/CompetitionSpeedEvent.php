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

        $rankedResults = $resolvedResults->sortBy(function ($result) {
            return [
                count($result->disqualifications) > 0 ? 1 : 0,
                $result->resolvedResult
            ];
        })->values()->map(function ($result, $index) {
            return RankedResult::fromResolved($result, $index + 1);
        });

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

    public function getRawResults(): array
    {
        return $this->results->map(function ($result) {
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

    public function penalties()
    {
        return $this->morphMany(Penalty::class, 'event');
    }

    public function disqualifications()
    {
        return $this->morphMany(Disqualification::class, 'event');
    }

    public function addEntityPenalty(Entity $entity, int $code)
    {
        $this->penalties()->create([
            'entity' => $entity,
            'code' => $code
        ]);
    }

    public function addEntityDisqualification(Entity $entity, int $code)
    {

        $disqualification = $this->disqualifications()->make([
            'code' => $code
        ]);

        $disqualification->entity()->associate($entity);

        $disqualification->save();
    }

    public function clearEntityPenalties(Entity $entity)
    {
        $this->penalties()->whereMorphedTo('entity', $entity)->delete();
    }

    public function clearEntityDisqualifications(Entity $entity)
    {
        $this->disqualifications()->whereMorphedTo('entity', $entity)->delete();
    }

    public function getBaseEvent()
    {
        return $this->hasOne(SpeedEvent::class, 'id', 'event')->first();
    }
}
