<?php

namespace App\Models\AbstractClasses;


use App\Scoring\ResultsVersion;
use App\DigitalJudge\DigitalJudge;
use App\Models\Competition;
use App\Models\CompetitionTeam;

use App\Models\Event\Disqualification;
use App\Models\Event\Penalty;
use App\Models\Event\ScoringSchema;
use App\Models\Interfaces\IJsonable;
use App\Models\League;
use App\Models\SERCResult;
use App\Models\SpeedResult;
use App\Scoring\CompetitionResults;
use App\Traits\RecordActivity;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Override;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

abstract class Event extends Model implements IJsonable
{

    use CascadesDeletes, RecordActivity;
    protected $cascadeDeletes = [
        'penalties',
        'disqualifications'
    ];

    public abstract function getName(): string;
    public abstract function results();
    public abstract function getCompetition();
    public function isComplete($no_check = False): bool
    {
        if ($no_check) {
            return $this->completed;
        }

        $complete = $this->checkCompletion();

        $this->completed = $complete;
        $this->save();

        return $complete;
    }
    public abstract function checkCompletion(): bool;

    protected static function booted()
    {

        static::created(function (Event $event) {
            $event->recordActivity('EVENT_CREATED', 'Event \'' . $event->getName() . '\' created', related: [$event, $event->getCompetition]);
        });


        static::deleted(function (Event $event) {
            $event->recordActivity('EVENT_DELETED', 'Event \'' . $event->getName() . '\' deleted', related: [$event->getCompetition]);

            // Its cached results have no version to outlive, so they go with it
            CompetitionResults::forget($event);
        });

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



    public function addEntityPenalty(Entity $entity, int $code): Penalty
    {
        $penalty = $this->penalties()->make([
            'code' => $code
        ]);

        $penalty->entity()->associate($entity);

        $penalty->save();

        return $penalty;
    }

    public function addEntityDisqualification(Entity $entity, int $code): Disqualification
    {

        $disqualification = $this->disqualifications()->make([
            'code' => $code
        ]);

        $disqualification->entity()->associate($entity);

        $disqualification->save();
        return $disqualification;
    }

    public function clearEntityPenalties(Entity $entity)
    {
        $this->penalties()->whereMorphedTo('entity', $entity)->delete();
        // Deleted without model events
        app(ResultsVersion::class)->bump($this->competition, $this->getConnection());
    }

    public function clearEntityDisqualifications(Entity $entity)
    {
        $this->disqualifications()->whereMorphedTo('entity', $entity)->delete();
        // Deleted without model events
        app(ResultsVersion::class)->bump($this->competition, $this->getConnection());
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

    public function getScorableEntity(): Entity
    {
        return new (Relation::getMorphedModel($this->scorable_entity));
    }

    public function hide()
    {
        $this->viewable = !$this->viewable;
        $this->save();
    }

    /**
     * The competition's Entities of the kind this event scores
     */
    public function scorableEntities()
    {
        return $this->getScorableEntity()::where('competition', $this->competition);
    }

    public function getScorableEntities()
    {
        return $this->scorableEntities()->get();
    }

    /**
     * One of the competition's Entities of the kind this event scores, or a 404
     */
    public function findScorableEntity(int $id): Entity
    {
        return $this->scorableEntities()->findOrFail($id);
    }

    #[Override]
    public function jsonable(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->getName(),
            'type' => $this->getType()
        ];
    }
}
