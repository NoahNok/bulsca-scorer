<?php

namespace App\Models;

use App\DTO\ResolvedResult;
use App\DTO\Result;
use App\Models\AbstractClasses\Event;
use App\Models\Interfaces\IEvent;
use App\Models\Interfaces\IPenalisable;
use App\Models\Scoring\Bulsca\BulscaSpeedScoring;
use App\Models\Scoring\IScoring;
use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CompetitionSpeedEvent extends Event implements IPenalisable
{
    use HasFactory, Cloneable;

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
            if ($result->disqualification) {
                $resolvedResult = 0;
            }

            $resolvedResults[] = new ResolvedResult(
                $result->id,
                $resolvedResult,
                $result->result,
                $result->entity,
                $result->event,
                $result->disqualification,
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

    public function addTeamPenalty($teamId, $code)
    {
        $result = SpeedResult::where('event', $this->id)->where('competition_team', $teamId)->first();
        $penalty = new Penalty();
        $penalty->speed_result = $result->id;
        $penalty->code = $code;
        $penalty->save();
    }

    public function addTeamDQ($teamId, $code)
    {
        $result = SpeedResult::where('event', $this->id)->where('competition_team', $teamId)->first();
        $result->disqualification = $code;
        $result->save();
    }

    public function getBaseEvent()
    {
        return $this->hasOne(SpeedEvent::class, 'id', 'event')->first();
    }
}
