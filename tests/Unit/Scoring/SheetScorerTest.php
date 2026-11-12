<?php

namespace Tests\Unit\Scoring;

use App\Exceptions\ScoringException;
use App\Scoring\Engine\EventScorer;
use App\Scoring\Engine\ScoredResult;
use App\Scoring\Engine\SheetEventResult;
use App\Scoring\Engine\SheetScorer;
use App\Scoring\Engine\SheetStanding;
use App\Scoring\Input\Entry;
use App\Scoring\Input\EventInput;
use App\Scoring\Input\EventRow;
use App\Scoring\Input\Formula;
use App\Scoring\Input\SheetDefinition;
use App\Scoring\Input\SheetEvent;
use PHPUnit\Framework\TestCase;

/**
 * The Results sheet scorer on inputs built by hand, with no database and no framework.
 * `ResultSheetScoringTest` checks the same rules through the models and the cache.
 *
 * Teams 1, 2 and 3 score 1000, 900 and 800 in speed event 10 (placing 1st, 2nd, 3rd) and 500, 700
 * and 600 in SERC 20 (placing 3rd, 1st, 2nd).
 */
class SheetScorerTest extends TestCase
{
    private const SPEED = [1 => 1000.0, 2 => 900.0, 3 => 800.0];

    private const SERC = [1 => 500.0, 2 => 700.0, 3 => 600.0];

    /**
     * An Event's results as `EventScorer` gives them, each entry's result being its points:
     * `entry id => result`. Entries are teams of Club 1 unless given.
     *
     * @param array<int, Entry> $entries By id, for those that aren't
     * @return ScoredResult[]
     */
    private function scored(string $type, array $results, array $disqualified = [], array $entries = []): array
    {
        $rows = [];
        foreach ($results as $id => $result) {
            $entry = $entries[$id] ?? new Entry($id, 'team', clubId: 1, teamId: $id);
            $rows[] = new EventRow($id + 100, $entry, $result, disqualifications: in_array($id, $disqualified) ? [201] : []);
        }

        return EventScorer::score(new EventInput($type, 1, Formula::fromSchema(['equation' => 'item.resolvedResult']), $rows));
    }

    /**
     * A sheet over speed event 10 and SERC 20, the SERC counting `$sercWeight` times
     *
     * @return SheetStanding[]
     */
    private function score(array $settings = [], float $sercWeight = 1, ?array $speed = null, ?array $serc = null): array
    {
        $sheet = SheetDefinition::fromSchema(
            ['equation' => 'item.points', ...$settings],
            [new SheetEvent(EventInput::SPEED, 10), new SheetEvent(EventInput::SERC, 20, $sercWeight)],
        );

        return SheetScorer::score($sheet, [
            $speed ?? $this->scored(EventInput::SPEED, self::SPEED),
            $serc ?? $this->scored(EventInput::SERC, self::SERC),
        ]);
    }

    /**
     * `[entry id, place, total]` for each row, in the order the scorer gave them
     *
     * @param SheetStanding[] $standings
     */
    private function standings(array $standings): array
    {
        return array_map(fn(SheetStanding $row) => [$row->entity->id, $row->position, $row->totalPoints], $standings);
    }

    public function test_points_are_summed_with_each_events_weight_and_ties_share_a_place(): void
    {
        // 1: 1000 + 500×2. 2: 900 + 700×2. 3: 800 + 600×2.
        $this->assertSame([[2, 1, 2300.0], [1, 2, 2000.0], [3, 2, 2000.0]], $this->standings($this->score(sercWeight: 2)));
    }

    public function test_each_row_carries_its_result_in_every_event(): void
    {
        $first = $this->score(sercWeight: 2)[0];

        $this->assertSame(
            [['speed', 10, 2, 900.0, 900.0], ['serc', 20, 1, 700.0, 1400.0]],
            array_map(fn(SheetEventResult $e) => [$e->event->type, $e->event->id, $e->position, $e->points, $e->adjustedPoints], $first->events),
        );
    }

    public function test_places_can_be_summed_instead_with_the_lowest_winning(): void
    {
        $standings = $this->score(['equation' => 'item.position', 'rank_higher' => false]);

        $this->assertSame([[2, 1, 3.0], [1, 2, 4.0], [3, 3, 5.0]], $this->standings($standings));
    }

    public function test_the_formula_is_worked_out_for_each_event_on_its_own(): void
    {
        // The BULSCA league Formula: each Event's points spread from 100 (the worst) to 1000 (the best)
        $standings = $this->score([
            'equation' => 'item.isDisqualified() ? 0 : ((item.points - minlsp)*mult_fac + 100)',
            'global_variables' => [
                ['name' => 'spread', 'expression' => '900'],
                ['name' => 'valid_teams', 'expression' => "FILTER(results, '!item.isDisqualified()')"],
                ['name' => 'minlsp', 'expression' => "MINIMUM(valid_teams, 'points')"],
                ['name' => 'maxlsp', 'expression' => "MAXIMUM(valid_teams, 'points')"],
                ['name' => 'mult_fac', 'expression' => 'spread/(maxlsp-minlsp)'],
            ],
        ]);

        // Speed: 1000, 550, 100. SERC: 100, 1000, 550.
        $this->assertSame([[2, 1, 1550.0], [1, 2, 1100.0], [3, 3, 650.0]], $this->standings($standings));
    }

