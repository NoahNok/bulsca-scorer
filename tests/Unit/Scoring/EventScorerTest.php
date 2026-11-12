<?php

namespace Tests\Unit\Scoring;

use App\Exceptions\ScoringException;
use App\Scoring\Engine\EventScorer;
use App\Scoring\Engine\ScoredResult;
use App\Scoring\Input\Entry;
use App\Scoring\Input\EventInput;
use App\Scoring\Input\EventRow;
use App\Scoring\Input\Formula;
use PHPUnit\Framework\TestCase;

/**
 * The Event scorer on inputs built by hand, with no database and no framework: one rule per test,
 * the answer worked out in a comment. `SpeedEventScoringTest` and `SercScoringTest` check the same
 * rules through the models and the loader.
 */
class EventScorerTest extends TestCase
{
    /** The result as it is */
    private const RAW = ['equation' => 'item.resolvedResult'];

    /** One penalty for being more than 10% over the seed time, and another for every further 15s */
    private const SLOWER_THAN_SEED = [
        'code' => '900',
        'condition' => '(item.result - (item.seed)) > (item.seed*0.1)',
        'amount' => '1 + FLOOR((item.result - (item.seed*1.1)) / 15000)',
    ];

    private function team(int $id, array $leagues = [], array $columns = []): Entry
    {
        return new Entry($id, 'team', clubId: 1, teamId: $id, leagueIds: $leagues, attributes: $columns);
    }

    private function competitor(int $id, int $team, array $leagues = []): Entry
    {
        return new Entry($id, 'competitor', clubId: 1, teamId: $team, leagueIds: $leagues);
    }

    /**
     * A row for each result, `entry id => result`, entered by teams in no League. The row's id is
     * the entry's id plus 100.
     *
     * @return EventRow[]
     */
    private function rows(array $results, array $disqualified = []): array
    {
        $rows = [];
        foreach ($results as $id => $result) {
            $rows[] = new EventRow($id + 100, $this->team($id), $result, disqualifications: in_array($id, $disqualified) ? [201] : []);
        }

        return $rows;
    }

    /**
     * @param EventRow[] $rows
     * @return ScoredResult[]
     */
    private function score(array $formula, array $rows, ?int $league = null, string $type = EventInput::SPEED): array
    {
        return EventScorer::score(new EventInput($type, 1, Formula::fromSchema($formula), $rows), $league);
    }

    /**
     * `[entry id, place, points]` for each result, in the order the scorer gave them
     *
     * @param ScoredResult[] $results
     */
    private function placings(array $results): array
    {
        return array_map(fn(ScoredResult $result) => [$result->entity->id, $result->position, $result->points], $results);
    }

    /**
     * @param ScoredResult[] $results
     */
    private function resultFor(array $results, int $entryId): ScoredResult
    {
        foreach ($results as $result) {
            if ($result->entity->id === $entryId) {
                return $result;
            }
        }

        $this->fail("No result for entry {$entryId}");
    }

    private function codes(ScoredResult $result, string $of = 'penalties'): array
    {
        return $result->{$of}->pluck('code')->all();
    }

    public function test_the_most_points_is_placed_first(): void
    {
        $results = $this->score(self::RAW, $this->rows([1 => 10.0, 2 => 30.0, 3 => 20.0]));

        $this->assertSame([[2, 1, 30.0], [3, 2, 20.0], [1, 3, 10.0]], $this->placings($results));
        $this->assertSame(102, $results[0]->id);
    }

    public function test_the_fewest_points_is_placed_first_when_ranking_lower(): void
    {
        $results = $this->score([...self::RAW, 'rank_higher' => false], $this->rows([1 => 10.0, 2 => 30.0, 3 => 20.0]));

        $this->assertSame([[1, 1, 10.0], [3, 2, 20.0], [2, 3, 30.0]], $this->placings($results));
    }

    public function test_equal_points_share_a_place_and_the_next_place_is_skipped(): void
    {
        $results = $this->score(self::RAW, $this->rows([1 => 20.0, 2 => 10.0, 3 => 30.0, 4 => 20.0]));

        // 1 and 4 tie for 2nd, listed in the order their rows were given; 2 is 4th, not 3rd
        $this->assertSame([[3, 1, 30.0], [1, 2, 20.0], [4, 2, 20.0], [2, 4, 10.0]], $this->placings($results));
    }

    public function test_a_time_stored_as_a_string_is_scored_as_a_number(): void
    {
        $results = $this->score([...self::RAW, 'rank_higher' => false], $this->rows([1 => '100000', 2 => '9000']));

        // As text "100000" sorts before "9000"; as times 9s beats 100s
        $this->assertSame([[2, 1, 9000.0], [1, 2, 100000.0]], $this->placings($results));
        $this->assertSame('9000', $results[0]->result);
        $this->assertSame(9000.0, $results[0]->resolvedResult);
    }

