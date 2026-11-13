<?php

namespace Tests\Feature\Scoring;

use App\Exceptions\ScoringException;
use ErrorException;

/**
 * Things the engine does today that look unintended, pinned so a rebuild doesn't change one by
 * accident: results that have already been published may depend on them. Each is written up in
 * docs/scoring-engine.md ("Quirks"), with what it would take to fix it on purpose. The two in the
 * formula language itself (Q2, Q3) are in Tests\Unit\Scoring\FormulaEvaluatorTest. Q8 (a sheet's
 * results going stale) is fixed: see ResultsVersionTest.
 */
class EngineQuirksTest extends ScoringTestCase
{
    public function test_an_automatic_dq_takes_the_code_of_the_last_automatic_penalty_not_its_own(): void
    {
        $t = $this->teams('A');
        $event = $this->speedEvent([
            ...self::TIME_POINTS,
            'auto_penalties' => [['code' => '900', 'condition' => 'false', 'amount' => '1']],
            'auto_disqualifications' => [['code' => '555', 'condition' => 'COUNT(item.penalties) > 1', 'amount' => '1']],
        ], [[$t['A'], 60000]]);
        $event->addEntityPenalty($t['A'], 801);
        $event->addEntityPenalty($t['A'], 802);

        $result = $this->eventResults($event)[0];

        $this->assertTrue($result->isDisqualified());
        $this->assertSame('DQ900', $result->disqualificationLabel());
    }

    public function test_an_automatic_dq_with_no_automatic_penalties_cannot_be_scored(): void
    {
        $t = $this->teams('A');
        $event = $this->speedEvent([
            ...self::TIME_POINTS,
            'auto_disqualifications' => [['code' => '555', 'condition' => 'true', 'amount' => '1']],
        ], [[$t['A'], 60000]]);

        $this->expectException(ErrorException::class);
        $this->expectExceptionMessage('Undefined variable $autoPen');

        $this->eventResults($event);
    }

    public function test_seed_times_come_from_the_competitions_first_speed_event_whichever_is_scored(): void
    {
        $t = $this->teams('A');
        $first = $this->speedEvent(self::RAW, [[$t['A'], 1]]);
        $second = $this->speedEvent([...self::RAW, 'penalty_func' => 'item.seed', 'force_penalty' => true], [[$t['A'], 70000]], name: 'Medley');

        $this->seedTime($first, $t['A'], 60000);
        $this->seedTime($second, $t['A'], 99000);

        $this->assertSame(60000.0, $this->eventResults($second)[0]->resolvedResult);
    }

    public function test_the_seed_time_can_be_read_by_penalties_but_not_by_the_equation(): void
    {
        $t = $this->teams('A');
        $event = $this->speedEvent(['equation' => 'item.seed', 'global_variables' => []], [[$t['A'], 70000]]);
        $this->seedTime($event, $t['A'], 60000);

        $this->assertSame(0.0, $this->eventResults($event)[0]->points);
    }

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
