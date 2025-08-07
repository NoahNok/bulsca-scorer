<?php

namespace App\DTO;

use App\Models\AbstractClasses\Entity;
use App\Models\AbstractClasses\Event;
use App\Models\SERCMarkingPoint;
use App\Models\Event\Penalty;
use App\Models\Event\Disqualification;

class SERCResult extends Result
{

    /**
     * @param Penalty[] $penalties
     * @param Disqualification[] $disqualifications
     */
    public function __construct(
        public int $id,
        public string|int $result,
        public SERCMarkingPoint $markingPoint,
        public Entity $entity,
        public Event $event,
        public array $disqualifications = [],
        public array $penalties = [],
    ) {
        parent::__construct($id, $result, $entity, $event, $disqualifications, $penalties);
    }
}
