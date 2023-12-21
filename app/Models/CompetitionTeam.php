<?php

namespace App\Models;

use App\Traits\Cloneable;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionTeam extends Model
{
    use HasFactory, Cloneable;

    protected $fillable = ['club', 'team'];

    public function getClubName()
    {
        return $this->hasOne(Club::class, 'id', 'club')->first()->name;
    }


    public function getLeague()
    {
        return $this->hasOne(League::class, 'id', 'league');
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