    public function test_disqualified_results_share_last_place_whatever_their_points(): void
    {
        $results = $this->score(self::RAW, $this->rows([1 => 50.0, 2 => 30.0, 3 => 40.0], disqualified: [1, 3]));

        // 2 is the only one left standing. 1 and 3 keep their points and order but are both 2nd.
        $this->assertSame([[2, 1, 30.0], [1, 2, 50.0], [3, 2, 40.0]], $this->placings($results));
        $this->assertTrue($results[1]->isDisqualified());
        $this->assertFalse($results[0]->isDisqualified());
    }

    public function test_disqualified_results_are_placed_on_their_points_when_allowed_to_rank(): void
    {
        $results = $this->score(
            [...self::RAW, 'allow_disqualified_to_rank' => true],
            $this->rows([1 => 50.0, 2 => 30.0, 3 => 40.0], disqualified: [1, 3]),
        );

        $this->assertSame([[1, 1, 50.0], [3, 2, 40.0], [2, 3, 30.0]], $this->placings($results));
    }

    public function test_a_serc_ties_its_disqualified_results_even_when_they_are_allowed_to_rank(): void
    {
        $formula = [...self::RAW, 'allow_disqualified_to_rank' => true];
        $rows = $this->rows([1 => 50.0, 2 => 60.0, 3 => 40.0, 4 => 20.0], disqualified: [1, 3]);

        // Sorted on points either way. In a SERC 1 and 3, next to each other, share 2nd and 4 is 4th.
        $this->assertSame([[2, 1, 60.0], [1, 2, 50.0], [3, 2, 40.0], [4, 4, 20.0]], $this->placings($this->score($formula, $rows, type: EventInput::SERC)));
        $this->assertSame([[2, 1, 60.0], [1, 2, 50.0], [3, 3, 40.0], [4, 4, 20.0]], $this->placings($this->score($formula, $rows)));
    }

    public function test_a_sercs_disqualified_results_share_last_place(): void
    {
        $rows = [
            new EventRow(101, $this->team(1), 50.0, disqualifications: [201], markingPoints: 12),
            new EventRow(102, $this->team(2), 30.0, markingPoints: 12),
            new EventRow(103, $this->team(3), 40.0, disqualifications: [201], markingPoints: 11),
        ];

        $results = $this->score(self::RAW, $rows, type: EventInput::SERC);

        $this->assertSame([[2, 1, 30.0], [1, 2, 50.0], [3, 2, 40.0]], $this->placings($results));
        $this->assertSame([12, 12, 11], array_map(fn(ScoredResult $result) => $result->total_marking_points, $results));
    }

    public function test_a_league_is_scored_among_its_own_entries_only(): void
    {
        // Points against the fastest time in whatever set is being scored
        $formula = [
            'equation' => 'fastest/item.resolvedResult*1000',
            'global_variables' => [['name' => 'fastest', 'expression' => "MINIMUM(results, 'resolvedResult')"]],
        ];
        $rows = [
            new EventRow(101, $this->team(1, leagues: [7]), 80000.0),
            new EventRow(102, $this->team(2, leagues: [8]), 50000.0),
            new EventRow(103, $this->team(3, leagues: [7, 8]), 100000.0),
            new EventRow(104, $this->team(4), 40000.0),
        ];

        // Everyone: 4 is fastest at 40s, so 2 has 40/50, 1 has 40/80 and 3 has 40/100
        $this->assertSame([[4, 1, 1000.0], [2, 2, 800.0], [1, 3, 500.0], [3, 4, 400.0]], $this->placings($this->score($formula, $rows)));
        // League 7 is 1 and 3: 1 is its fastest
        $this->assertSame([[1, 1, 1000.0], [3, 2, 800.0]], $this->placings($this->score($formula, $rows, league: 7)));
        // League 8 is 2 and 3, which is in both
        $this->assertSame([[2, 1, 1000.0], [3, 2, 500.0]], $this->placings($this->score($formula, $rows, league: 8)));
    }

    public function test_the_penalty_function_works_out_the_result_of_one_with_penalties(): void
    {
        $formula = [...self::RAW, 'rank_higher' => false, 'penalty_func' => 'item.result + COUNT(item.penalties)*15000'];
        $rows = [
            new EventRow(101, $this->team(1), '60000', penalties: [801, 802]),
            new EventRow(102, $this->team(2), '66000'),
        ];

        $results = $this->score($formula, $rows);

        // 1: 60s + 2 × 15s = 90s. 2 has no penalties, so its time stands.
        $this->assertSame([[2, 1, 66000.0], [1, 2, 90000.0]], $this->placings($results));
        $this->assertSame('60000', $results[1]->result);
        $this->assertSame([801, 802], $this->codes($results[1]));
        $this->assertTrue($results[1]->hasPenalties());
        $this->assertFalse($results[0]->hasPenalties());
    }

