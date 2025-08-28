<?php

namespace App\DTO;

use App\Models\AbstractClasses\Entity;
use App\Models\AbstractClasses\Event;
use App\Models\Event\Penalty;
use App\Models\Event\Disqualification;
use Illuminate\Database\Eloquent\Collection;

/**
 * Represents a result that has been transformed into its final value based on any applied
 * disqualifications or penalties from the given scoring setup
 * 
 * Stores the same data as Result but with an additional resolvedResult value
 */
class ScaledResult extends RankedResult
{

    /**
     * @param Collection<int, Disqualification> $disqualifications
     * @param Collection<int, Penalty> $penalties
     */
    public function __construct(
        public int $id,
        public float $resolvedResult,
        public string|float|null $result,
        public Entity $entity,
        public Event $event,
        public int $position,
        public float $adjustedPoints,
        public ?float $points,
        public ?Collection $disqualifications = null,
        public ?Collection $penalties = null,
    ) {
        $this->disqualifications ??= new Collection();
        $this->penalties ??= new Collection();
        parent::__construct($id, $resolvedResult, $result, $entity, $event, $position, $points, $disqualifications, $penalties);
    }

    public static function fromRanked(RankedResult $resolved, float $adjustedPoints): RankedResult
    {
        return new ScaledResult(
            $resolved->id,
            $resolved->resolvedResult,
            $resolved->result,
            $resolved->entity,
            $resolved->event,
            $resolved->position,
            $adjustedPoints,
            $resolved->points,
            $resolved->disqualifications,
            $resolved->penalties
        );
    }
}
