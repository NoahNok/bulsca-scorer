<?php

namespace App\Scoring\Input;

/**
 * One Entry's result in an Event, before any scoring: its time or its Marks summed by marking point
 * weight, and the penalties and disqualifications it was given.
 */
final class EventRow
{
    /**
     * @param int $id The stored result's id (for a SERC, the Entry's first Mark)
     * @param null|string|float $result As stored: a speed event's time is a string of milliseconds
     * @param array<int|string> $penalties Penalty codes, in the order they were given
     * @param array<int|string> $disqualifications Disqualification codes, in the order they were given
     * @param int $seed The Entry's seed time, 0 without one
     * @param int $markingPoints How many Marks were summed (1 for a speed event)
     */
    public function __construct(
        public readonly int $id,
        public readonly Entry $entry,
        public readonly null|string|float $result,
        public readonly array $penalties = [],
        public readonly array $disqualifications = [],
        public readonly int $seed = 0,
        public readonly int $markingPoints = 1,
    ) {}
}