    public function test_the_penalty_function_only_runs_for_everyone_when_forced(): void
    {
        $formula = [...self::RAW, 'rank_higher' => false, 'allow_disqualified_to_rank' => true, 'penalty_func' => 'item.isDisqualified() ? 600000 : item.result + 1000'];
        $rows = $this->rows([1 => 60000.0, 2 => 66000.0], disqualified: [2]);

        // Nobody has a penalty, so unforced it runs for nobody
        $this->assertSame([[1, 1, 60000.0], [2, 2, 66000.0]], $this->placings($this->score($formula, $rows)));
        $this->assertSame([[1, 1, 61000.0], [2, 2, 600000.0]], $this->placings($this->score([...$formula, 'force_penalty' => true], $rows)));
    }

    public function test_automatic_penalties_are_given_for_being_slower_than_the_seed_time(): void
    {
        $formula = [
            ...self::RAW,
            'rank_higher' => false,
            'penalty_func' => 'item.result + COUNT(item.penalties)*15000',
            'auto_penalties' => [self::SLOWER_THAN_SEED],
        ];
        $rows = [
            new EventRow(101, $this->team(1), 66000.0, seed: 60000),
            new EventRow(102, $this->team(2), 67000.0, seed: 60000),
            new EventRow(103, $this->team(3), 82000.0, penalties: [801], seed: 60000),
            new EventRow(104, $this->team(4), 61000.0),
        ];

        $results = $this->score($formula, $rows);

        // 1 is exactly 10% over: no penalty. 2: one, so 67s + 15s = 82s. 3: 1 + FLOOR(16s / 15s) = 2
        // on top of the one it was given, so 82s + 3 × 15s = 127s. 4 has no seed, which counts as 0
        // (Q6): 1 + FLOOR(61s / 15s) = 5, so 61s + 5 × 15s = 136s.
        $this->assertSame([[1, 1, 66000.0], [2, 2, 82000.0], [3, 3, 127000.0], [4, 4, 136000.0]], $this->placings($results));
        $this->assertSame([], $this->codes($results[0]));
        $this->assertSame(['900'], $this->codes($results[1]));
        $this->assertSame([801, '900', '900'], $this->codes($results[2]));
        $this->assertSame(['900', '900', '900', '900', '900'], $this->codes($results[3]));
    }

    /** Q1 */
    public function test_an_automatic_disqualification_takes_the_code_of_the_last_automatic_penalty(): void
    {
        $formula = [
            ...self::RAW,
            'rank_higher' => false,
            'auto_penalties' => [
                ['code' => '900', 'condition' => 'false', 'amount' => '1'],
                ['code' => '901', 'condition' => 'false', 'amount' => '1'],
            ],
            'auto_disqualifications' => [['code' => '999', 'condition' => 'item.result > 100000', 'amount' => '1']],
        ];

        $results = $this->score($formula, $this->rows([1 => 120000.0, 2 => 70000.0]));

        $this->assertSame([[2, 1, 70000.0], [1, 2, 120000.0]], $this->placings($results));
        $this->assertSame(['901'], $this->codes($results[1], 'disqualifications'));
        $this->assertSame([], $this->codes($results[0], 'disqualifications'));
    }

    /** Q4 */
    public function test_the_seed_can_be_read_by_the_penalty_function_but_is_zero_in_the_equation(): void
    {
        $rows = [new EventRow(101, $this->team(1), 70000.0, seed: 60000)];

        $byPenaltyFunction = $this->score([...self::RAW, 'penalty_func' => 'item.seed', 'force_penalty' => true], $rows);
        $byEquation = $this->score(['equation' => 'item.seed + 1'], $rows);

        $this->assertSame(60000.0, $byPenaltyFunction[0]->resolvedResult);
        $this->assertSame(1.0, $byEquation[0]->points);
        $this->assertSame(0, $byEquation[0]->seed);
    }

