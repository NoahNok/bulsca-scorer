<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpeedResult extends Model
{
    use HasFactory;

    public function getTeam()
    {
        return $this->belongsTo(CompetitionTeam::class, 'competition_team', 'id');
    }

    public function getPenalties()
    {
        return $this->hasMany(Penalty::class, 'speed_result', 'id');
    }

    public function getPenaltiesAsString()
    {
        return $this->getPenalties()->get('code')->implode('code', ', ');
    }


    public function getEvent()
    {
        return $this->belongsTo(CompetitionSpeedEvent::class, 'event', 'id');
    }

    public function getResultAsString()
    {
        $result = "";
        if ($this->getEvent->getName() == "Rope Throw") {
            if ($this->result < 4) {
                return $result;
            }
        }

        // Otherwise we have a time in milliseconds that needs to be shown in the format xx:xx.xxx
        $minutes = floor($this->result / 60000);
        $seconds = floor(($this->result - ($minutes * 60000)) / 1000);
        $milliseconds = $this->result - ($minutes * 60000) - ($seconds * 1000);

        $result = str_pad($minutes, 2, "0", STR_PAD_LEFT) . ":" . str_pad($seconds, 2, "0", STR_PAD_LEFT) . "." . str_pad($milliseconds, 3, "0", STR_PAD_LEFT);

        return $result;
    }
}
