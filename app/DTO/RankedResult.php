<?php

namespace App\DTO;

use App\Models\AbstractClasses\Entity;
use App\Models\AbstractClasses\Event;
use App\Models\Event\Penalty;
use App\Models\Event\Disqualification;

/**
 * Represents a result that has been transformed into its final value based on any applied
 * disqualifications or penalties from the given scoring setup
 * 
 * Stores the same data as Result but with an additional resolvedResult value
 */
class RankedResult extends Result
{

    /**
     * @param Penalty[] $penalties
     * @param Disqualification[] $disqualifications
     */
    public function __construct(
        public int $id,
        public int $resolvedResult,
        public string|int $result,
        public Entity $entity,
        public Event $event,
        public int $position,
        public float $points = 0.0,
        public array $disqualifications = [],
        public array $penalties = [],
    ) {
        parent::__construct($id, $result, $entity, $event, $disqualifications, $penalties);
    }

    public static function fromResolved(ResolvedResult $resolved, int $position, float $points = 0.0): RankedResult
    {
        return new RankedResult(
            $resolved->id,
            $resolved->resolvedResult,
            $resolved->result,
            $resolved->entity,
            $resolved->event,
            $position,
            $points,
            $resolved->disqualifications,
            $resolved->penalties
        );
    }
}
