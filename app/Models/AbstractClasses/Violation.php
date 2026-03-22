<?php

namespace App\Models\AbstractClasses;

use App\Models\DigitalJudge\JudgeDQSubmission;
use App\Models\Event\Disqualification;
use App\Traits\RecordActivity;
use Illuminate\Database\Eloquent\Model;

abstract class Violation extends Model
{
    use RecordActivity;

    protected $fillable = ['entity', 'code', 'event'];

    protected static function boot()
    {

        parent::boot();

        static::created(function ($model) {
            $model->triggerResultCacheClear();
        });

        static::updated(function ($model) {
            $model->triggerResultCacheClear();
        });

        static::deleted(function ($model) {
            $model->triggerResultCacheClear();
        });
    }

    public function activity(string $activity): string
    {
        if ($this instanceof Disqualification) {
            return "DISQUALIFICATION_{$activity}";
        } else {
            return "PENALTY_{$activity}";
        }
    }

    protected static function booted()
    {
        static::created(function (Violation $violation) {
            $entity = $violation->entity;
            $event = $violation->event;

            $related = [$entity, $event, $event->getCompetition, $violation];
            if ($violation->submission) {
                $related[] = $violation->submission;
            }


            $violation->reportActivity("APPLIED");
        });

        static::deleted(function (Violation $violation) {
            $entity = $violation->entity;
            $event = $violation->event;

            $related = [$entity, $event, $event->getCompetition, $violation];
            if ($violation->submission) {
                $related[] = $violation->submission;
            }


            $violation->reportActivity("DELETED");
        });
    }

    private function triggerResultCacheClear()
    {
        $this->entity->getCompetition->clearResultSchemaCaches();
    }

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

    // make this take an organisation param (optional) so that we get the right pen/dq message for the current org
    abstract public function getMessage(): string;

    public function reportActivity(string $activity, ?JudgeDQSubmission $submission = null): void
    {
        $entity = $this->entity;
        $event = $this->event;

        $related = [$entity, $event, $event->getCompetition, $this];
        if ($submission) {
            $related[] = $submission;
        }

        $description = match ($activity) {
            'APPROVED' => "{$this} for {$entity->getName()} in {$event->getName()} given by {$submission->name} ({$submission->position}) was approved by the Referee",
            'APPLIED' => "{$this} given to {$entity->getName()} for {$event->getName()}",
            'APPEALED' => "{$this} for {$entity->getName()} in {$event->getName()} was appealed by {$submission->name} ({$submission->position})",
            'REMOVED' => "{$this} for {$entity->getName()} in {$event->getName()} was removed by the Referee",
            default => "{$activity} activity for {$this} on {$entity->getName()} in {$event->getName()}",
        };

        $this->recordActivity($this->activity($activity), $description, context: ['code' => "{$this}"], related: $related);
    }
}
