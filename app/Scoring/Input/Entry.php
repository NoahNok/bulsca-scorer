<?php

namespace App\Scoring\Input;

/**
 * Someone with a result in an Event: a team, a competitor or a Club. Plain data, read once from
 * the database, so scoring needs no queries.
 */
final class Entry
{
    /**
     * @param string $type Its morph type: `team`, `competitor` or `club`
     * @param ?int $clubId Its Club: a team's (-1 when it has none), a competitor's team's, or a Club itself
     * @param ?int $teamId Its team: a team itself or a competitor's; a Club has none
     * @param int[] $leagueIds The Leagues it is in, lowest id first
     * @param array<string, mixed> $attributes Its stored columns, which old formulas read as `item.entity.<column>`
     */
    public function __construct(
        public readonly int $id,
        public readonly string $type,
        public readonly ?int $clubId = null,
        public readonly ?int $teamId = null,
        public readonly array $leagueIds = [],
        private readonly array $attributes = [],
    ) {}

    public function key(): string
    {
        return "{$this->type}:{$this->id}";
    }

    public function inLeague(int $leagueId): bool
    {
        return in_array($leagueId, $this->leagueIds, true);
    }

    /**
     * The League a result is grouped under when an Event combines results by League
     */
    public function firstLeagueId(): ?int
    {
        return $this->leagueIds[0] ?? null;
    }

    /**
     * What a Results sheet puts on one row together: the id of the Entry's Club, team or first
     * League, or its own as a competitor. Null when it has none (a Club has no team).
     */
    public function groupId(string $by): ?int
    {
        return match ($by) {
            'club' => $this->clubId,
            'team' => $this->teamId,
            'competitor' => $this->type === 'competitor' ? $this->id : null,
            'league' => $this->firstLeagueId(),
        };
    }

    public function __get(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }
}
