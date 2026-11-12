<?php

namespace App\Scoring\Input;

/**
 * Everything needed to score one Event: its formula and every Entry's result. A League's results
 * are scored from the same input, by leaving out the rows of Entries that aren't in it.
 */
final class EventInput
{
    public const SPEED = 'speed';
    public const SERC = 'serc';

    /**
     * @param EventRow[] $rows In the order they are scored, which decides how exact ties are listed
     */
    public function __construct(
        public readonly string $type,
        public readonly int $id,
        public readonly Formula $formula,
        public readonly array $rows,
    ) {}
}
