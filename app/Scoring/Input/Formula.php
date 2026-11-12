<?php

namespace App\Scoring\Input;

/**
 * An Event's formula as it is stored on `scoring_schemas.schema`, with each setting's default in
 * one place. The expressions are in the formula language (`App\Scoring\Engine\FormulaEvaluator`).
 */
final class Formula
{
    /**
     * @param mixed $equation Works out a result's points. Anything but a non-empty string can't be scored.
     * @param array<array{name?: string, expression?: string}> $globalVariables Worked out once, in this order
     * @param array<array{name?: string, expression?: string}> $localVariables Worked out for each result, in this order
     * @param ?string $penaltyFunction Works out the resolved result of a result with penalties
     * @param bool $forcePenaltyFunction Run the penalty function for every result, penalties or not
     * @param array<array{code: mixed, condition: string, amount: string}> $autoPenalties
     * @param array<array{code: mixed, condition: string, amount: string}> $autoDisqualifications
     * @param bool $rankHigher The most points is 1st, not the fewest
     * @param ?string $rankEquation Adjusts each place once ranked, reading `rank` and `teams`
     * @param bool $allowDisqualifiedToRank Disqualified results are ranked on their points, not put last
     * @param string[] $groupBy Combine the results that share these (`league`, `team`, `club`) into one
     */
    public function __construct(
        public readonly mixed $equation = null,
        public readonly array $globalVariables = [],
        public readonly array $localVariables = [],
        public readonly ?string $penaltyFunction = null,
        public readonly bool $forcePenaltyFunction = false,
        public readonly array $autoPenalties = [],
        public readonly array $autoDisqualifications = [],
        public readonly bool $rankHigher = true,
        public readonly ?string $rankEquation = null,
        public readonly bool $allowDisqualifiedToRank = false,
        public readonly array $groupBy = [],
    ) {}

    public static function fromSchema(?array $schema): self
    {
        $schema ??= [];

        return new self(
            equation: $schema['equation'] ?? null,
            globalVariables: $schema['global_variables'] ?? [],
            localVariables: $schema['local_variables'] ?? [],
            penaltyFunction: ($schema['penalty_func'] ?? null) ?: null,
            forcePenaltyFunction: (bool) ($schema['force_penalty'] ?? false),
            autoPenalties: $schema['auto_penalties'] ?? [],
            autoDisqualifications: $schema['auto_disqualifications'] ?? [],
            rankHigher: (bool) ($schema['rank_higher'] ?? true),
            rankEquation: ($schema['rank_equation'] ?? null) ?: null,
            allowDisqualifiedToRank: (bool) ($schema['allow_disqualified_to_rank'] ?? false),
            groupBy: (array) (($schema['group_by'] ?? null) ?: []),
        );
    }

    /**
     * The part `FormulaEvaluator::process()` works points out from
     */
    public function equationPayload(): array
    {
        return [
            'equation' => $this->equation,
            'global_variables' => $this->globalVariables,
            'local_variables' => $this->localVariables,
        ];
    }
}
