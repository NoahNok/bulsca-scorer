<?php

namespace App\Scoring\Results;

/**
 * A penalty or disqualification on a result: its code, and the id of the stored one. One that the
 * Event's formula added automatically isn't stored, and has no id.
 */
final class Violation
{
    public const PENALTY = 'penalty';
    public const DISQUALIFICATION = 'disqualification';

    public function __construct(
        public readonly string $kind,
        public readonly mixed $code,
        public readonly ?int $id = null,
    ) {}

    /**
     * As it is written on a results table: P900, DQ501, or DNF, DNS and OOT for those three
     */
    public function label(): string
    {
        if ($this->kind === self::PENALTY) {
            return "P{$this->code}";
        }

        return match ($this->code) {
            99915 => 'DNF',
            99904 => 'DNS',
            99901 => 'OOT',
            default => "DQ{$this->code}",
        };
    }

    public function __toString(): string
    {
        return $this->label();
    }
}
