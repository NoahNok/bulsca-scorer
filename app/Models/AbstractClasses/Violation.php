<?php

namespace App\Models\AbstractClasses;

use App\Models\DigitalJudge\JudgeDQSubmission;
use Illuminate\Database\Eloquent\Model;

abstract class Violation extends Model
{

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
}
