<?php

namespace App\Models;

use App\Data\TeamAdditionalDetailsData;
use App\Helpers\ClassHelpers;
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

    public function getClub()
    {
        return $this->hasOne(Club::class, 'id', 'club');
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

    public function formatName($format = ':C :N (:S)')
    {

        if ($this->getCompetition->scoring_type == 'rlss-nationals' && $format == ':C :N (:S)') {
            $format = ":N - :C (:R) - :L";
        }

        return str_replace([":C", ":L", ":N", ":S", ":R"], [$this->getClub->name, $this->getLeague->name, $this->team, $this->getSwimTowTimeForDefault(), $this->getClub->region], $format);
    }

    public function getCompetition()
    {
        return $this->belongsTo(Competition::class, 'competition');
    }

    public function getPositionInDraw()
    {
        $drawOrder = $this->getCompetition->getCompetitionTeams; // getCompetitionTeams() is ordered by the serc draw

        $id = $this->id;

        $position = $drawOrder->search(function ($team) use ($id) {
            return $team->id === $id;
        }) + 1;

        return $position;
    }

    public function asCompetitior()
    {
        return ClassHelpers::castToClass($this, Competitor::class);
    }
}
