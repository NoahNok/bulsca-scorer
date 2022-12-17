<?php

namespace App\Models;

use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionTeam extends Model
{
    use HasFactory;



    public function getClubName()
    {
        return $this->hasOne(Club::class, 'id', 'club')->first()->name;
    }

    public function getLeague()
    {
        return $this->belongsToMany(League::class, 'competition_teams_league', 'competition_team', 'league');
    }

    public function getSwimTowTime()
    {
        return CarbonInterval::second($this->st_time)->cascade()->forHumans(true);
    }

    public function getSwimTowTimeForDefault()
    {
        return gmdate("i:s", $this->st_time);
    }

    public function getFullname()
    {
        return $this->getClubName() . " " . $this->team;
    }
}
