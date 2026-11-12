<?php

namespace Tests\Feature\Scoring;

use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\SERC;

/**
 * How a Results sheet turns its Events' results into one standing: the Formula per Event, Event
 * weights, DQs, grouping, Leagues, tie-breaks and the cache.
 *
 * Teams A, B and C score 1000, 900 and 800 in the speed event (placing 1st, 2nd, 3rd) and 500, 700
 * and 600 in the SERC (placing 3rd, 1st, 2nd).
 */
class ResultSheetScoringTest extends ScoringTestCase
{
    /** @var CompetitionTeam[] */
    private array $t;

    private CompetitionSpeedEvent $speed;

    private SERC $serc;

    protected function setUp(): void
    {
        parent::setUp();

        $this->t = $this->teams('A', 'B', 'C');
        $this->speed = $this->speedEvent(self::RAW, [[$this->t['A'], 1000], [$this->t['B'], 900], [$this->t['C'], 800]]);
        $this->serc = $this->serc(self::RAW, [1], [[$this->t['A'], [500]], [$this->t['B'], [700]], [$this->t['C'], [600]]]);
    }

    public function test_points_are_summed_with_each_events_weight_and_ties_share_a_place(): void
    {
        $sheet = $this->sheet([], [$this->speed, [$this->serc, 2]]);

        // A: 1000 + 500×2. B: 900 + 700×2. C: 800 + 600×2.
        $this->assertSame([['B', 1, 2300.0], ['A', 2, 2000.0], ['C', 2, 2000.0]], $this->standings($sheet->getResults()));
    }

    public function test_each_entitys_row_carries_its_result_in_every_event(): void
    {
        $sheet = $this->sheet([], [$this->speed, [$this->serc, 2]]);

        $b = $sheet->getResults()->first();

        $this->assertSame(
            [['speed', 2, 900.0, 900.0], ['serc', 1, 700.0, 1400.0]],
            $b->events->map(fn($e) => [$e->event->getType(), $e->position, $e->points, $e->adjustedPoints])->all(),
        );
    }

    public function test_places_can_be_summed_instead_with_the_lowest_winning(): void
    {
        $sheet = $this->sheet(['equation' => 'item.position', 'rank_higher' => false], [$this->speed, $this->serc]);

        $this->assertSame([['B', 1, 3.0], ['A', 2, 4.0], ['C', 3, 5.0]], $this->standings($sheet->getResults()));
    }

    public function test_a_formula_can_rescale_each_events_points(): void
    {
        // The BULSCA league Formula: each Event's points spread from 100 (the worst) to 1000 (the best)
        $sheet = $this->sheet([
            'equation' => 'item.isDisqualified() ? 0 : ((item.points - minlsp)*mult_fac + 100)',
            'global_variables' => [
                ['name' => 'spread', 'order' => null, 'expression' => '900'],
                ['name' => 'valid_teams', 'order' => null, 'expression' => "FILTER(results, '!item.isDisqualified()')"],
                ['name' => 'minlsp', 'order' => null, 'expression' => "MINIMUM(valid_teams, 'points')"],
                ['name' => 'maxlsp', 'order' => null, 'expression' => "MAXIMUM(valid_teams, 'points')"],
                ['name' => 'mult_fac', 'order' => null, 'expression' => 'spread/(maxlsp-minlsp)'],
            ],
        ], [$this->speed, $this->serc]);

        // Speed: 1000, 550, 100. SERC: 100, 1000, 550.
        $this->assertSame([['B', 1, 1550.0], ['A', 2, 1100.0], ['C', 3, 650.0]], $this->standings($sheet->getResults()));
    }

    public function test_a_disqualification_scores_nothing_for_that_event(): void
    {
        $this->speed->addEntityDisqualification($this->t['A'], 201);

        $sheet = $this->sheet([], [$this->speed, $this->serc]);

        $this->assertSame([['B', 1, 1600.0], ['C', 2, 1400.0], ['A', 3, 500.0]], $this->standings($sheet->getResults()));
    }

    public function test_a_sheet_can_ignore_disqualifications(): void
    {
        $this->speed->addEntityDisqualification($this->t['A'], 201);

        $sheet = $this->sheet(['ignore_dq' => true], [$this->speed, $this->serc]);

        $this->assertSame([['B', 1, 1600.0], ['A', 2, 1500.0], ['C', 3, 1400.0]], $this->standings($sheet->getResults()));
    }

    public function test_tie_breaks_separate_equal_totals_by_place_in_an_event(): void
    {
        // A and C tie on 2000; C placed better in the SERC
        $sheet = $this->sheet(['break_ties' => [['type' => 'serc', 'id' => $this->serc->id]]], [$this->speed, [$this->serc, 2]]);

        $this->assertSame([['B', 1, 2300.0], ['C', 2, 2000.0], ['A', 3, 2000.0]], $this->standings($sheet->getResults()));
    }

    public function test_an_entity_missing_from_an_event_scores_only_what_it_has(): void
    {
        $d = $this->team('D');
        $this->time($this->speed, $d, 950);

        $sheet = $this->sheet([], [$this->speed, $this->serc]);

        $this->assertSame([['B', 1, 1600.0], ['A', 2, 1500.0], ['C', 3, 1400.0], ['D', 4, 950.0]], $this->standings($sheet->getResults()));
    }

    public function test_a_league_sheet_scores_only_that_leagues_entities_among_themselves(): void
    {
        $league = $this->league('A League');
        $this->t['A']->leagues()->attach($league);
        $this->t['C']->leagues()->attach($league);

        $sheet = $this->sheet(['league' => $league->id, 'equation' => 'item.position', 'rank_higher' => false], [$this->speed, $this->serc]);

        // Without B: A is 1st and 2nd, C is 2nd and 1st
        $this->assertSame([['A', 1, 3.0], ['C', 1, 3.0]], $this->standings($sheet->getResults()));
    }

    public function test_a_sheet_grouped_on_club_adds_a_clubs_teams_together(): void
    {
        $swansea = $this->club('Swansea');
        $d = $this->team('D', $swansea);
        $this->time($this->speed, $d, 4000);

        $sheet = $this->sheet(['group_on' => 'club'], [$this->speed, $this->serc]);

        // Each club's row is under the first of its teams the sheet came across
        $this->assertSame([['A', 1, 4500.0], ['D', 2, 4000.0]], $this->standings($sheet->getResults()));
    }

    public function test_results_are_remembered_until_a_result_is_edited(): void
    {
        $sheet = $this->sheet([], [$this->speed, $this->serc]);
        $this->assertSame('B', $sheet->getResults()->first()->entity->team);

        $time = $this->speed->results()->forEntity($this->t['C'])->first();
        $time->result = 5000;
        $time->save();

        $this->assertSame([['C', 1, 5600.0], ['B', 2, 1600.0], ['A', 3, 1500.0]], $this->standings($sheet->getResults()));
    }

    public function test_a_penalty_or_dq_refreshes_the_remembered_results(): void
    {
        $sheet = $this->sheet([], [$this->speed, $this->serc]);
        $this->assertSame('B', $sheet->getResults()->first()->entity->team);

        $this->speed->addEntityDisqualification($this->t['B'], 201);

        $this->assertSame([['A', 1, 1500.0], ['C', 2, 1400.0], ['B', 3, 700.0]], $this->standings($sheet->getResults()));
    }
}
