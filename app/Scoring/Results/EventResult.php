<?php

namespace App\Scoring\Results;

/**
 * One row of an Event's results as pages read it: who, the result as entered and after penalties,
 * points, place, and its penalties and disqualifications. Plain data with nothing to load.
 */
final class EventResult
{
    /**
     * @param int $id The id of the stored result (a SERC: of the Entry's first Mark)
     * @param null|string|float|int $result As entered: a time in milliseconds, or a SERC's weighted Marks added up
     * @param float $resolvedResult After the penalty function, and for a combined result the parts added up
     * @param Violation[] $penalties Stored ones first, then any the formula added
     * @param Violation[] $disqualifications Stored ones first, then any the formula added
     * @param EventResultPart[] $parts For a combined result, each result it was made from, this row's own first. Otherwise empty.
     * @param int $markingPoints A SERC: how many Marks the Entry has
     */
    public function __construct(
        public readonly int $id,
        public readonly EntryRef $entry,
        public readonly null|string|float|int $result,
        public readonly float $resolvedResult,
        public readonly ?float $points,
        public readonly int $position,
        public readonly array $penalties = [],
        public readonly array $disqualifications = [],
        public readonly array $parts = [],
        public readonly int $markingPoints = 1,
    ) {}

    public function isDisqualified(): bool
    {
        return count($this->disqualifications) > 0;
    }

    public function hasPenalties(): bool
    {
        return count($this->penalties) > 0;
    }

    public function isCombined(): bool
    {
        return count($this->parts) > 0;
    }

    /**
     * Who the row is for: each part of a combined result, or the one Entry
     *
     * @return EntryRef[]
     */
    public function entries(): array
    {
        return $this->isCombined() ? array_map(fn(EventResultPart $part) => $part->entry, $this->parts) : [$this->entry];
    }

    /**
     * The first disqualification as a results table writes it, or null without one
     */
    public function disqualificationLabel(): ?string
    {
        return ($this->disqualifications[0] ?? null)?->label();
    }

    /**
     * The penalties as a results table writes them ("P201, P900 (x3)"), or null without any.
     * Several P900s are counted rather than listed, after the rest.
     */
    public function penaltiesLabel(): ?string
    {
        if (!$this->hasPenalties()) {
            return null;
        }

        $labels = [];
        $p900s = count(array_filter($this->penalties, fn(Violation $penalty) => $penalty->code == '900'));

        foreach ($this->penalties as $penalty) {
            if ($p900s > 1 && $penalty->code == '900') {
                continue;
            }

            $labels[] = $penalty->label();
        }

        if ($p900s > 1) {
            $labels[] = "P900 (x{$p900s})";
        }

        return implode(', ', $labels);
    }
}
