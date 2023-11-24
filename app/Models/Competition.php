<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Competition extends Model
{
    use HasFactory;

    public function speedEvents()
    {
        return $this->hasManyThrough(SpeedEvent::class, CompetitionSpeedEvent::class, 'event', 'id', 'id', 'event');
    }

    public function getSpeedEvents()
    {
        return $this->hasMany(CompetitionSpeedEvent::class, 'competition', 'id');
        //return $this->hasMany(CompetitionSpeedEvent::class, 'event', 'id');
    }

    public function getSERCs()
    {
        return $this->hasMany(SERC::class, 'competition', 'id');
        //return $this->hasMany(CompetitionSpeedEvent::class, 'event', 'id');
    }

    public function getCompetitionTeams()
    {
        return $this->hasMany(CompetitionTeam::class, 'competition', 'id')->orderBy('serc_order');
    }

    public function getHeatEntries()
    {
        return $this->hasMany(Heat::class, 'competition', 'id');
    }

    protected $casts = [
        'when' => 'datetime',
    ];

    public function getResultSchemas()
    {
        return $this->hasMany(ResultSchema::class, 'competition', 'id');
    }

    public function getAllEvents()
    {
        $sercs = $this->getSERCs()->get();
        $speeds = $this->getSpeedEvents()->get();

        $merged = $sercs->merge($speeds);

        return $merged;
    }

    public function getUser()
    {
        return $this->hasOne(User::class, 'competition', 'id');
    }

    public function areResultsPublic()
    {

        return $this->public_results;
    }


    public function areResultsProvisional()
    {

        return $this->results_provisional;
    }


    public function resultsSlug()
    {

        return Str::lower(str_replace(" ", "-", $this->name)) . "." . $this->id;
    }

    public function needsToRegenerateSERCDraw(): bool
    {
        return $this->getCompetitionTeams()->where('serc_order', 0)->exists();
    }

    public function getMaxHeats(): int
    {
        return $this->getHeatEntries->max('heat') ?: -1;
    }

    public function getMaxLanes(): int
    {
        return $this->max_lanes;
    }

    public function getSeason()
    {
        return $this->hasOne(Season::class, 'id', 'season');
    }

    public function getHeats()
    {
        return $this->hasMany(Heat::class, 'competition', 'id');
    }



    public function howManySercsHasEachTeamFinished()
    {


        $teamsFinished = [];

        foreach ($this->getCompetitionTeams as $team) {
            $finished = 0;
            foreach ($this->getSERCs as $serc) {
                if ($serc->hasTeamFinished($team)) $finished++;
            }

            $teamsFinished[$team->id] = $finished;
        }

        return $teamsFinished;
    }
}
