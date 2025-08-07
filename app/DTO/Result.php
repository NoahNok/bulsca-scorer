<?php

namespace App\DTO;

use App\Models\AbstractClasses\Entity;
use App\Models\AbstractClasses\Event;

class Result
{

    /**
     * @param int[] $penalties
     */
    public function __construct(
        public int $id,
        public string|int $result,
        public Entity $entity,
        public Event $event,
        public ?int $disqualification = null,
        public array $penalties = [],
    ) {}
}
