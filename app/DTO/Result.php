<?php

namespace App\DTO;

use App\Models\AbstractClasses\Entity;
use App\Models\AbstractClasses\Event;
use App\Models\Event\Penalty;
use App\Models\Event\Disqualification;
use App\Models\SpeedResult;

class Result
{

    /**
     * @param Penalty[] $penalties
     * @param Disqualification[] $disqualifications
     */
    public function __construct(
        public int $id,
        public string|int $result,
        public Entity $entity,
        public Event $event,
        public array $disqualifications = [],
        public array $penalties = [],
    ) {}


    public function getPenaltiesString(): string
    {
        return "pens";
    }

    public function getDisqualificationsString(): string
    {

        $dqs = $this->disqualifications;
        if (count($dqs) == 0) {
            return "-";
        }

        return SpeedResult::remapDq($dqs[0]['code']);
    }
}
