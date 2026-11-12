<?php

namespace App\Scoring\Input;

/**
 * A Results sheet as it is stored on `result_schemas.schema`, with each setting's default in one
 * place, and the Events it adds up. Its Formula is in the formula language
 * (`App\Scoring\Engine\FormulaEvaluator`) and is worked out for each Event's results in turn.
 */
final class SheetDefinition
{
    /**
     * @param mixed $equation Works out what a result in one Event counts for. Anything but a non-empty string can't be scored.
     * @param array<array{name?: string, expression?: string}> $globalVariables Worked out once per Event, in this order
     * @param array<array{name?: string, expression?: string}> $localVariables Worked out for each result, in this order
     * @param SheetEvent[] $events In the order their points are added up
     * @param bool $ignoreDq A disqualified result keeps what the Formula gave it, rather than counting for nothing
     * @param string $groupOn One row per `team`, `club`, `competitor` or `league`
     * @param bool $rankHigher The highest total is 1st, not the lowest
     * @param array<int|string|array{type: string, id: int|string}> $breakTies Events whose places separate equal totals, the first deciding first
     */
    public function __construct(
        public readonly mixed $equation = null,
        public readonly array $globalVariables = [],
        public readonly array $localVariables = [],
        public readonly array $events = [],
        public readonly bool $ignoreDq = false,
        public readonly string $groupOn = 'team',
        public readonly bool $rankHigher = true,
        public readonly array $breakTies = [],
    ) {}

    /**
     * @param SheetEvent[] $events
     */
    public static function fromSchema(?array $schema, array $events): self
    {
        $schema ??= [];

        return new self(
            equation: $schema['equation'] ?? null,
            globalVariables: $schema['global_variables'] ?? [],
            localVariables: $schema['local_variables'] ?? [],
            events: $events,
            ignoreDq: (bool) ($schema['ignore_dq'] ?? false),
            groupOn: $schema['group_on'] ?? 'team',
            rankHigher: (bool) ($schema['rank_higher'] ?? true),
            breakTies: (array) (($schema['break_ties'] ?? null) ?: []),
        );
    }

    /**
     * The part `FormulaEvaluator::process()` works each result's points out from
     */
    public function formulaPayload(): array
    {
        return [
            'equation' => $this->equation,
            'global_variables' => $this->globalVariables,
            'local_variables' => $this->localVariables,
        ];
    }
}
