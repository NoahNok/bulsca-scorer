<?php

namespace App\Scoring\Engine;

use App\Scoring\Input\Entry;

/**
 * One row of a Results sheet: an Entry (or the first of the Entries grouped into the row), its
 * result in each Event, their total and its place.
 */
final class SheetStanding
{
    public int $position = 0;

    /**
     * @param Entry $entity Who the row is listed under
     * @param SheetEventResult[] $events In the order they were added up
     */
    public function __construct(
        public readonly Entry $entity,
        public readonly array $events,
        public readonly float $totalPoints,
    ) {}

    /**
     * The row's place in an Event that breaks ties, or null when it has no result in one
     *
     * @param int|string|array{type: string, id: int|string} $tieBreak
     */
    public function placeIn(int|string|array $tieBreak): ?int
    {
        foreach ($this->events as $result) {
            if ($result->event->isTieBreak($tieBreak)) {
                return $result->position;
            }
        }

        return null;
    }
}
