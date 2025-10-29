<?php

namespace App\Models\AbstractClasses;


use App\DTO\Result;
use App\Models\Competition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Event\Penalty;
use App\Models\Event\Disqualification;

abstract class Resultable extends Loggable
{

    public function scopeForEntity(Builder $query, Entity $entity): Builder
    {
        return $query->where('entity_type', $entity->getMorphClass())
            ->where('entity_id', $entity->getKey());
    }

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($model) {
            $model->getCompetition->clearResultSchemaCaches();
        });
    }


    public abstract function transformToResult(): Result;

    public abstract function penalties();
    public abstract function disqualifications();
    public abstract function getCompetition();
}
