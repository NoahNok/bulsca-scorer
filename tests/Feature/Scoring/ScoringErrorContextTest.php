<?php

namespace Tests\Feature\Scoring;

use App\Exceptions\ScoringException;

/**
 * A formula that fails shows Admins what it could see. The engine knows Entries and Events by key
 * only, so whoever loaded them names them.
 */
class ScoringErrorContextTest extends ScoringTestCase
{
    public function test_a_broken_event_formula_names_the_result_it_failed_on(): void
    {
        $t = $this->teams('A', 'B');
        $event = $this->speedEvent(['equation' => 'item.result / 0'], [[$t['A'], 60000], [$t['B'], 61000]]);

        $context = $this->contextOf(fn() => $this->eventResults($event));

        $this->assertSame($t['A']->getName($this->comp), $context['item']['entity']);
        $this->assertEquals(60000, $context['item']['result']);
        $this->assertNull($context['item']['position'], 'It failed before it was placed');
        $this->assertSame(
            [$t['A']->getName($this->comp), $t['B']->getName($this->comp)],
            array_column($context['results'], 'entity'),
        );
    }

    public function test_a_broken_sheet_formula_names_the_result_and_its_event(): void
    {
        $t = $this->teams('A');
        $event = $this->speedEvent(self::RAW, [[$t['A'], 60000]], name: 'Rope Throw');
        $sheet = $this->sheet(['equation' => 'item.points / 0'], [$event]);

        $context = $this->contextOf(fn() => $this->sheetRows($sheet));

        $this->assertSame($t['A']->getName($this->comp), $context['item']['entity']);
        $this->assertSame($event->getName(), $context['item']['event']);
        $this->assertSame(1, $context['item']['position']);
    }

    private function contextOf(callable $score): array
    {
        try {
            $score();
        } catch (ScoringException $e) {
            return $e->getContext();
        }

        $this->fail('Scoring did not fail');
    }
}
