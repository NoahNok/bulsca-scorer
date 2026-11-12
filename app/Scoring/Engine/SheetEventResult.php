<?php

namespace App\Scoring\Engine;

use App\Scoring\Input\Entry;
use App\Scoring\Input\SheetEvent;
use Illuminate\Support\Collection;

/**
 * One Entry's result in one of a Results sheet's Events, and what the sheet's Formula reads as
 * `item`: the Event's scored result (`result`, `resolvedResult`, `points`, `position`, `seed`,
 * `penalties`, `disqualifications`, `entity`, `isDisqualified()`, `isDNF()`, `hasPenalties()`,
 * `isCombined()`), the `event` it is from, and `adjustedPoints`, which is what it counts for in
 * the sheet.
 *
 * It is a copy: the scored result it was made from is shared between every sheet that includes the
 * Event, and is not changed. A sheet does not see which results were combined into one.
 */
final class SheetEventResult
{
    public int $id;

    public Entry $entity;

    public null|string|float $result;

    public float $resolvedResult;

    public ?float $points;

    public int $position;

    public int $seed;

    public int $total_marking_points = 1;

    /** @var Collection<int, ViolationCode> */
    public Collection $penalties;

    /** @var Collection<int, ViolationCode> */
    public Collection $disqualifications;

    /** @var Collection<int, ScoredResult> Always empty */
    public Collection $combined;

    /** What the sheet's Formula gave it, then multiplied by the Event's weight; -1 until then */
    public float $adjustedPoints = -1;

    public function __construct(public readonly ScoredResult $scored, public readonly SheetEvent $event)
    {
        $this->id = $scored->id;
        $this->entity = $scored->entity;
        $this->result = $scored->result;
        $this->resolvedResult = $scored->resolvedResult;
        $this->points = $scored->points;
        $this->position = $scored->position;
        $this->seed = $scored->seed;
        $this->penalties = $scored->penalties;
        $this->disqualifications = $scored->disqualifications;
        $this->combined = collect();
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
        return false;
    }

    /**
     * Did Not Finish, and nothing else
     */
    public function isDNF(): bool
    {
        return $this->disqualifications->firstWhere('code', '99915') !== null && $this->disqualifications->count() == 1;
    }
}
