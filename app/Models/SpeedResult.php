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
}
