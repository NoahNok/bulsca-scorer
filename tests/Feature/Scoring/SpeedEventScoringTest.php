<?php

namespace Tests\Feature\Scoring;

/**
 * How a speed event's times become points and places: the formula, ranking, ties, DQs, penalties,
 * Leagues and combined (pairs) results.
 */
class SpeedEventScoringTest extends ScoringTestCase
{
    public function test_times_become_points_and_the_most_points_wins(): void
    {
        $t = $this->teams('A', 'B', 'C');
        $event = $this->speedEvent(self::TIME_POINTS, [[$t['A'], 66000], [$t['B'], 60000], [$t['C'], 72000]]);

        $this->assertSame([['B', 1, 1000.0], ['A', 2, 900.0], ['C', 3, 800.0]], $this->placings($event->getRankedResults()));
    }

    public function test_the_fewest_points_wins_when_the_formula_ranks_lower(): void
    {
        $t = $this->teams('A', 'B', 'C');
        $event = $this->speedEvent([...self::RAW, 'rank_higher' => false], [[$t['A'], 66000], [$t['B'], 60000], [$t['C'], 72000]]);

        $this->assertSame([['B', 1, 60000.0], ['A', 2, 66000.0], ['C', 3, 72000.0]], $this->placings($event->getRankedResults()));
    }

    public function test_equal_points_share_a_place_and_the_next_place_is_skipped(): void
    {
        $t = $this->teams('A', 'B', 'C', 'D');
        $event = $this->speedEvent(self::TIME_POINTS, [[$t['A'], 66000], [$t['B'], 66000], [$t['C'], 60000], [$t['D'], 72000]]);

        $this->assertSame([['C', 1, 1000.0], ['A', 2, 900.0], ['B', 2, 900.0], ['D', 4, 800.0]], $this->placings($event->getRankedResults()));
    }

    public function test_an_entity_without_a_time_is_left_out(): void
    {
        $t = $this->teams('A', 'B');
        $event = $this->speedEvent(self::TIME_POINTS, [[$t['A'], 66000], [$t['B'], null]]);

        $this->assertSame([['A', 1, 900.0]], $this->placings($event->getRankedResults()));
    }

    public function test_an_event_without_a_formula_has_no_results(): void
    {
        $t = $this->teams('A');
        $event = $this->speedEvent(null, [[$t['A'], 66000]]);

        $this->assertSame([], $event->getRankedResults());
    }

    public function test_disqualified_entities_come_last_and_share_a_place_but_keep_their_points(): void
    {
        $t = $this->teams('A', 'B', 'C', 'D');
        $event = $this->speedEvent(self::TIME_POINTS, [[$t['A'], 60000], [$t['B'], 66000], [$t['C'], 72000], [$t['D'], 63000]]);
        $event->addEntityDisqualification($t['A'], 201);
        $event->addEntityDisqualification($t['D'], 202);

        $results = $event->getRankedResults();

        // The two DQs are still in points order between themselves
        $this->assertSame([['B', 1, 900.0], ['C', 2, 800.0], ['A', 3, 1000.0], ['D', 3, 950.0]], $this->placings($results));
        $this->assertTrue($this->resultFor($results, $t['A'])->isDisqualified());
        $this->assertSame('DQ201', $this->resultFor($results, $t['A'])->getDisqualificationsString());
    }

    public function test_a_formula_can_let_disqualified_entities_rank_on_their_points(): void
    {
        $t = $this->teams('A', 'B', 'C');
        $event = $this->speedEvent([...self::TIME_POINTS, 'allow_disqualified_to_rank' => true], [[$t['A'], 60000], [$t['B'], 66000], [$t['C'], 72000]]);
        $event->addEntityDisqualification($t['A'], 201);

        $this->assertSame([['A', 1, 1000.0], ['B', 2, 900.0], ['C', 3, 800.0]], $this->placings($event->getRankedResults()));
    }

    public function test_dnf_dns_and_oot_are_disqualifications_with_their_own_names(): void
    {
        $t = $this->teams('A', 'B', 'C');
        $event = $this->speedEvent(self::TIME_POINTS, [[$t['A'], 60000], [$t['B'], 60000], [$t['C'], 60000]]);
        $event->addEntityDisqualification($t['A'], 99915);
        $event->addEntityDisqualification($t['B'], 99904);
        $event->addEntityDisqualification($t['C'], 99901);

        $results = $event->getRankedResults();

        $this->assertSame('DNF', $this->resultFor($results, $t['A'])->getDisqualificationsString());
        $this->assertSame('DNS', $this->resultFor($results, $t['B'])->getDisqualificationsString());
        $this->assertSame('OOT', $this->resultFor($results, $t['C'])->getDisqualificationsString());
        $this->assertTrue($this->resultFor($results, $t['A'])->isDNF());
        $this->assertFalse($this->resultFor($results, $t['B'])->isDNF());
    }

