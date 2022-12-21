<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SERC extends Model
{
    use HasFactory;

    protected $table = 'sercs';

    public function getJudges()
    {
        return $this->hasMany(SERCJudge::class, 'serc', 'id');
    }

    public function getTeams()
    {
        return CompetitionTeam::where('competition', $this->competition)->get();
    }
}
