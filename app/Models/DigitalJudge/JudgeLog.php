<?php

namespace App\Models\DigitalJudge;

use App\Models\CompetitionTeam;
use App\Models\SERCJudge;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JudgeLog extends Model
{
    use HasFactory;

    protected $table = "judging_log";

    public function getJudge()
    {
        return $this->belongsTo(SERCJudge::class, 'judge', 'id');
        //return $this->hasOne(SERCJudge::class, 'id', 'judge');
    }

    public function getTeam()
    {
        return $this->hasOne(CompetitionTeam::class, 'id', 'team');
    }
}
