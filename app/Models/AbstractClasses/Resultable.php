<?php

namespace App\Models\AbstractClasses;


use App\DTO\Result;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Event\Penalty;
use App\Models\Event\Disqualification;

abstract class Resultable extends Loggable
{

    public function scopeForEntity(Builder $query, Model $entity): Builder
    {
        return $query->where('entity_type', get_class($entity))
            ->where('entity_id', $entity->getKey());
    }


    public abstract function transformToResult(): Result;

    public abstract function penalties();
    public abstract function disqualifications();
}
