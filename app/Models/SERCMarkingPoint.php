<?php

namespace App\Models;

use App\Models\AbstractClasses\Entity;
use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SERCMarkingPoint extends Model
{
    use HasFactory, Cloneable;

    protected $table = "serc_marking_points";


    public function serc()
    {
        return $this->belongsTo(SERC::class, 'serc', 'id');
    }


    public function getScoreForTeam(Entity $entity)
    {

        $mpId = $this->id;



        //return Cache::rememberForever('mp.' . $mpId . '.team.' . $team->id, function () use ($team, $mpId) {
        return SERCResult::where('marking_point', $mpId)->forEntity($entity)->first()?->result ?: null;
        //});
    }

    
    public function getLastUpdatedForTeam(Entity $entity)
    {

        $mpId = $this->id;

        return SERCResult::where('marking_point', $mpId)->forEntity($entity)->first()?->updated_at ?: null;
    }


    public function getJudge()
    {
        return $this->belongsTo(SERCJudge::class, 'judge', 'id');
    }
}
