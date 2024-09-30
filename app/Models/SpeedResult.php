<?php

namespace App\Models;

use App\Models\AbstractClasses\Loggable;
use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpeedResult extends Loggable
{
    use HasFactory, Cloneable;

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

        if ($this->getEvent->getName() == "Rope Throw") {
            if ($this->result < 4) {
                return $this->result;
            }
        }

        // Otherwise we have a time in milliseconds that needs to be shown in the format xx:xx.xxx
        $minutes = floor($this->result / 60000);
        $seconds = floor(($this->result - ($minutes * 60000)) / 1000);
        $milliseconds = $this->result - ($minutes * 60000) - ($seconds * 1000);

        $result = str_pad($minutes, 2, "0", STR_PAD_LEFT) . ":" . str_pad($seconds, 2, "0", STR_PAD_LEFT) . "." . str_pad($milliseconds, 3, "0", STR_PAD_LEFT);

        return $result;
    }


    static function prettyTime($result)
    {

        if ($result < 4) {
            return $result;
        } else {

            $mins = floor($result / 60000);
            $secs = ($result - $mins * 60000) / 1000;

            return sprintf('%02d', $mins) . ':' . str_pad(number_format($secs, 3, '.', ''), 6, '0', STR_PAD_LEFT);
        }
    }

    public function getJudgeLogTitle()
    {
        return "Speed: {judge} marked {team} for {event}";
    }

    public function getJudgeLogDescription()
    {
        return "Result: " . $this->prettyTime($this->result) . " | Penalties: " . (strlen($this->getPenaltiesAsString()) > 0 ? $this->getPenaltiesAsString() : '-') . " | DQ: " . ($this->disqualification ?? '-');
    }

    public function resolveJudgeLogTeam(): ?CompetitionTeam
    {
        return $this->getTeam;
    }

    public function resolveJudgeLogName()
    {
        return $this->getEvent->getName();
    }

    public function resolveJudgeLogAssociation()
    {
        return $this->getEvent;
    }

    public static function getPrettyTime($time)
    {


        $minutes = floor($time / 60000);
        $seconds = floor(($time - ($minutes * 60000)) / 1000);
        $milliseconds = $time - ($minutes * 60000) - ($seconds * 1000);

        $result = str_pad($minutes, 2, "0", STR_PAD_LEFT) . ":" . str_pad($seconds, 2, "0", STR_PAD_LEFT) . "." . str_pad($milliseconds, 3, "0", STR_PAD_LEFT);
        return $result;
    }

    public static function remapDq($dq)
    {
        return match ($dq) {
            'DQ015' => 'DNF',
            'DQ004' => 'DNS',
            'DQ1001' => 'OOT',
            default => $dq,
        };
    }
}
