<?php

namespace App\Models;

use App\Models\AbstractClasses\Entity;
use App\Models\AbstractClasses\Event;
use App\Models\Event\Disqualification;
use App\Models\Event\Penalty;
use App\Models\Event\ScoringSchema;
use App\Models\Orders\EntityEventSeed;
use App\Models\Orders\Heat;
use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Override;

class CompetitionSpeedEvent extends Event
{
    use HasFactory, Cloneable;

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

    public function getMaxHeats(?int $pool = null): int
    {
        return $this->getHeats()->inPool($pool)->max('heat') ?? 1;
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

    #[Override]
    public function jsonable(): array
    {
        $data = parent::jsonable();
        $data['max_lanes'] = $this->getCompetition()->value('max_lanes');

        return $data;
    }
}
