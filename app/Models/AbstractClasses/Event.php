<?php

namespace App\Models\AbstractClasses;

use App\DigitalJudge\DigitalJudge;
use App\DTO\Result;
use App\DTO\ResolvedResult;
use App\DTO\RankedResult;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\DigitalJudge\BetterJudgeLog;
use App\Models\Event\Disqualification;
use App\Models\Event\Penalty;
use App\Models\Event\ScoringSchema;
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
    public abstract function getRawResults(bool $withEmpty = false): array;
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

    protected static function booted()
    {
        static::deleting(function (Event $event) {
            $event->penalties()->delete();
            $event->disqualifications()->delete();
        });
    }

    public function penalties()
    {
        return $this->morphMany(Penalty::class, 'event');
    }

    public function disqualifications()
    {
        return $this->morphMany(Disqualification::class, 'event');
    }



    public function addEntityPenalty(Entity $entity, int $code)
    {
        $penalty = $this->penalties()->make([
            'code' => $code
        ]);

        $penalty->entity()->associate($entity);

        $penalty->save();
    }

    public function addEntityDisqualification(Entity $entity, int $code)
    {

        $disqualification = $this->disqualifications()->make([
            'code' => $code
        ]);

        $disqualification->entity()->associate($entity);

        $disqualification->save();
    }

    public function clearEntityPenalties(Entity $entity)
    {
        $this->penalties()->whereMorphedTo('entity', $entity)->delete();
    }

    public function clearEntityDisqualifications(Entity $entity)
    {
        $this->disqualifications()->whereMorphedTo('entity', $entity)->delete();
    }

    public function getEntityPenalties(Entity $entity)
    {
        return $this->penalties()->whereMorphedTo('entity', $entity);
    }

    public function getEntityDisqualifications(Entity $entity)
    {
        return $this->disqualifications()->whereMorphedTo('entity', $entity);
    }

    public function scoringSchema()
    {
        return $this->belongsTo(ScoringSchema::class, 'scoring_schema');
    }
}
