<?php

namespace Tests\Feature\Scoring;

use App\Exceptions\ScoringException;

/**
 * Things the engine does that look unintended but are kept on purpose (Q6, Q7, decided 2026-11-11):
 * results that have already been published depend on them. These tests are their specification.
 * The other quirks in docs/scoring-engine.md ("Quirks") are fixed, and tested where the behaviour
 * they fixed is: SpeedEventScoringTest, EventScorerTest, FormulaEvaluatorTest, ResultsVersionTest.
 */
class EngineQuirksTest extends ScoringTestCase
{
    public function test_an_entity_without_a_seed_time_is_scored_as_if_its_seed_were_zero(): void
    {
        $t = $this->teams('A', 'B');
        $event = $this->speedEvent([
            ...self::TIME_POINTS,
            'penalty_func' => 'item.result + COUNT(item.penalties)*15000',
            'auto_penalties' => [['code' => '900', 'condition' => '(item.result - (item.seed)) > (item.seed*0.1)', 'amount' => '1 + FLOOR((item.result - (item.seed*1.1)) / 15000)']],
        ], [[$t['A'], 61000], [$t['B'], 61000]]);
        $this->seedTime($event, $t['A'], 60000);

        $results = $this->eventResults($event);

        // B swam the same time as A but has no seed: 1 + FLOOR(61000 / 15000) = 5 penalties
        $this->assertCount(0, $this->resultFor($results, $t['A'])->penalties);
        $this->assertCount(5, $this->resultFor($results, $t['B'])->penalties);
    }

    public function test_a_tie_break_gives_fully_tied_entities_different_places(): void
    {
        $t = $this->teams('A', 'B');
        $event = $this->speedEvent(self::RAW, [[$t['A'], 1000], [$t['B'], 1000]]);

        $tied = $this->sheet([], [$event]);
        $broken = $this->sheet(['break_ties' => [['type' => 'speed', 'id' => $event->id]]], [$event]);

        $this->assertSame([['A', 1, 1000.0], ['B', 1, 1000.0]], $this->standings($this->sheetRows($tied)));
        $this->assertSame([['A', 1, 1000.0], ['B', 2, 1000.0]], $this->standings($this->sheetRows($broken)));
    }

    public function test_a_broken_formula_fails_naming_the_part_that_broke(): void
    {
        $t = $this->teams('A');
        $event = $this->speedEvent(['equation' => 'best', 'global_variables' => [['name' => 'best', 'order' => null, 'expression' => 'NOPE(results)']]], [[$t['A'], 60000]]);

        $this->expectException(ScoringException::class);
        $this->expectExceptionMessage("Error evaluating global variable 'best'");

        $this->eventResults($event);
    }
}
