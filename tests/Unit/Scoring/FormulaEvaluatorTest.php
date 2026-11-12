<?php

namespace Tests\Unit\Scoring;

use App\DTO\ResolvedResult;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Exceptions\ScoringException;
use App\Models\Event\Disqualification;
use App\Scoring\Engine\FormulaEvaluator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * The formula language on its own, with no database and no framework: what a formula can see
 * (`item`, `results`, its variables) and what each built-in function works out.
 */
class FormulaEvaluatorTest extends TestCase
{
    /**
     * Results with these resolved values, ids from 1. A value in `$disqualified` is given a DQ.
     */
    private function results(array $values, array $disqualified = []): Collection
    {
        return collect($values)->values()->map(function ($value, $index) use ($disqualified) {
            $dqs = new EloquentCollection();
            if (in_array($value, $disqualified, true)) {
                $dqs->push(new Disqualification(['code' => 201]));
            }

            return new ResolvedResult($index + 1, $value, $value, new CompetitionTeam(), new CompetitionSpeedEvent(), $dqs);
        });
    }

    /**
     * Each result's points from a formula, in the order the results were given
     */
    private function points(array $formula, Collection $results, string $field = 'points'): array
    {
        return (new FormulaEvaluator())->process($formula, $results, $field)->pluck($field)->all();
    }

    public function test_the_equation_is_worked_out_for_each_result(): void
    {
        $this->assertSame([20.0, 40.0], $this->points(['equation' => 'item.resolvedResult * 2'], $this->results([10, 20])));
    }

    public function test_global_variables_are_worked_out_once_in_order_and_can_use_earlier_ones(): void
    {
        $formula = [
            'equation' => 'item.resolvedResult / double * 100',
            'global_variables' => [
                ['name' => 'best', 'expression' => "MAXIMUM(results, 'resolvedResult')"],
                ['name' => 'double', 'expression' => 'best * 2'],
            ],
        ];

        $this->assertSame([12.5, 50.0], $this->points($formula, $this->results([10, 40])));
    }

    public function test_local_variables_are_worked_out_for_each_result(): void
    {
        $formula = [
            'equation' => 'half + quarter',
            'local_variables' => [
                ['name' => 'half', 'expression' => 'item.resolvedResult / 2'],
                ['name' => 'quarter', 'expression' => 'half / 2'],
            ],
        ];

        $this->assertSame([7.5, 30.0], $this->points($formula, $this->results([10, 40])));
    }

    public function test_the_answer_can_be_written_to_another_field(): void
    {
        $results = $this->results([10]);

        $this->assertSame([11.0], $this->points(['equation' => 'item.resolvedResult + 1'], $results, 'resolvedResult'));
        $this->assertNull($results[0]->points);
    }

    public function test_filter_keeps_the_results_a_condition_holds_for(): void
    {
        $formula = [
            'equation' => 'COUNT(valid)',
            'global_variables' => [['name' => 'valid', 'expression' => "FILTER(results, '!item.isDisqualified()')"]],
        ];

        $this->assertSame([2.0, 2.0, 2.0], $this->points($formula, $this->results([10, 20, 30], disqualified: [20])));
    }

    public function test_maximum_and_minimum_read_an_attribute_across_results(): void
    {
        $formula = ['equation' => "MAXIMUM(results, 'resolvedResult') - MINIMUM(results, 'resolvedResult')"];

        $this->assertSame([25.0, 25.0], $this->points($formula, $this->results([5, 30])));
    }

    public function test_maximum_of_result_means_the_resolved_result(): void
    {
        $results = $this->results([5, 30]);
        $results[1]->resolvedResult = 12.0; // a penalty function brought the 30 down

        $this->assertSame([12.0, 12.0], $this->points(['equation' => "MAXIMUM(results, 'result')"], $results));
    }

    public function test_ceil_and_floor_round_up_and_down(): void
    {
        $this->assertSame([3.0], $this->points(['equation' => 'CEIL(item.resolvedResult / 4)'], $this->results([10])));
        $this->assertSame([2.0], $this->points(['equation' => 'FLOOR(item.resolvedResult / 4)'], $this->results([10])));
    }

    public function test_rank_places_the_highest_first_and_ties_share_the_better_place(): void
    {
        $formula = ['equation' => "RANK(results, 'resolvedResult', item)"];

        $this->assertSame([4.0, 1.0, 1.0, 3.0], $this->points($formula, $this->results([10, 40, 40, 30])));
    }

