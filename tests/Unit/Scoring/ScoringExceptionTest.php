<?php

namespace Tests\Unit\Scoring;

use App\Exceptions\ScoringException;
use App\Models\Interfaces\IScoringContextSimplify;
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
}
