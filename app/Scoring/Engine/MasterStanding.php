<?php

namespace App\Scoring\Engine;

/**
 * One row of a Master sheet: a Club, what it takes from each Results sheet, their total and its
 * place.
 */
final class MasterStanding
{
    public int $position = 0;

    /**
     * @param array<int, mixed> $sheetResults By sheet id: the Club's value in the sheet, or the default
     */
    public function __construct(
        public readonly int $clubId,
        public readonly array $sheetResults,
        public readonly float $total,
    ) {}
}
