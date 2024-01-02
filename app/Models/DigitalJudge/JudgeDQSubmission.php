<?php

namespace App\Models\DigitalJudge;

use App\Models\AbstractClasses\Loggable;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\Heat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JudgeDQSubmission extends Loggable
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

    public function getJudgeLogTitle()
    {
        return "DQ/Penalty: {judge} submitted a DQ/Penalty for {team} in {event}";
    }

    public function getJudgeLogDescription()
    {
        return $this->code . " | Status: " . ($this->resolved === null ? "Pending" : ($this->resolved ? "Accepted" : "Rejected"));
    }

    public function resolveJudgeLogTeam(): CompetitionTeam
    {
        return $this->getHeat->getTeam;
    }

    public function resolveJudgeLogName()
    {
        return $this->getEvent->getName();
    }

    public function resolveJudgeLogAssociation()
    {
        return $this;
    }
}