    public function test_the_penalty_function_changes_the_result_of_an_entity_with_penalties(): void
    {
        $t = $this->teams('A', 'B');
        $event = $this->speedEvent(
            [...self::TIME_POINTS, 'penalty_func' => 'item.result + COUNT(item.penalties)*15000'],
            [[$t['A'], 60000], [$t['B'], 66000]],
        );
        $event->addEntityPenalty($t['A'], 801);
        $event->addEntityPenalty($t['A'], 802);

        $results = $event->getRankedResults();

        // A: 60s + 2 × 15s = 90s, which is 500 points. B has no penalties, so its time stands.
        $this->assertSame([['B', 1, 900.0], ['A', 2, 500.0]], $this->placings($results));
        $this->assertSame(90000.0, $this->resultFor($results, $t['A'])->resolvedResult);
        $this->assertEquals(60000, $this->resultFor($results, $t['A'])->result);
        $this->assertSame('P801, P802', $this->resultFor($results, $t['A'])->getPenaltiesString());
        $this->assertSame(66000.0, $this->resultFor($results, $t['B'])->resolvedResult);
    }

    public function test_the_penalty_function_only_runs_for_everyone_when_forced(): void
    {
        $t = $this->teams('A', 'B');
        $formula = [...self::RAW, 'rank_higher' => false, 'penalty_func' => 'item.isDisqualified() ? 600000 : item.result + 1000'];

        $unforced = $this->speedEvent($formula, [[$t['A'], 60000], [$t['B'], 66000]]);
        $unforced->addEntityDisqualification($t['B'], 201);
        $forced = $this->speedEvent([...$formula, 'force_penalty' => true, 'allow_disqualified_to_rank' => true], [[$t['A'], 60000], [$t['B'], 66000]]);
        $forced->addEntityDisqualification($t['B'], 201);

        $this->assertSame([['A', 1, 60000.0], ['B', 2, 66000.0]], $this->placings($unforced->getRankedResults()));
        $this->assertSame([['A', 1, 61000.0], ['B', 2, 600000.0]], $this->placings($forced->getRankedResults()));
    }

    public function test_automatic_penalties_are_given_for_being_slower_than_the_seed_time(): void
    {
        $t = $this->teams('A', 'B', 'C');
        $event = $this->speedEvent([
            ...self::TIME_POINTS,
            'penalty_func' => 'item.result + COUNT(item.penalties)*15000',
            // More than 10% over the seed time: one P900, and another for every further 15s
            'auto_penalties' => [['code' => '900', 'condition' => '(item.result - (item.seed)) > (item.seed*0.1)', 'amount' => '1 + FLOOR((item.result - (item.seed*1.1)) / 15000)']],
        ], [[$t['A'], 66000], [$t['B'], 67000], [$t['C'], 82000]]);

        foreach ($t as $team) {
            $this->seedTime($event, $team, 60000);
        }

        $results = $event->getRankedResults();

        // A is exactly 10% over: no penalty. B: 67s + 15s = 82s. C: 82s + 2 × 15s = 112s.
        $this->assertSame([['A', 1, 900.0], ['B', 2, 633.33], ['C', 3, 133.33]], $this->placings($results));
        $this->assertSame([], $this->resultFor($results, $t['A'])->penalties->all());
        $this->assertSame('P900', $this->resultFor($results, $t['B'])->getPenaltiesString());
        $this->assertSame('P900 (x2)', $this->resultFor($results, $t['C'])->getPenaltiesString());
    }

    public function test_each_entry_is_scored_on_its_own_seed_time(): void
    {
        $t = $this->teams('A', 'B');
        $event = $this->speedEvent([...self::RAW, 'rank_higher' => false, 'penalty_func' => 'item.seed', 'force_penalty' => true], [[$t['A'], 70000], [$t['B'], 70000]]);

        $this->seedTime($event, $t['B'], 61000);
        $this->seedTime($event, $t['A'], 60000);

        // Seeds are matched to results by entry, not by the order either was entered in
        $this->assertSame([['A', 1, 60000.0], ['B', 2, 61000.0]], $this->placings($event->getRankedResults()));
    }