    public function test_rank_can_place_the_lowest_first(): void
    {
        $formula = ['equation' => "RANK(results, 'resolvedResult', item, 'ASC')"];

        $this->assertSame([1.0, 3.0, 3.0, 2.0], $this->points($formula, $this->results([10, 40, 40, 30])));
    }

    public function test_rankmedian_gives_ties_the_middle_of_the_places_they_cover(): void
    {
        $formula = ['equation' => "RANKMEDIAN(results, 'resolvedResult', item, 'ASC')"];

        // Two tied for 2nd and 3rd take 2.5; three tied for 4th to 6th take 5th
        $this->assertSame([1.0, 2.5, 2.5, 5.0, 5.0, 5.0], $this->points($formula, $this->results([10, 20, 20, 30, 30, 30])));
    }

    public function test_a_formula_needs_an_equation(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->points(['equation' => ''], $this->results([10]));
    }

    public function test_one_expression_can_be_worked_out_for_one_result(): void
    {
        $result = $this->results([4])[0];

        // What a rank equation is given: the place, and how many were ranked
        $this->assertSame(7.0, (new FormulaEvaluator())->evaluateInstance('teams - rank + item.resolvedResult', $result, ['teams' => 5, 'rank' => 2]));
    }

    public function test_a_broken_formula_fails_naming_the_part_that_broke(): void
    {
        $formula = ['equation' => 'best', 'global_variables' => [['name' => 'best', 'expression' => 'NOPE(results)']]];

        try {
            $this->points($formula, $this->results([10]));
            $this->fail('The formula should not have been worked out');
        } catch (ScoringException $e) {
            $this->assertStringStartsWith("Error evaluating global variable 'best': The function \"NOPE\" does not exist", $e->getMessage());
            $this->assertStringEndsWith("- 'NOPE(results)'", $e->getMessage());
        }
    }

    public function test_a_global_variable_without_an_expression_fails_as_a_broken_one(): void
    {
        $this->expectException(ScoringException::class);
        $this->expectExceptionMessage("Error evaluating global variable 'best'");

        $this->points(['equation' => 'best', 'global_variables' => [['name' => 'best', 'expression' => null]]], $this->results([10]));
    }

    public function test_variables_are_worked_out_in_the_order_they_are_stored_whatever_their_order_says(): void
    {
        $formula = [
            'equation' => 'second',
            'global_variables' => [
                ['name' => 'first', 'order' => 2, 'expression' => '10'],
                ['name' => 'second', 'order' => 1, 'expression' => 'first * 2'],
            ],
        ];

        $this->assertSame([20.0], $this->points($formula, $this->results([1])));
    }

    /*
     * Quirks: unintended, but pinned so a rebuild can't change them by accident. See "Quirks" in
     * docs/scoring-engine.md.
     */

    /** Q2 */
    public function test_rank_remembers_the_first_direction_it_was_asked_for(): void
    {
        $formula = ['equation' => "RANK(results, 'resolvedResult', item, 'ASC') * 10 + RANK(results, 'resolvedResult', item, 'DESC')"];

        // The lowest is 1st ascending and should be 3rd descending (13), but the second RANK reuses the first's order
        $this->assertSame([11.0, 22.0, 33.0], $this->points($formula, $this->results([10, 20, 30])));
    }

    /** Q3 */
    public function test_rankmedian_treats_results_that_differ_by_less_than_one_as_tied(): void
    {
        $formula = ['equation' => "RANKMEDIAN(results, 'resolvedResult', item)"];

        // The values are used as array keys, which cuts a float to a whole number. PHP 8 only warns.
        $warnings = [];
        set_error_handler(function (int $level, string $message) use (&$warnings) {
            $warnings[] = $message;

            return true;
        }, E_DEPRECATED);

        try {
            $points = $this->points($formula, $this->results([22.0, 22.5, 30.0]));
        } finally {
            restore_error_handler();
        }

        // 22.5 should be 2nd and 22 3rd; both get the middle of 2nd and 3rd
        $this->assertSame([2.5, 2.5, 1.0], $points);
        $this->assertContains('Implicit conversion from float 22.5 to int loses precision', $warnings);
    }
}