    public function test_the_weight_is_applied_after_the_formula(): void
    {
        $standings = $this->score(['equation' => 'item.points + 100'], sercWeight: 2);

        // 2: (900 + 100) + (700 + 100) × 2, not 900 + 100 + 700 × 2 + 100
        $this->assertSame(2600.0, $standings[0]->totalPoints);
    }

    public function test_a_disqualification_counts_for_nothing_unless_the_sheet_ignores_them(): void
    {
        $speed = $this->scored(EventInput::SPEED, self::SPEED, disqualified: [1]);

        $this->assertSame([[2, 1, 1600.0], [3, 2, 1400.0], [1, 3, 500.0]], $this->standings($this->score(speed: $speed)));
        $this->assertSame([[2, 1, 1600.0], [1, 2, 1500.0], [3, 3, 1400.0]], $this->standings($this->score(['ignore_dq' => true], speed: $speed)));
    }

    public function test_tie_breaks_separate_equal_totals_by_place_in_an_event(): void
    {
        // 1 and 3 tie on 2000. 3 placed better in the SERC, 1 in the speed event.
        $bySerc = $this->score(['break_ties' => [['type' => 'serc', 'id' => 20]]], sercWeight: 2);
        $bySpeed = $this->score(['break_ties' => [['type' => 'speed', 'id' => 10]]], sercWeight: 2);

        $this->assertSame([[2, 1, 2300.0], [3, 2, 2000.0], [1, 3, 2000.0]], $this->standings($bySerc));
        $this->assertSame([[2, 1, 2300.0], [1, 2, 2000.0], [3, 3, 2000.0]], $this->standings($bySpeed));
    }

    public function test_a_tie_break_stored_as_a_bare_id_matches_an_event_of_either_type(): void
    {
        $this->assertSame([[2, 1, 2300.0], [3, 2, 2000.0], [1, 3, 2000.0]], $this->standings($this->score(['break_ties' => [20]], sercWeight: 2)));
        // A SERC's id given as a speed event's matches nothing, so the rows stay as they came
        $this->assertSame([[2, 1, 2300.0], [1, 2, 2000.0], [3, 3, 2000.0]], $this->standings($this->score(['break_ties' => [['type' => 'speed', 'id' => 20]]], sercWeight: 2)));
    }

    public function test_the_better_place_breaks_a_tie_whichever_way_totals_rank(): void
    {
        $serc = $this->scored(EventInput::SERC, [1 => 500.0, 2 => 600.0, 3 => 700.0]);

        // Summing places, the lowest winning: 1 is 1st and 3rd, 2 is 2nd twice, 3 is 3rd and 1st.
        // All are on 4, and the better place in the SERC still comes first.
        $standings = $this->score(['equation' => 'item.position', 'rank_higher' => false, 'break_ties' => [['type' => 'serc', 'id' => 20]]], serc: $serc);

        $this->assertSame([[3, 1, 4.0], [2, 2, 4.0], [1, 3, 4.0]], $this->standings($standings));
    }

    public function test_a_row_with_no_result_in_a_tie_break_event_comes_after_those_with_one(): void
    {
        // 4 is only in the speed event, on the 1300 that 3 totals
        $speed = $this->scored(EventInput::SPEED, array_replace(self::SPEED, [3 => 700.0, 4 => 1300.0]));

        $standings = $this->score(['break_ties' => [['type' => 'serc', 'id' => 20]]], speed: $speed);

        $this->assertSame([[2, 1, 1600.0], [1, 2, 1500.0], [3, 3, 1300.0], [4, 4, 1300.0]], $this->standings($standings));
    }

    /** Q7 */
    public function test_a_tie_break_gives_rows_it_does_not_separate_different_places(): void
    {
        $speed = $this->scored(EventInput::SPEED, [1 => 1000.0, 2 => 1000.0]);
        $sheet = fn(array $settings) => SheetScorer::score(
            SheetDefinition::fromSchema(['equation' => 'item.points', ...$settings], [new SheetEvent(EventInput::SPEED, 10)]),
            [$speed],
        );

        $this->assertSame([[1, 1, 1000.0], [2, 1, 1000.0]], $this->standings($sheet([])));
        $this->assertSame([[1, 1, 1000.0], [2, 2, 1000.0]], $this->standings($sheet(['break_ties' => [['type' => 'speed', 'id' => 10]]])));
    }

