<?php

namespace App\Scoring\Engine;

/**
 * Scores a Master sheet: each Club takes its first (best placed) row in every Results sheet, or a
 * default where it has none, and the values are added up. Summing places, the fewest wins.
 *
 * No database, cache or models. Q10: a sheet's `weight` is not read, and summing anything but
 * `position` reads whatever property of that name a sheet's row has.
 */
final class MasterScorer
{
    /**
     * @param int[] $clubIds The Clubs to rank, in the order exact ties are listed
     * @param array<int, array<array{?int, object}>> $sheets By sheet id: each of the sheet's rows, best
     *                                                       placed first, as `[its Club's id, the row]`.
     *                                                       A row with no Club counts for none.
     * @param mixed $default What a Club takes from a sheet it isn't in
     * @param string $sumOver The property of a sheet's row that is added up
     * @return MasterStanding[] Best placed first
     */
    public static function score(array $clubIds, array $sheets, mixed $default, string $sumOver = 'position'): array
    {
        $standings = [];

        foreach ($clubIds as $clubId) {
            $total = 0;
            $sheetResults = [];

            foreach ($sheets as $sheetId => $rows) {
                $value = $default;

                foreach ($rows as [$rowClubId, $row]) {
                    if ($rowClubId !== null && $rowClubId === $clubId) {
                        $value = $row->{$sumOver};
                        break;
                    }
                }

                $sheetResults[$sheetId] = $value;
                $total += $value;
            }

            $standings[] = new MasterStanding($clubId, $sheetResults, $total);
        }

        // Fewest places wins, most points wins
        $sorted = Ranking::sort($standings, fn(MasterStanding $standing) => $standing->total, descending: $sumOver != 'position');

        return Ranking::place($sorted, fn(MasterStanding $standing) => $standing->total);
    }
}
