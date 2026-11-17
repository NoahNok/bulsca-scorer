<?php

namespace App\Scoring\Engine;

/**
 * Putting results in order and giving them places, the same way for an Event, a Results sheet and
 * a Master sheet.
 */
final class Ranking
{
    /**
     * The rows in the order of what `$key` gives for each, lowest first: a number, or a list
     * compared item by item. Rows with the same key stay in the order they were given.
     *
     * @template T
     * @param T[] $rows
     * @param callable(T): mixed $key
     * @return T[]
     */
    public static function sort(array $rows, callable $key, bool $descending = false): array
    {
        return collect($rows)->sortBy($key, SORT_REGULAR, $descending)->values()->all();
    }

    /**
     * Writes each row's place to its `position`, in the order given. Rows next to each other with
     * the same score share the better place and the next row takes the place after them all
     * (1, 2, 2, 4). Without `$share` every row takes its own place (1, 2, 3, 4).
     *
     * @template T of object
     * @param T[] $rows Already in order
     * @param callable(T): mixed $score Rows share a place when theirs are identical (`===`)
     * @return T[]
     */
    public static function place(array $rows, callable $score, bool $share = true): array
    {
        $previousScore = null;
        $previousRank = 0;
        $position = 1;

        foreach ($rows as $row) {
            $currentScore = $score($row);

            if ($share && $currentScore === $previousScore) {
                $rank = $previousRank;
            } else {
                $rank = $position;
                $previousScore = $currentScore;
                $previousRank = $rank;
            }

            $row->position = $rank;
            $position++;
        }

        return $rows;
    }
}