    public function test_an_entry_missing_from_an_event_scores_only_what_it_has(): void
    {
        $speed = $this->scored(EventInput::SPEED, self::SPEED + [4 => 950.0]);

        $standings = $this->score(speed: $speed);

        $this->assertSame([[2, 1, 1600.0], [1, 2, 1500.0], [3, 3, 1400.0], [4, 4, 950.0]], $this->standings($standings));
        $this->assertCount(1, $standings[3]->events);
    }

    public function test_a_sheet_grouped_on_club_adds_a_clubs_teams_together(): void
    {
        $speed = $this->scored(EventInput::SPEED, self::SPEED + [4 => 4000.0], entries: [4 => new Entry(4, 'team', clubId: 2, teamId: 4)]);

        $standings = $this->score(['group_on' => 'club'], speed: $speed);

        // Club 1 is teams 1, 2 and 3: 2700 + 1800. Each Club's row is under the first of its teams.
        $this->assertSame([[1, 1, 4500.0], [4, 2, 4000.0]], $this->standings($standings));
        $this->assertCount(6, $standings[0]->events);
    }

    public function test_a_row_is_listed_under_the_entry_that_is_what_the_sheet_groups_on(): void
    {
        // The Club itself has a result in the SERC, after its team's in the speed event
        $serc = $this->scored(EventInput::SERC, [7 => 500.0], entries: [7 => new Entry(7, 'club', clubId: 7)]);
        $team = [1 => new Entry(1, 'team', clubId: 7, teamId: 1)];
        $speed = $this->scored(EventInput::SPEED, [1 => 1000.0], entries: $team);

        $standings = $this->score(['group_on' => 'club'], speed: $speed, serc: $serc);

        $this->assertSame([[7, 1, 1500.0]], $this->standings($standings));
        $this->assertSame('club', $standings[0]->entity->type);
    }

    public function test_competitors_can_be_grouped_into_their_teams(): void
    {
        $pair = fn(int $id, int $team) => new Entry($id, 'competitor', clubId: 1, teamId: $team);
        $speed = $this->scored(EventInput::SPEED, [1 => 10.0, 2 => 30.0, 3 => 15.0, 4 => 20.0], entries: [1 => $pair(1, 50), 2 => $pair(2, 60), 3 => $pair(3, 50), 4 => $pair(4, 60)]);
        $sheet = fn(string $on) => SheetScorer::score(
            SheetDefinition::fromSchema(['equation' => 'item.points', 'group_on' => $on], [new SheetEvent(EventInput::SPEED, 10)]),
            [$speed],
        );

        // By team: 60 has 30 + 20, 50 has 10 + 15, each under whichever of the pair placed better
        $this->assertSame([[2, 1, 50.0], [3, 2, 25.0]], $this->standings($sheet('team')));
        $this->assertSame([[2, 1, 30.0], [4, 2, 20.0], [3, 3, 15.0], [1, 4, 10.0]], $this->standings($sheet('competitor')));
    }

    public function test_an_events_results_are_left_as_they_were_so_sheets_can_share_them(): void
    {
        $speed = $this->scored(EventInput::SPEED, self::SPEED);
        $serc = $this->scored(EventInput::SERC, self::SERC);
        $before = [array_map(fn($r) => clone $r, $speed), array_map(fn($r) => clone $r, $serc)];

        $doubled = $this->score(['equation' => 'item.points * 2'], speed: $speed, serc: $serc);
        $plain = $this->score(speed: $speed, serc: $serc);

        $this->assertSame([[2, 1, 3200.0], [1, 2, 3000.0], [3, 3, 2800.0]], $this->standings($doubled));
        $this->assertSame([[2, 1, 1600.0], [1, 2, 1500.0], [3, 3, 1400.0]], $this->standings($plain));
        $this->assertEquals($before, [$speed, $serc]);
        $this->assertSame($speed[1], $plain[0]->events[0]->scored);
    }

    public function test_the_formula_is_worked_out_even_for_an_event_with_no_results(): void
    {
        $settings = ['equation' => 'item.points * share', 'global_variables' => [['name' => 'share', 'expression' => '1 / COUNT(results)']]];

        // A third each of 1000, 900, 800 and of 500, 700, 600
        $this->assertSame([[2, 1, 533.33], [1, 2, 500.0], [3, 3, 466.67]], array_map(
            fn(array $row) => [$row[0], $row[1], round($row[2], 2)],
            $this->standings($this->score($settings)),
        ));

        $this->expectException(ScoringException::class);
        $this->expectExceptionMessage("Error evaluating global variable 'share': Division by zero");

        $this->score($settings, serc: []);
    }

    public function test_a_sheet_with_no_events_has_no_rows(): void
    {
        $this->assertSame([], SheetScorer::score(SheetDefinition::fromSchema(['equation' => 'item.points'], []), []));
    }
}
