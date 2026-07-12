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


    public function clubs()
    {
        return $this->hasMany(Club::class, 'league');
    }

    public function teams()
    {
        return $this->hasMany(CompetitionTeam::class, 'league');
    }

    public function competitors()
    {
        return $this->hasMany(Competitor::class, 'league');
    }

    public function entityCount(): array
    {
        return [
            'teams' => $this->teams()->count(),
            'competitors' => $this->competitors()->count()
        ];
    }
}
