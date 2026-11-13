<?php

namespace Tests\Feature\Scoring;

/**
 * How a SERC's marks become points and places: marks weighted and summed, then the formula.
 */
class SercScoringTest extends ScoringTestCase
{
    /** The BULSCA SERC formula: points against the best entry that wasn't disqualified */
    private const AGAINST_BEST = [
        'equation' => 'item.resolvedResult/highest*1000',
        'global_variables' => [['name' => 'highest', 'order' => null, 'expression' => "MAXIMUM(FILTER(results, '!item.isDisqualified()'), 'result')"]],
    ];

    public function test_marks_are_weighted_and_summed_then_scored_against_the_best(): void
    {
        $t = $this->teams('A', 'B');
        $serc = $this->serc(self::AGAINST_BEST, [1, 2, 0.5], [[$t['A'], [10, 5, 4]], [$t['B'], [8, 8, 10]]]);

        $results = $this->eventResults($serc);

        // A: 10 + 5×2 + 4×0.5 = 22. B: 8 + 8×2 + 10×0.5 = 29.
        $this->assertSame([['B', 1, 1000.0], ['A', 2, 758.62]], $this->placings($results));
        $this->assertSame(22.0, $this->resultFor($results, $t['A'])->resolvedResult);
        $this->assertSame(3, $this->resultFor($results, $t['A'])->markingPoints);
    }

    public function test_a_disqualified_entity_comes_last_and_is_not_the_best_to_score_against(): void
    {
        $t = $this->teams('A', 'B', 'C');
        $serc = $this->serc(self::AGAINST_BEST, [1], [[$t['A'], [22]], [$t['B'], [29]], [$t['C'], [35]]]);
        $serc->addEntityDisqualification($t['C'], 301);

        // C still gets points for its 35 marks (over 1000), but places last
        $this->assertSame([['B', 1, 1000.0], ['A', 2, 758.62], ['C', 3, 1206.9]], $this->placings($this->eventResults($serc)));
    }

    public function test_marks_can_be_ranked_into_places_with_ties_sharing_the_better_place(): void
    {
        $t = $this->teams('A', 'B', 'C', 'D');
        $serc = $this->serc(
            ['equation' => "RANK(results, 'resolvedResult', item)", 'global_variables' => [], 'rank_higher' => false],
            [1],
            [[$t['A'], [22]], [$t['B'], [29]], [$t['C'], [29]], [$t['D'], [10]]],
        );

        $this->assertSame([['B', 1, 1.0], ['C', 1, 1.0], ['A', 3, 3.0], ['D', 4, 4.0]], $this->placings($this->eventResults($serc)));
    }

    public function test_an_entity_with_no_marks_is_left_out(): void
    {
        $t = $this->teams('A', 'B');
        $serc = $this->serc(self::AGAINST_BEST, [1, 1], [[$t['A'], [5, 5]]]);

        $this->assertSame([['A', 1, 1000.0]], $this->placings($this->eventResults($serc)));
    }

    public function test_a_league_is_scored_among_its_own_entities_only(): void
    {
        $a = $this->league('A League');
        $b = $this->league('B League');
        $t = ['A' => $this->team('A', league: $a), 'B' => $this->team('B', league: $b), 'C' => $this->team('C', league: $a)];
        $serc = $this->serc(self::AGAINST_BEST, [1], [[$t['A'], [20]], [$t['B'], [40]], [$t['C'], [10]]]);

        $this->assertSame([['B', 1, 1000.0], ['A', 2, 500.0], ['C', 3, 250.0]], $this->placings($this->eventResults($serc)));
        $this->assertSame([['A', 1, 1000.0], ['C', 2, 500.0]], $this->placings($this->eventResults($serc, $a)));
    }
}
