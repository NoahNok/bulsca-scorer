<?php

namespace App\Scoring\Input;

/**
 * One of the Events a Results sheet adds up, and how much it counts for
 */
final class SheetEvent
{
    /**
     * @param string $type `EventInput::SPEED` or `EventInput::SERC`
     * @param float $weight What the Event's points are multiplied by, after the sheet's Formula
     */
    public function __construct(
        public readonly string $type,
        public readonly int $id,
        public readonly float $weight = 1,
    ) {}

    /**
     * Whether a stored tie-break is this Event. Tie-breaks were once bare ids, which can't tell a
     * speed event from a SERC with the same id, so those match on the id alone.
     *
     * @param int|string|array{type: string, id: int|string} $tieBreak
     */
    public function isTieBreak(int|string|array $tieBreak): bool
    {
        if (!is_array($tieBreak)) {
            return $this->id == $tieBreak;
        }

        return $tieBreak['type'] === $this->type && $this->id == $tieBreak['id'];
    }
}
