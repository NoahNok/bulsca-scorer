<?php

namespace Tests\Unit\Scoring;

use App\Scoring\Engine\Ranking;
use PHPUnit\Framework\TestCase;

/**
 * Ordering and placing on their own, as Events, Results sheets and Master sheets all use them.
 */
class RankingTest extends TestCase
{
    private function rows(array $scores): array
    {
        return array_map(fn($name, $score) => (object) ['name' => $name, 'score' => $score, 'position' => null], array_keys($scores), $scores);
    }

    private function names(array $rows): array
    {
        return array_column($rows, 'name');
    }

    public function test_rows_are_sorted_lowest_first_and_equal_rows_keep_their_order(): void
    {
        $rows = $this->rows(['a' => 3, 'b' => 1, 'c' => 3, 'd' => 2]);

        $this->assertSame(['b', 'd', 'a', 'c'], $this->names(Ranking::sort($rows, fn($row) => $row->score)));
        $this->assertSame(['a', 'c', 'd', 'b'], $this->names(Ranking::sort($rows, fn($row) => $row->score, descending: true)));
    }

    public function test_a_list_key_is_compared_item_by_item(): void
    {
        $rows = $this->rows(['a' => [1, 5], 'b' => [0, 9], 'c' => [1, 2]]);

        $this->assertSame(['b', 'c', 'a'], $this->names(Ranking::sort($rows, fn($row) => $row->score)));
    }

    public function test_equal_scores_share_a_place_and_the_next_place_is_skipped(): void
    {
        $rows = Ranking::place($this->rows(['a' => 9.0, 'b' => 7.0, 'c' => 7.0, 'd' => 5.0]), fn($row) => $row->score);

        $this->assertSame([1, 2, 2, 4], array_column($rows, 'position'));
    }

    public function test_scores_must_be_identical_to_share_a_place(): void
    {
        $rows = Ranking::place($this->rows(['a' => 7.0, 'b' => 7, 'c' => '7']), fn($row) => $row->score);

        $this->assertSame([1, 2, 3], array_column($rows, 'position'));
    }

    public function test_every_row_takes_its_own_place_when_places_are_not_shared(): void
    {
        $rows = Ranking::place($this->rows(['a' => 9.0, 'b' => 7.0, 'c' => 7.0]), fn($row) => $row->score, share: false);

        $this->assertSame([1, 2, 3], array_column($rows, 'position'));
    }
}