    public function test_grouping_adds_together_the_results_of_a_pair(): void
    {
        $formula = [
            'equation' => "RANK(results, 'resolvedResult', item, 'ASC')",
            'rank_higher' => false,
            'group_by' => ['league', 'team'],
        ];
        $rows = [
            new EventRow(101, $this->competitor(1, team: 10, leagues: [7]), 10000.0),
            new EventRow(102, $this->competitor(2, team: 20, leagues: [7]), 9000.0),
            new EventRow(103, $this->competitor(3, team: 10, leagues: [7]), 15000.0),
            new EventRow(104, $this->competitor(4, team: 20, leagues: [7]), 14000.0),
        ];

        $results = $this->score($formula, $rows);

        // One result per pair, under whichever of the pair's rows came first: 2+4 23s, 1+3 25s
        $this->assertSame([[2, 1, 1.0], [1, 2, 2.0]], $this->placings($results));
        $this->assertSame(25000.0, $results[1]->resolvedResult);
        $this->assertSame(10000.0, $results[1]->result);
        $this->assertTrue($results[1]->isCombined());
        $this->assertSame([1, 3], $results[1]->combined->map(fn(ScoredResult $part) => $part->entity->id)->all());
    }

    public function test_grouping_keeps_apart_the_same_team_in_different_leagues(): void
    {
        $formula = [...self::RAW, 'rank_higher' => false, 'group_by' => ['league', 'team']];
        $rows = [
            new EventRow(101, $this->competitor(1, team: 10, leagues: [7]), 10000.0),
            new EventRow(102, $this->competitor(2, team: 10, leagues: [8, 9]), 9000.0),
            new EventRow(103, $this->competitor(3, team: 10, leagues: [8]), 15000.0),
            new EventRow(104, $this->competitor(4, team: 10), 14000.0),
        ];

        $results = $this->score($formula, $rows);

        // An entry is grouped under its first League: 2 and 3 are both under 8. 1 and 4 (in no
        // League) each stand alone, and a result on its own isn't a combined one.
        $this->assertSame([[1, 1, 10000.0], [4, 2, 14000.0], [2, 3, 24000.0]], $this->placings($results));
        $this->assertFalse($results[0]->isCombined());
        $this->assertTrue($results[2]->isCombined());
    }

    public function test_a_rank_equation_can_move_a_place_after_ranking(): void
    {
        $formula = [
            'equation' => "RANK(results, 'resolvedResult', item, 'ASC')",
            'rank_higher' => false,
            'penalty_func' => 'item.isDisqualified() ? 600000 : item.result',
            'force_penalty' => true,
            'allow_disqualified_to_rank' => true,
            // With fewer than 4 entries a DQ is 4th, not last of however many there are
            'rank_equation' => 'teams < 4 && item.isDisqualified() ? 4 : rank',
        ];

        $three = $this->score($formula, $this->rows([1 => 50000.0, 2 => 40000.0, 3 => 45000.0], disqualified: [2]));
        $four = $this->score($formula, $this->rows([1 => 50000.0, 2 => 40000.0, 3 => 45000.0, 4 => 55000.0], disqualified: [2]));

        $this->assertSame([[3, 1, 1.0], [1, 2, 2.0], [2, 4, 3.0]], $this->placings($three));
        $this->assertSame([[3, 1, 1.0], [1, 2, 2.0], [4, 3, 3.0], [2, 4, 4.0]], $this->placings($four));
    }

    /** Q9 */
    public function test_a_league_with_no_entries_still_has_its_formula_worked_out(): void
    {
        $rows = [new EventRow(101, $this->team(1, leagues: [7]), 60000.0)];

        // Nothing to work the equation out for, so a formula that needs no results gives none
        $this->assertSame([], $this->score(self::RAW, $rows, league: 8));

        // But the global variables are worked out, and this one divides by how many results there are
        $perResult = ['equation' => 'share', 'global_variables' => [['name' => 'share', 'expression' => '1000 / COUNT(results)']]];
        $this->assertSame([[1, 1, 1000.0]], $this->placings($this->score($perResult, $rows, league: 7)));

        $this->expectException(ScoringException::class);
        $this->expectExceptionMessage("Error evaluating global variable 'share': Division by zero");

        $this->score($perResult, $rows, league: 8);
    }

    public function test_a_formula_can_read_an_entrys_stored_columns(): void
    {
        $formula = [...self::RAW, 'rank_higher' => false, 'penalty_func' => 'item.result - item.entity.st_time', 'force_penalty' => true];
        $rows = [
            new EventRow(101, $this->team(1, columns: ['st_time' => 5000, 'team' => 'A']), 60000.0),
            new EventRow(102, $this->team(2, columns: ['st_time' => 20000]), 70000.0),
            new EventRow(103, $this->team(3), 52000.0),
        ];

        $results = $this->score($formula, $rows);

        // 2: 70s - 20s = 50s. 3 has no such column, which reads as nothing: 52s. 1: 60s - 5s = 55s.
        $this->assertSame([[2, 1, 50000.0], [3, 2, 52000.0], [1, 3, 55000.0]], $this->placings($results));
        $this->assertSame('A', $results[2]->entity->team);
    }

    public function test_an_event_without_an_equation_cannot_be_scored(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->score(['equation' => ''], $this->rows([1 => 10.0]));
    }
}
