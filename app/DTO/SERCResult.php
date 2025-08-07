<?php

namespace App\DTO;

use App\Models\AbstractClasses\Entity;
use App\Models\AbstractClasses\Event;
use App\Models\SERCMarkingPoint;

class SERCResult extends Result
{

    /**
     * @param int[] $penalties
     */
    public function __construct(
        public int $id,
        public string|int $result,
        public SERCMarkingPoint $markingPoint,
        public Entity $entity,
        public Event $event,
        public ?int $disqualification,
        public array $penalties = [],
    ) {
        parent::__construct($id, $result, $entity, $event, $disqualification, $penalties);
    }
}
