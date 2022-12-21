<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SERCMarkingPoint extends Model
{
    use HasFactory;

    protected $table = "serc_marking_points";




    public function getScoreForTeam($team)
    {
        return SERCResult::where('marking_point', $this->id)->where('team', $team)->first()?->result;
    }
}
