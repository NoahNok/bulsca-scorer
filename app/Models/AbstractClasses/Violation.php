<?php

namespace App\Models\AbstractClasses;

use App\DigitalJudge\DigitalJudge;
use App\DTO\Result;
use App\DTO\ResolvedResult;
use App\DTO\RankedResult;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\DigitalJudge\BetterJudgeLog;
use App\Models\DigitalJudge\JudgeDQSubmission;
use App\Models\SERCResult;
use App\Models\SpeedResult;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

abstract class Violation extends Model
{

    protected $fillable = ['entity', 'code', 'event'];

    public function entity()
    {
        return $this->morphTo();
    }

    public function event()
    {
        return $this->morphTo();
    }

    public function __toString(): string
    {
        return "OVERRIDE ME";
    }

    public function submission()
    {
        return $this->morphOne(JudgeDQSubmission::class, 'violation');
    }

    abstract public function getMessage(): string;
}
