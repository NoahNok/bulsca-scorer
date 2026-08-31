<?php

namespace App\Models;

use App\Models\AbstractClasses\Entity;
use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    use HasFactory, Cloneable;

    protected $fillable = ['name'];




    public function teams()
    {
        return $this->morphedByMany(
            CompetitionTeam::class,
            'entity',
            'leagueables',
            'league_id',   // League's key on pivot
            'entity_id'    // Organisation's key on pivot
        );
    }

    public function competitors()
    {
        return $this->morphedByMany(Competitor::class, 'entity', 'leagueables', 'league_id', 'entity_id');
    }

    public function entityCount(): array
    {
        return [
            'teams' => $this->teams()->count(),
            'competitors' => $this->competitors()->count()
        ];
    }

    public function restrictedJudges()
    {
        return $this->belongsToMany(
            SERCJudge::class,
            'serc_judge_league_restriction',
            'league_id',
            'judge_id'
        );
    }
}
