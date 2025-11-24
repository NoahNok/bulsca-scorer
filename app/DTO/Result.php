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

    public int $seed = 0;

    public int $total_marking_points = 1;

    /**
     * @param Collection<int, Disqualification> $disqualifications
     * @param Collection<int, Penalty> $penalties
     * @param Collection<int, Result> $combined
     */
    public function __construct(
        public int $id,
        public null|string|float $result,
        public Entity $entity,
        public Event $event,
        public ?Collection $disqualifications = null,
        public ?Collection $penalties = null,
        public ?Collection $combined = null
    ) {
        $this->disqualifications ??= new Collection();
        $this->penalties ??= new Collection();
        $this->combined ??= new Collection();
    }



    public function getPenaltiesString(): ?string
    {
        $pens = $this->penalties;


        if (count($pens) == 0) {
            return null;
        }

        // Count how many P900 there are and add on to the string with a multiplier if more than 1
        $penaltyMessage = "";

        $p900Count = $pens->where('code', '900')->count();
        if ($p900Count > 1) {
            $pens = $pens->filter(fn($pen) => $pen->code != '900');
            $pens->add("P900 (x$p900Count)");
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

    public function isCombined(): bool
    {
        return $this->combined->count() > 0;
    }

    public function isDNF(): bool
    {
        return $this->disqualifications->firstWhere('code', '99915') !== null && $this->disqualifications->count() == 1;
    }
}
