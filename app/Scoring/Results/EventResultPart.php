<?php

namespace App\Scoring\Results;

/**
 * One of the results that a combined result was made from (one swimmer of a pair): who, their own
 * result and disqualifications.
 */
final class EventResultPart
{
    /**
     * @param Violation[] $disqualifications
     */
    public function __construct(
        public readonly EntryRef $entry,
        public readonly null|string|float|int $result,
        public readonly array $disqualifications = [],
    ) {}

    public function isDisqualified(): bool
    {
        return count($this->disqualifications) > 0;
    }

    /**
     * The first disqualification as a results table writes it, or null without one
     */
    public function disqualificationLabel(): ?string
    {
        return ($this->disqualifications[0] ?? null)?->label();
    }
}
