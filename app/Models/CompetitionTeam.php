<?php

namespace App\Models;

use App\Data\TeamAdditionalDetailsData;
use App\DTO\EntityGrouping;
use App\Helpers\ClassHelpers;
use App\Models\AbstractClasses\Entity;
use App\Traits\Cloneable;
use App\Traits\MorphableModel;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionTeam extends Entity
{
    use HasFactory, Cloneable, MorphableModel;

    protected $fillable = ['club', 'team', 'league', 'competition'];

    protected $with = ['getClub', 'getLeague'];

    public function getName(?Competition $comp): string
    {


        return $this->formatName($comp?->team_format);
    }

    public function getClubName()
    {
        return $this->hasOne(Club::class, 'id', 'club')->first()?->name ?? '';
    }

    public function getClub()
    {
        return $this->hasOne(Club::class, 'id', 'club');
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

    public function formatName($format = ':C - :N - (:S)')
    {
        $targets = [':C', ':L', ':N', ':R', ':S'];
        $search = [];
        $replace = [];

        foreach ($targets as $target) {
            if (str_contains($format, $target)) {
                $search[] = $target;

                $value = match ($target) {
                    ':C' => fn() => $this->getClub?->name ?? '-',
                    ':L' => fn() => $this->getLeague?->name ?? '-',
                    ':N' => fn() => $this->team ?? '-',
                    ':R' => fn() => $this->getClub?->region ?? '-',
                    ':S' => fn() => $this->getCompetitors->pluck('name')->implode(', ')
                };

                $replace[] = $value();
            }
        }

        return str_replace($search, $replace, $format);
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

    public function getGrouping(): EntityGrouping
    {
        return new EntityGrouping($this->getClub->id, $this->id, null, $this->league);
    }

    public function getCompetitors()
    {
        return $this->hasMany(Competitor::class, 'team');
    }
}
