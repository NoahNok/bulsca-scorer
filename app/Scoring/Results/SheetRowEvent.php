<?php

namespace App\Scoring\Results;

/**
 * What one Event gave a row of a Results sheet: the Entry whose result it is (a member of the row
 * when the sheet groups Entries), its place and points in the Event, and what it counts for here.
 */
final class SheetRowEvent
{
    /**
     * @param string $eventType `speed` or `serc`
     * @param ?float $points The Event's own points
     * @param float $adjustedPoints After the sheet's Formula and the Event's weight
     */
    public function __construct(
        public readonly string $eventType,
        public readonly int $eventId,
        public readonly string $eventName,
        public readonly EntryRef $entry,
        public readonly int $position,
        public readonly ?float $points,
        public readonly float $adjustedPoints,
    ) {}

    /**
     * `id:type`, which is how the public pages key a sheet's Event columns
     */
    public function eventKey(): string
    {
        return "{$this->eventId}:{$this->eventType}";
    }
}
