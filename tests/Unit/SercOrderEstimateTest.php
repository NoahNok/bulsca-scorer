<?php

namespace Tests\Unit;

use App\Services\SercOrderEstimate;
use PHPUnit\Framework\TestCase;

/**
 * Estimated starts in the SERC order: each Tank its own queue at its own pace, and "Due now" rather than
 * estimates creeping forward through a gap.
 */
class SercOrderEstimateTest extends TestCase
{
    const NOON = 1_800_000_000;

    private function estimate(array $tanks, array $firstMarks = [], ?int $sercStart = null, int $now = self::NOON, array $finished = []): SercOrderEstimate
    {
        return new SercOrderEstimate($tanks, $firstMarks, $finished ?: array_map(fn () => 1, $firstMarks), $sercStart, $now);
    }

    public function test_before_anyone_starts_teams_are_spaced_from_the_serc_start_time(): void
    {
        $teams = $this->estimate([[1, 2, 3]], sercStart: self::NOON + 600)->teams();

        $this->assertSame(self::NOON + 600, $teams[1]['start']);
        $this->assertSame(self::NOON + 600 + SercOrderEstimate::DEFAULT_PACE, $teams[2]['start']);
        $this->assertSame(self::NOON + 600 + 2 * SercOrderEstimate::DEFAULT_PACE, $teams[3]['start']);
        $this->assertTrue($teams[1]['next']);
        $this->assertFalse($teams[2]['next']);
    }

    public function test_without_a_serc_start_time_the_first_team_starts_now(): void
    {
        $teams = $this->estimate([[1, 2]])->teams();

        $this->assertSame(self::NOON, $teams[1]['start']);
    }

    public function test_two_tanks_run_side_by_side_not_one_after_the_other(): void
    {
        $teams = $this->estimate([[1, 2], [3, 4]], sercStart: self::NOON + 60)->teams();

        $this->assertSame($teams[1]['start'], $teams[3]['start']);
        $this->assertSame($teams[2]['start'], $teams[4]['start']);
        $this->assertTrue($teams[3]['next']);
    }

    public function test_a_tank_runs_at_its_own_pace(): void
    {
        $t = self::NOON - 3600;
        $estimate = $this->estimate(
            [[1, 2, 3, 4], [5, 6, 7, 8]],
            [1 => $t, 2 => $t + 300, 3 => $t + 600, 5 => $t, 6 => $t + 480, 7 => $t + 960],
        );

        $this->assertSame(300, $estimate->pace([1, 2, 3, 4]));
        $this->assertSame(480, $estimate->pace([5, 6, 7, 8]));
    }

    public function test_one_long_gap_doesnt_change_the_pace(): void
    {
        $t = self::NOON - 7200;
        // 300s apart, then a 45 minute lunch, then 300s again
        $marks = [1 => $t, 2 => $t + 300, 3 => $t + 600, 4 => $t + 3300, 5 => $t + 3600];

        $this->assertSame(300, $this->estimate([[1, 2, 3, 4, 5, 6]], $marks)->pace([1, 2, 3, 4, 5, 6]));
    }

    public function test_the_pace_follows_the_latest_gaps(): void
    {
        $t = self::NOON - 20000;
        $marks = [];
        // Ten Teams 600s apart, then nine 240s apart
        foreach (range(1, 10) as $i) $marks[$i] = $t + $i * 600;
        foreach (range(11, 19) as $i) $marks[$i] = $marks[10] + ($i - 10) * 240;

        $this->assertSame(240, $this->estimate([range(1, 20)], $marks)->pace(range(1, 20)));
    }

    public function test_a_tank_with_too_few_teams_started_borrows_the_other_tanks_pace(): void
    {
        $t = self::NOON - 3600;
        $estimate = $this->estimate([[1, 2, 3], [4, 5]], [1 => $t, 2 => $t + 420, 3 => $t + 840, 4 => $t]);

        $this->assertSame(420, $estimate->pace([4, 5]));
    }

    public function test_the_next_team_starts_a_reset_after_the_last_first_mark(): void
    {
        $t = self::NOON - 60;
        $teams = $this->estimate([[1, 2, 3, 4]], [1 => $t - 300, 2 => $t])->teams();

        $this->assertNull($teams[1]['start']);
        $this->assertNull($teams[2]['start']);
        $this->assertSame($t + SercOrderEstimate::RESET, $teams[3]['start']);
        $this->assertSame($t + SercOrderEstimate::RESET + 300, $teams[4]['start']);
    }

    public function test_a_passed_estimate_is_due_now_and_the_rest_count_on_from_now(): void
    {
        // The last Team started an hour ago: lunch
        $t = self::NOON - 3600;
        $teams = $this->estimate([[1, 2, 3, 4]], [1 => $t - 300, 2 => $t])->teams();

        $this->assertSame(self::NOON, $teams[3]['start']);
        $this->assertTrue($teams[3]['next']);
        $this->assertSame(self::NOON + 300, $teams[4]['start']);
    }

    public function test_a_skipped_team_gets_no_estimate_and_doesnt_hold_up_the_rest(): void
    {
        $t = self::NOON - 100;
        // Team 2 hasn't turned up, so Team 3 went
        $teams = $this->estimate([[1, 2, 3, 4]], [1 => $t - 300, 3 => $t])->teams();

        $this->assertNull($teams[2]['start']);
        $this->assertFalse($teams[2]['next']);
        $this->assertTrue($teams[4]['next']);
        $this->assertSame($t + SercOrderEstimate::RESET, $teams[4]['start']);
    }

    public function test_each_team_says_how_many_sercs_it_has_finished(): void
    {
        $teams = $this->estimate([[1, 2]], [1 => self::NOON - 600], finished: [1 => 2])->teams();

        $this->assertSame(2, $teams[1]['finished']);
        $this->assertSame(0, $teams[2]['finished']);
    }
}
