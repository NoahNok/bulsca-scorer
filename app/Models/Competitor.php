<?php

namespace App\Models;

use App\DTO\EntityGrouping;
use App\Models\AbstractClasses\Entity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competitor extends Entity
{
    use HasFactory;


    protected $fillable = ['team', 'league', 'competition', 'name'];

    protected $with = ['getLeague', 'getTeam'];

    public function getFormattedName(?Competition $comp): string
    {
        // Implement the logic to return the competitor's name
        // For example, if there is a 'name' property:
        return $this->formatName($comp?->competitor_format);
    }

    public function formatName($format = ':C - :N - (:L)')
    {

        $targets = [':C', ':L', ':N', ':T', ':S'];
        $search = [];
        $replace = [];

        foreach ($targets as $target) {
            if (str_contains($format, $target)) {
                $search[] = $target;

                $value = match ($target) {
                    ':C' => $this->getTeam->getClub?->name ?? '-',
                    ':L' => $this->getLeague?->name ?? '-',
                    ':T' => $this->getTeam->team ?? '-',
                    ':N' => $this->name
                };

                $replace[] = $value;
            }
        }

        return str_replace($search, $replace, $format);
    }

    public function getTeam()
    {
        return $this->belongsTo(CompetitionTeam::class, 'team');
    }

    public function getGrouping(): EntityGrouping
    {

        $team = $this->getTeam;
        return new EntityGrouping($team->club, $team->id, $this->id, $this->league);
    }
}
