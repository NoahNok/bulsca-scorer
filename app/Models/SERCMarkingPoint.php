<?php

namespace App\Models;

use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SERCMarkingPoint extends Model
{
    use HasFactory, Cloneable;

    protected $table = "serc_marking_points";




    public function getScoreForTeam($team)
    {

        $mpId = $this->id;

        return Cache::rememberForever('mp.' . $mpId . '.team.' . $team, function () use ($team, $mpId) {
            return SERCResult::where('marking_point', $mpId)->where('team', $team)->first()?->result;
        });
    }
}
