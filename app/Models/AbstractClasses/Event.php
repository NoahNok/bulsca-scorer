<?php

namespace App\Models\AbstractClasses;

use App\DigitalJudge\DigitalJudge;
use App\DTO\Result;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\DigitalJudge\BetterJudgeLog;
use App\Models\SERCResult;
use App\Models\SpeedResult;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

abstract class Event extends Model
{

    public abstract function getName(): string;
    /**
     * @return Result[]
     */
    public abstract function getRawResults(): array;
    public abstract function getResolvedResults(): array;
    public abstract function results();
    public abstract function getCompetition();
}
