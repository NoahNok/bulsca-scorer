<?php

namespace App\Scoring\Engine;

use App\Scoring\Input\EventInput;
use App\Scoring\Input\Formula;

/**
 * Scores an Event from plain inputs, for speed events and SERCs alike:
 *
 *   rows ──► automatic penalties and DQs ──► penalty function ──► grouping ──► equation (points)
 *        ──► rank ──► rank equation
 *
 * No database, cache or models: the same input always gives the same results. It reproduces the
 * engine it replaced exactly, quirks included (the Qn comments; see docs/scoring-engine.md), because
 * published results depend on them.
 */
final class EventScorer
{
    /**
     * @param ?int $leagueId Score only the Entries in this League, against each other
     * @return ScoredResult[] Best placed first, exact ties in the order their rows were given
     */
    public static function score(EventInput $event, ?int $leagueId = null): array
    {
        $formula = $event->formula;
        $evaluator = new FormulaEvaluator();

        $results = [];
        foreach ($event->rows as $row) {
            if ($leagueId === null || $row->entry->inLeague($leagueId)) {
                $results[] = ScoredResult::fromRow($row);
            }
        }

        self::applyViolations($evaluator, $formula, $results);

        // Q9: the equation is worked out even when there is nobody to score
        $results = $evaluator->process($formula->equationPayload(), collect(self::group($formula, $results)));

        // A SERC's disqualified results always share last place. A speed event's do unless they are
        // allowed to rank, when each is placed on its points.
        $dqsShareLast = $event->type === EventInput::SERC || !$formula->allowDisqualifiedToRank;

        $ranked = self::rank($formula, $results->all(), $dqsShareLast);

        if ($formula->rankEquation) {
            $rankEvaluator = new FormulaEvaluator();
            $total = count($ranked);

            foreach ($ranked as $result) {
                $result->position = $rankEvaluator->evaluateInstance($formula->rankEquation, $result, ['teams' => $total, 'rank' => $result->position]);
            }
        }

        return $ranked;
    }

    /**
     * Adds the formula's automatic penalties and disqualifications to each result, then works out
     * its resolved result: the penalty function's answer for a result with penalties (or for every
     * result when it is forced), otherwise the result as it stands.
     *
     * @param ScoredResult[] $results
     */
    private static function applyViolations(FormulaEvaluator $evaluator, Formula $formula, array $results): void
    {
        foreach ($formula->autoPenalties as $autoPen) {
            $applyCode = $autoPen['code'];

            $applyIf = $evaluator->parse($autoPen['condition'], ['item']);
            $applyAmount = $evaluator->parse($autoPen['amount'], ['item']);

            foreach ($results as $result) {
                if (!((bool) $evaluator->evaluate($applyIf, ['item' => $result]))) {
                    continue;
                }

                $amount = $evaluator->evaluate($applyAmount, ['item' => $result]);

                for ($i = 0; $i < $amount; $i++) {
                    $result->penalties->push(new ViolationCode($applyCode));
                }
            }
        }

        foreach ($formula->autoDisqualifications as $autoDq) {
            // Q1: the code of the last automatic penalty, not this DQ's own. `$autoPen` is left over
            // from the loop above, and isn't defined when there are no automatic penalties.
            $applyCode = $autoPen['code'];

            $applyIf = $evaluator->parse($autoDq['condition'], ['item']);
            $applyAmount = $evaluator->parse($autoDq['amount'], ['item']);

            foreach ($results as $result) {
                if (!((bool) $evaluator->evaluate($applyIf, ['item' => $result]))) {
                    continue;
                }

                $amount = $evaluator->evaluate($applyAmount, ['item' => $result]);

                for ($i = 0; $i < $amount; $i++) {
                    $result->disqualifications->push(new ViolationCode($applyCode));
                }
            }
        }

        $penaltyFunction = $formula->penaltyFunction ? $evaluator->parse($formula->penaltyFunction, ['item']) : null;

        foreach ($results as $result) {
            $resolved = $result->result;

            if ($penaltyFunction && ($result->penalties->count() > 0 || $formula->forcePenaltyFunction)) {
                $resolved = $evaluator->evaluate($penaltyFunction, ['item' => $result]);
            }

            $result->resolvedResult = $resolved;

            // Q4: the seed can be read by automatic penalties and the penalty function, but is 0 from here on
            $result->seed = 0;
        }
    }

    /**
     * Combines the results that share everything in the formula's `group_by` into the first of
     * them, adding their resolved results together
     *
     * @param ScoredResult[] $results
     * @return ScoredResult[]
     */
    private static function group(Formula $formula, array $results): array
    {
        if (!$formula->groupBy) {
            return $results;
        }

        $groups = [];
        foreach ($results as $result) {
            $key = implode('|', array_map(fn($by) => match ($by) {
                'league' => $result->entity->firstLeagueId() ?? 'no-league',
                'team' => $result->entity->teamId ?? 'no-team',
                'club' => $result->entity->clubId ?? 'no-club',
                default => 'unknown',
            }, $formula->groupBy));

            $groups[$key][] = $result;
        }

        return array_map(function (array $group) {
            $base = array_shift($group);

            foreach ($group as $other) {
                $base->combineWith($other);
            }

            return $base;
        }, array_values($groups));
    }

    /**
     * Gives each result its place: best points first, results with the same points sharing the
     * better place and the next taking the place after them all (1, 2, 2, 4).
     *
     * @param ScoredResult[] $results
     * @return ScoredResult[] In place order
     */
    private static function rank(Formula $formula, array $results, bool $dqsShareLast): array
    {
        $direction = $formula->rankHigher ? -1 : 1;

        $sorted = Ranking::sort(
            $results,
            $formula->allowDisqualifiedToRank
                ? fn(ScoredResult $result) => $result->points * $direction
                : fn(ScoredResult $result) => [count($result->disqualifications) > 0 ? 1 : 0, $result->points * $direction]
        );

        return Ranking::place($sorted, fn(ScoredResult $result) => (count($result->disqualifications) > 0 && $dqsShareLast) ? 'DQ' : $result->points);
    }
}
