<?php

namespace App\Scoring\Engine;

use App\Scoring\Input\SheetDefinition;

/**
 * Scores a Results sheet from its Events' scored results:
 *
 *   each Event's results ──► the sheet's Formula ──► × the Event's weight (0 for a DQ)
 *        ──► added up per team, Club, competitor or League ──► ranked, with tie-breaks
 *
 * No database, cache or models. The Events' results are not changed, so one scoring of an Event can
 * be given to every sheet that includes it. Arithmetic is in the order it has always been (an
 * Entry's Events added up in sheet order, the weight applied after the Formula), because published
 * totals depend on it to the last digit.
 */
final class SheetScorer
{
    /**
     * @param array<int, ScoredResult[]> $eventResults Each Event's results for the sheet's League, best
     *                                                 placed first, keyed as `$sheet->events` is
     * @return SheetStanding[] Best placed first
     */
    public static function score(SheetDefinition $sheet, array $eventResults): array
    {
        $all = [];

        foreach ($sheet->events as $index => $event) {
            $results = array_map(fn(ScoredResult $result) => new SheetEventResult($result, $event), $eventResults[$index]);

            // The Formula is worked out even for an Event with no results, as an Event's is (Q9)
            (new FormulaEvaluator())->process($sheet->formulaPayload(), collect($results), 'adjustedPoints');

            foreach ($results as $result) {
                $result->adjustedPoints = $result->adjustedPoints * $event->weight;

                if (!$sheet->ignoreDq && $result->isDisqualified()) {
                    $result->adjustedPoints = 0;
                }

                $all[] = $result;
            }
        }

        $standings = array_map(fn(array $group) => self::standing($sheet, $group), self::group($sheet, $all));

        // Points in the sheet's direction, then each tie-break Event's place, the better place first
        // whichever way points rank. A row with no result in a tie-break Event comes after those with one.
        $sorted = Ranking::sort($standings, function (SheetStanding $standing) use ($sheet) {
            $key = [$sheet->rankHigher ? -$standing->totalPoints : $standing->totalPoints];

            foreach ($sheet->breakTies as $tieBreak) {
                $key[] = $standing->placeIn($tieBreak) ?? PHP_INT_MAX;
            }

            return $key;
        });

        // Q7: with tie-breaks nothing shares a place, even rows the tie-breaks don't separate
        return Ranking::place($sorted, fn(SheetStanding $standing) => $standing->totalPoints, share: !$sheet->breakTies);
    }

    /**
     * The results that belong on one row together, rows in the order their first result came
     *
     * @param SheetEventResult[] $results
     * @return SheetEventResult[][]
     */
    private static function group(SheetDefinition $sheet, array $results): array
    {
        $groups = [];

        foreach ($results as $result) {
            // Those with nothing to group on (a Club in a sheet of teams) share one row
            $groups[$result->entity->groupId($sheet->groupOn) ?? ''][] = $result;
        }

        return array_values($groups);
    }

    /**
     * @param SheetEventResult[] $results
     */
    private static function standing(SheetDefinition $sheet, array $results): SheetStanding
    {
        // Listed under the first Entry that is what the sheet groups on (a Club's own result in a
        // sheet of Clubs), or failing that the first Entry there is
        $entity = $results[0]->entity;
        foreach ($results as $result) {
            if ($result->entity->type === $sheet->groupOn) {
                $entity = $result->entity;
                break;
            }
        }

        $total = 0;
        foreach ($results as $result) {
            $total += $result->adjustedPoints;
        }

        return new SheetStanding($entity, $results, $total);
    }
}
