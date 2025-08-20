<?php

namespace App\DTO;

use App\Models\AbstractClasses\Entity;
use App\Models\AbstractClasses\Event;
use App\Models\Event\Penalty;
use App\Models\Event\Disqualification;
use App\Models\SpeedResult;
use Illuminate\Database\Eloquent\Collection;

class Result
{

    /**
     * @param Collection<int, Disqualification> $disqualifications
     * @param Collection<int, Penalty> $penalties
     */
    public function __construct(
        public int $id,
        public null|string|int $result,
        public Entity $entity,
        public Event $event,
        public ?Collection $disqualifications = null,
        public ?Collection $penalties = null,
    ) {
        $this->disqualifications ??= new Collection();
        $this->penalties ??= new Collection();
    }


    public function getPenaltiesString(): ?string
    {
        $pens = $this->penalties;


        if (count($pens) == 0) {
            return null;
        }

        return $pens->map(fn($pen) => "$pen")->implode(', ');
    }

    public function getDisqualificationsString(): ?string
    {

        $dqs = $this->disqualifications;
        if (count($dqs) == 0) {
            return null;
        }

        $dq = $dqs->first();

        if ($this->event->getType() == 'speed') {
            return SpeedResult::remapDq($dq->code);
        }

        return $dq;
    }

    public function isDisqualified(): bool
    {
        return $this->disqualifications->count() > 0;
    }

    public function hasPenalties(): bool
    {
        return $this->penalties->count() > 0;
    }
}