    public function test_a_rank_equation_can_move_a_place_after_ranking(): void
    {
        $t = $this->teams('A', 'B', 'C');
        $event = $this->speedEvent([
            'equation' => "RANK(results, 'resolvedResult', item, 'ASC')",
            'global_variables' => [],
            'rank_higher' => false,
            'penalty_func' => 'item.isDisqualified() ? 600000 : item.result',
            'force_penalty' => true,
            'allow_disqualified_to_rank' => true,
            // With fewer than 4 entries a DQ is 4th, not last of however many there are
            'rank_equation' => 'teams < 4 && item.isDisqualified() ? 4 : rank',
        ], [[$t['A'], 50000], [$t['B'], 40000], [$t['C'], 45000]]);
        $event->addEntityDisqualification($t['B'], 201);

        $this->assertSame([['C', 1, 1.0], ['A', 2, 2.0], ['B', 4, 3.0]], $this->placings($event->getRankedResults()));
    }

    public function test_tied_times_take_the_middle_of_the_places_they_cover_with_rankmedian(): void
    {
        $league = $this->league('Open');
        $team = $this->team('A');
        $names = ['V' => 10000, 'W' => 20000, 'X' => 20000, 'Y' => 30000, 'Z' => 40000];

        $event = $this->speedEvent([
            'equation' => "TOTAL > 4 ? RANKMEDIAN(results, 'resolvedResult', item, 'ASC') : RANK(results, 'resolvedResult', item, 'ASC')",
            'global_variables' => [['name' => 'TOTAL', 'order' => null, 'expression' => 'COUNT(results)']],
            'rank_higher' => false,
        ], scores: 'competitor');

        foreach ($names as $name => $time) {
            $this->time($event, $this->competitor($name, $team, $league), $time);
        }

        // W and X tie for 2nd and 3rd: 2.5 points each, and both placed 2nd
        $this->assertSame(
            [['V', 1, 1.0], ['W', 2, 2.5], ['X', 2, 2.5], ['Y', 4, 4.0], ['Z', 5, 5.0]],
            $this->placings($event->getRankedResults()),
        );
    }

    public function test_grouping_adds_together_the_results_of_a_pair(): void
    {
        $league = $this->league('Senior Pairs');
        $one = $this->team('Pair 1');
        $two = $this->team('Pair 2');

        $event = $this->speedEvent([
            'equation' => "RANK(results, 'resolvedResult', item, 'ASC')",
            'global_variables' => [],
            'rank_higher' => false,
            'group_by' => ['league', 'team'],
        ], scores: 'competitor');

        $this->time($event, $first = $this->competitor('P', $one, $league), 10000);
        $this->time($event, $this->competitor('Q', $two, $league), 9000);
        $this->time($event, $this->competitor('R', $one, $league), 15000);
        $this->time($event, $this->competitor('S', $two, $league), 14000);

        $results = $event->getRankedResults();

        // One row per pair, under whichever of the pair's results came first: Q+S 23s, P+R 25s
        $this->assertSame([['Q', 1, 1.0], ['P', 2, 2.0]], $this->placings($results));
        $this->assertSame(25000.0, $this->resultFor($results, $first)->resolvedResult);
        $this->assertTrue($this->resultFor($results, $first)->isCombined());
        $this->assertSame(['P', 'R'], $this->resultFor($results, $first)->combined->map(fn($r) => $r->entity->name)->all());
    }

    public function test_a_league_is_scored_among_its_own_entities_only(): void
    {
        $a = $this->league('A League');
        $b = $this->league('B League');
        $t = [
            'A' => $this->team('A', league: $a),
            'B' => $this->team('B', league: $b),
            'C' => $this->team('C', league: $a),
        ];

        // Points against the fastest time in whatever set is being scored
        $event = $this->speedEvent([
            'equation' => 'fastest/item.resolvedResult*1000',
            'global_variables' => [['name' => 'fastest', 'order' => null, 'expression' => "MINIMUM(results, 'resolvedResult')"]],
        ], [[$t['A'], 66000], [$t['B'], 60000], [$t['C'], 72000]]);

        $this->assertSame([['B', 1, 1000.0], ['A', 2, 909.09], ['C', 3, 833.33]], $this->placings($event->getRankedResults()));
        $this->assertSame([['A', 1, 1000.0], ['C', 2, 916.67]], $this->placings($event->getRankedResults($a)));
        $this->assertSame([['B', 1, 1000.0]], $this->placings($event->getRankedResults($b)));
    }
}
