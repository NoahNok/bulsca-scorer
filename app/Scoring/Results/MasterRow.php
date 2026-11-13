<?php

namespace App\Scoring\Results;

/**
 * One row of a Master sheet as pages read it: a Club, what it takes from each Results sheet, the
 * total and its place.
 */
final class MasterRow
{
    /**
     * @param array<int, mixed> $sheetResults By sheet id
     */
    public function __construct(
        public readonly EntryRef $club,
        public readonly array $sheetResults,
        public readonly float $total,
        public readonly int $position,
    ) {}
}
