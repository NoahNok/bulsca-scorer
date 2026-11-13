<?php

namespace App\Scoring\Results;

/**
 * Who a row of results belongs to: a team, a competitor or a Club, with its name as the
 * competition shows it. Plain data, so a row can be cached and shown without reading its model.
 */
final class EntryRef
{
    /**
     * @param string $type Its morph type: `team`, `competitor` or `club`
     * @param ?int $clubId The Club it counts for on a Master sheet: its own as a Club, a team's, or a competitor's team's
     * @param int[] $leagueIds The Leagues it is in, lowest id first
     * @param ?string $teamLetter A team's letter (the A of "Bristol A")
     */
    public function __construct(
        public readonly string $type,
        public readonly int $id,
        public readonly string $name,
        public readonly ?int $clubId = null,
        public readonly array $leagueIds = [],
        public readonly ?string $teamLetter = null,
    ) {}

    /**
     * `type:id`, which is how pages key their rows
     */
    public function key(): string
    {
        return "{$this->type}:{$this->id}";
    }
}
