<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return $this->hasMany(CompetitionTeam::class, 'competition', 'id');
    }

    public function getResultSchemas()
    {
        return $this->hasMany(ResultSchema::class, 'competition', 'id');
    }
}
