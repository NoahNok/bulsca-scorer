<?php

namespace App\Scoring\Engine;

use App\Scoring\Input\Entry;
use App\Scoring\Input\EventRow;
use Illuminate\Support\Collection;

/**
 * One result as it is scored, and what a formula reads as `item`. These names are the contract
 * stored formulas rely on: `result`, `resolvedResult`, `points`, `position`, `seed`, `penalties`,
 * `disqualifications`, `combined`, `total_marking_points`, `entity`, `isDisqualified()`, `isDNF()`,
 * `isCombined()` and `hasPenalties()`.
 *
 * `resolvedResult` is set once the penalty function has run and `position` once ranked; a formula
 * that reads one before then fails. The property types matter: what a formula works out is
 * converted to them (a place of 2.5 to 2, a time held as a string to a number).
 */
final class ScoredResult
{
    public float $resolvedResult;

    public ?float $points = null;

    public int $position;

    /** @var Collection<int, ViolationCode> */
    public Collection $penalties;

    /** @var Collection<int, ViolationCode> */
    public Collection $disqualifications;

    /** @var Collection<int, ScoredResult> When results were combined: this one, then each one added to it */
    public Collection $combined;

    public function __construct(
        public int $id,
        public Entry $entity,
        public null|string|float $result,
        array $penalties = [],
        array $disqualifications = [],
        public int $seed = 0,
        public int $total_marking_points = 1,
    ) {
        $this->penalties = collect($penalties)->map(fn($code) => new ViolationCode($code));
        $this->disqualifications = collect($disqualifications)->map(fn($code) => new ViolationCode($code));
        $this->combined = collect();
    }

    public static function fromRow(EventRow $row): self
    {
        return new self($row->id, $row->entry, $row->result, $row->penalties, $row->disqualifications, $row->seed, $row->markingPoints);
    }

    public function isDisqualified(): bool
    {
        return $this->disqualifications->count() > 0;
    }

    public function hasPenalties(): bool
    {
        return $this->penalties->count() > 0;
    }

    public function isCombined(): bool
    {
        return $this->combined->count() > 0;
    }

    /**
     * Did Not Finish, and nothing else
     */
    public function isDNF(): bool
    {
        return $this->disqualifications->firstWhere('code', '99915') !== null && $this->disqualifications->count() == 1;
    }

    /**
     * Adds another result's resolved result to this one's, for Events scored on several results
     * together (a pair's two swims)
     */
    public function combineWith(ScoredResult $other): void
    {
        $this->resolvedResult = $this->resolvedResult + $other->resolvedResult;

        $this->combined->push($this);
        $this->combined->push($other);
    }
}
