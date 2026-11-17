<?php

namespace Tests\Unit\Scoring;

use App\Exceptions\ScoringException;
use App\Models\Interfaces\IScoringContextSimplify;
use App\Scoring\Engine\ScoredResult;
use App\Scoring\Engine\SheetEventResult;
use App\Scoring\Input\Entry;
use App\Scoring\Input\EventInput;
use App\Scoring\Input\SheetEvent;
use PHPUnit\Framework\TestCase;

/**
 * A failed formula carries what it could see. Describing that names every Entity, which takes
 * queries, so it is left until someone asks.
 */
class ScoringExceptionTest extends TestCase
{
    /**
     * A result that counts how often it is asked to describe itself
     */
    private function describable(string $name): IScoringContextSimplify
    {
        return new class($name) implements IScoringContextSimplify {
            public int $asked = 0;

            public function __construct(private string $name) {}

            public function simplifyContext(): array
            {
                $this->asked++;

                return ['entity' => $this->name];
            }
        };
    }

    public function test_results_are_not_described_until_the_context_is_asked_for(): void
    {
        $a = $this->describable('A');

        $e = new ScoringException('Broken', ['results' => collect([$a]), 'item' => $a]);

        $this->assertSame('Broken', $e->getMessage());
        $this->assertSame(0, $a->asked);
    }

    public function test_the_context_describes_each_result_and_the_one_that_failed(): void
    {
        $a = $this->describable('A');
        $b = $this->describable('B');
        $results = collect([$a, $b]);

        $e = new ScoringException('Broken', ['results' => $results, 'best' => 12.5, 'item' => $b]);

        $this->assertSame(
            ['results' => [['entity' => 'A'], ['entity' => 'B']], 'best' => 12.5, 'item' => ['entity' => 'B']],
            $e->getContext(),
        );
        $this->assertSame([$a, $b], $results->all(), 'The results being scored are left as they were');
    }

    public function test_the_context_is_only_worked_out_once(): void
    {
        $a = $this->describable('A');
        $e = new ScoringException('Broken', ['results' => [$a]]);

        $e->getContext();
        $e->getContext();

        $this->assertSame(1, $a->asked);
    }

    public function test_an_item_that_cannot_describe_itself_is_left_out(): void
    {
        $e = new ScoringException('Broken', ['item' => new \stdClass(), 'team' => 'Cardiff A']);

        $this->assertSame(['team' => 'Cardiff A'], $e->getContext());
    }

    public function test_there_need_be_no_context(): void
    {
        $this->assertSame([], (new ScoringException('Broken'))->getContext());
    }

    public function test_entries_and_events_are_named_when_a_namer_is_given(): void
    {
        $result = new SheetEventResult($this->scored(), new SheetEvent(EventInput::SPEED, 3));
        $names = [];

        $e = (new ScoringException('Broken', ['item' => $result]))->nameWith(
            entries: function (string $key) use (&$names) {
                $names[] = $key;

                return $key === 'team:12' ? 'Cardiff A' : null;
            },
            events: fn(string $key) => $key === 'speed:3' ? 'Swim & Tow' : null,
        );

        $this->assertSame([], $names, 'Nothing is named until the context is asked for');
        $this->assertSame('Cardiff A', $e->getContext()['item']['entity']);
        $this->assertSame('Swim & Tow', $e->getContext()['item']['event']);
    }

    public function test_a_key_the_namer_does_not_know_is_kept(): void
    {
        $e = (new ScoringException('Broken', ['item' => $this->scored()]))->nameWith(entries: fn() => null);

        $this->assertSame('team:12', $e->getContext()['item']['entity']);
    }

    public function test_a_scored_result_describes_itself_before_it_is_placed(): void
    {
        $this->assertSame([
            'entity' => 'team:12',
            'result' => 61000.0,
            'resolved_result' => null,
            'points' => null,
            'position' => null,
            'penalties' => '900, 900',
            'disqualifications' => '',
        ], $this->scored(resolved: false)->simplifyContext());
    }

    public function test_a_scored_result_describes_its_numbers_once_worked_out(): void
    {
        $scored = $this->scored();
        $scored->points = 850.5;
        $scored->position = 2;

        $this->assertSame(
            ['resolved_result' => 61000.0, 'points' => 850.5, 'position' => 2],
            array_intersect_key($scored->simplifyContext(), array_flip(['resolved_result', 'points', 'position'])),
        );
    }

    public function test_a_sheets_result_adds_its_event_and_what_it_counts_for(): void
    {
        $scored = $this->scored();
        $scored->points = 850.5;
        $scored->position = 2;
        $result = new SheetEventResult($scored, new SheetEvent(EventInput::SERC, 4));

        $this->assertSame(['event' => 'serc:4', 'adjusted_points' => null], array_intersect_key($result->simplifyContext(), array_flip(['event', 'adjusted_points'])));

        $result->adjustedPoints = 1701.0;
        $this->assertSame(1701.0, $result->simplifyContext()['adjusted_points']);
        $this->assertSame('team:12', $result->simplifyContext()['entity']);
    }

    /**
     * Team 12's 61000 with two P900s, its penalty function run unless `$resolved` is false
     */
    private function scored(bool $resolved = true): ScoredResult
    {
        $scored = new ScoredResult(112, new Entry(12, 'team', clubId: 1, teamId: 12), 61000.0, penalties: [900, 900]);

        if ($resolved) {
            $scored->resolvedResult = 61000.0;
            $scored->position = 1;
        }

        return $scored;
    }
}
