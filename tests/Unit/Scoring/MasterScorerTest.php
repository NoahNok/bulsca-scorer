<?php

namespace Tests\Unit\Scoring;

use App\Scoring\Engine\MasterScorer;
use App\Scoring\Engine\MasterStanding;
use PHPUnit\Framework\TestCase;

/**
 * The Master sheet scorer on inputs built by hand, with no database and no framework.
 * `MasterSheetScoringTest` checks the same rules through the models.
 *
 * Clubs are 1 (Cardiff), 2 (Swansea) and 3 (Bangor). In sheet 10 Cardiff is 1st, Swansea 2nd and
 * Bangor 3rd. In sheet 20 Swansea is 1st and Cardiff 2nd; Bangor has nobody.
 */
class MasterScorerTest extends TestCase
{
    /**
     * A sheet's rows from `[club id, place]` pairs, best placed first, each totalling 100 less
     * than the one before
     */
    private function sheet(array ...$rows): array
    {
        return array_map(
            fn(array $row, int $index) => [$row[0], (object) ['position' => $row[1], 'totalPoints' => 1000.0 - 100 * $index]],
            $rows,
            array_keys($rows),
        );
    }

    private function sheets(): array
    {
        return [
            10 => $this->sheet([1, 1], [2, 2], [3, 3]),
            20 => $this->sheet([2, 1], [1, 2]),
        ];
    }

    /**
     * `[club id, place, total, what it took from each sheet]` for each row
     *
     * @param MasterStanding[] $standings
     */
    private function rows(array $standings): array
    {
        return array_map(fn(MasterStanding $row) => [$row->clubId, $row->position, $row->total, $row->sheetResults], $standings);
    }

    public function test_each_clubs_places_are_added_up_and_the_fewest_wins(): void
    {
        $standings = MasterScorer::score([1, 2, 3], $this->sheets(), 10);

        // Bangor isn't in sheet 20, so takes the default there
        $this->assertSame([
            [1, 1, 3.0, [10 => 1, 20 => 2]],
            [2, 1, 3.0, [10 => 2, 20 => 1]],
            [3, 3, 13.0, [10 => 3, 20 => 10]],
        ], $this->rows($standings));
    }

    public function test_clubs_that_tie_are_listed_in_the_order_they_were_given(): void
    {
        $standings = MasterScorer::score([3, 2, 1], $this->sheets(), 10);

        $this->assertSame([2, 1, 3], array_map(fn(MasterStanding $row) => $row->clubId, $standings));
    }

    public function test_a_club_counts_its_best_placed_row_in_each_sheet(): void
    {
        $sheets = [
            10 => $this->sheet([1, 1], [2, 2], [3, 3]),
            // Bangor's first row wins the sheet; its second is last
            20 => $this->sheet([3, 1], [2, 2], [1, 3], [3, 4]),
        ];

        $this->assertSame([
            [1, 1, 4.0, [10 => 1, 20 => 3]],
            [2, 1, 4.0, [10 => 2, 20 => 2]],
            [3, 1, 4.0, [10 => 3, 20 => 1]],
        ], $this->rows(MasterScorer::score([1, 2, 3], $sheets, 10)));
    }

    public function test_only_the_clubs_asked_for_are_ranked(): void
    {
        $standings = MasterScorer::score([1, 2], $this->sheets(), 10);

        $this->assertSame([[1, 1, 3.0, [10 => 1, 20 => 2]], [2, 1, 3.0, [10 => 2, 20 => 1]]], $this->rows($standings));
    }

    public function test_a_row_with_no_club_counts_for_none(): void
    {
        $sheets = [10 => $this->sheet([null, 1], [1, 2], [2, 3])];

        $this->assertSame([[1, 1, 2.0, [10 => 2]], [2, 2, 3.0, [10 => 3]]], $this->rows(MasterScorer::score([1, 2], $sheets, 10)));
    }

    public function test_with_no_sheets_every_club_is_first_on_nothing(): void
    {
        $this->assertSame([[1, 1, 0.0, []], [2, 1, 0.0, []]], $this->rows(MasterScorer::score([1, 2], [], 10)));
    }

    /** Q10 */
    public function test_summing_anything_but_places_reads_that_property_of_a_sheets_row_and_the_most_wins(): void
    {
        $standings = MasterScorer::score([1, 2, 3], $this->sheets(), 0, 'totalPoints');

        // Sheet 10 gives 1000, 900, 800 down its rows and sheet 20 gives 1000, 900
        $this->assertSame([
            [1, 1, 1900.0, [10 => 1000.0, 20 => 900.0]],
            [2, 1, 1900.0, [10 => 900.0, 20 => 1000.0]],
            [3, 3, 800.0, [10 => 800.0, 20 => 0]],
        ], $this->rows($standings));
    }
}
