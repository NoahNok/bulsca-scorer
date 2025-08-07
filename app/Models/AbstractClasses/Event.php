<?php

namespace App\Models\AbstractClasses;

use App\DigitalJudge\DigitalJudge;
use App\DTO\Result;
use App\DTO\ResolvedResult;
use App\DTO\RankedResult;
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
    /**
     * @return ResolvedResult[]
     */
    public abstract function getResolvedResults(): array;
    /**
     * @return RankedResult[]
     */
    public abstract function getRankedResults(): array;
    public abstract function results();
    public abstract function getCompetition();

    public abstract function penalties();
    public abstract function disqualifications();
    public abstract function addEntityPenalty(Entity $entity, int $code);
    public abstract function addEntityDisqualification(Entity $entity, int $code);
    public abstract function clearEntityPenalties(Entity $entity);
    public abstract function clearEntityDisqualifications(Entity $entity);
}
