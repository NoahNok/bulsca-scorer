<?php

namespace App\Models\DigitalJudge;

use App\Models\CompetitionTeam;
use App\Models\SERCJudge;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JudgeNote extends Model
{
    use HasFactory;

    protected $table = "digitaljudge_judge_notes";

    public function getJudge()
    {
        return $this->hasOne(SERCJudge::class, 'id', 'judge');
    }

    public function getTeam()
    {
        return $this->hasOne(CompetitionTeam::class, 'id', 'team');
    }
}
