<?php

namespace App\Models\DigitalJudge;

use App\Models\Competition;
use App\Models\Heat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JudgeDQSubmission extends Model
{
    use HasFactory;

    protected $table = "judge_dq_submissions";

    protected $guarded = [];

    public function getEvent()
    {
        return $this->morphTo(__FUNCTION__, 'event_type', 'event_id');
    }

    public function getHeat()
    {
        return $this->hasOne(Heat::class, 'id', 'heat_lane');
    }

    public function getCompetition()
    {
        return $this->hasOne(Competition::class, 'id', 'competition');
    }
}
