<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Heat extends Model
{
    use HasFactory;

    public function getTeam()
    {
        return $this->hasOne(CompetitionTeam::class, 'id', 'team');
    }
}
