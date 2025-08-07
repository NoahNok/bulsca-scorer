<?php

namespace App\Models\AbstractClasses;

use App\DTO\DQ;
use App\DTO\Pen;
use App\DTO\Result;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class Resultable extends Loggable
{

    public function scopeForEntity(Builder $query, Model $entity): Builder
    {
        return $query->where('entity_type', get_class($entity))
            ->where('entity_id', $entity->getKey());
    }


    public abstract function getDisqualification(): ?DQ;
    /**
     * @return Pen[]
     */
    public abstract function getPenalties(): array;
    public abstract function transformToResult(): Result;
}
