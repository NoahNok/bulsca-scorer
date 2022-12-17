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
}
