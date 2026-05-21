<?php

namespace App\Models;

use App\Models\AbstractClasses\Entity;
use App\Models\SERC\MarkingPointTemplate;
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

    public function getJudge()
    {
        return $this->belongsTo(SERCJudge::class, 'judge', 'id');
    }

    public function template()
    {
        return $this->belongsTo(MarkingPointTemplate::class, 'marking_point_template_id');
    }

    public function minMaxAvg()
    {
        return SERCResult::query()->where('marking_point', $this->id)
            ->selectRaw('
                COALESCE(ROUND(MIN(result), 0), 0) as min_result,
                COALESCE(ROUND(AVG(result), 0), 0) as avg_result,
                COALESCE(ROUND(MAX(result), 0), 0) as max_result
            ')->first();
    }
}
