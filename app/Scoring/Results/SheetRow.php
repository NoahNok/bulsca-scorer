<?php

namespace App\Scoring\Results;

/**
 * One row of a Results sheet as pages read it: who it is listed under, what each Event gave it,
 * the total and its place.
 */
final class SheetRow
{
    /**
     * @param SheetRowEvent[] $events In the sheet's Event order
     */
    public function __construct(
        public readonly EntryRef $entry,
        public readonly array $events,
        public readonly float $totalPoints,
        public readonly int $position,
    ) {}
}
