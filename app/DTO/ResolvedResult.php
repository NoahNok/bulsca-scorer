<?php

namespace App\DTO;

use App\Models\AbstractClasses\Entity;
use App\Models\AbstractClasses\Event;

/**
 * Represents a result that has been transformed into its final value based on any applied
 * disqualifications or penalties from the given scoring setup
 * 
 * Stores the same data as Result but with an additional resolvedResult value
 */
class ResolvedResult extends Result
{

    /**
     * @param int[] $penalties
     */
    public function __construct(
        public int $id,
        public string|int $resolvedResult,
        public string|int $result,
        public Entity $entity,
        public Event $event,
        public ?int $disqualification = null,
        public array $penalties = [],
    ) {
        parent::__construct($id, $result, $entity, $event, $disqualification, $penalties);
    }
}
