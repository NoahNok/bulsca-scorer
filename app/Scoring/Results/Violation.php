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

    /**
     * Penalties as a results table writes them ("P201, P900 (x3)"), or null without any. Several
     * P900s are counted rather than listed, after the rest.
     *
     * @param Violation[] $penalties
     */
    public static function penaltiesLabel(array $penalties): ?string
    {
        if (!$penalties) {
            return null;
        }

        $labels = [];
        $p900s = count(array_filter($penalties, fn(Violation $penalty) => $penalty->code == '900'));

        foreach ($penalties as $penalty) {
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

    public function __toString(): string
    {
        return $this->label();
    }
}
