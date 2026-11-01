<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\SERC;
use Illuminate\Support\Facades\DB;

/**
 * The SERC order's Estimated starts (see GLOSSARY.md), for the live pages.
 *
 * Each Tank is its own queue. Its pace is the median of its last few gaps between Teams' first marks (made
 * up with the overall pace while there are only a few), so one long gap (lunch, a re-run) doesn't move it,
 * and it follows the session as it speeds up or slows down.
 * The Tank's next Team starts a reset after the last Team's first mark, and the ones after it a pace apart.
 * An estimate that has passed is "Due now", and the Teams after it count on from now, rather than every
 * estimate creeping forward through a break.
 *
 * Times are unix seconds.
 */
class SercOrderEstimate
{
    /** How many of a Tank's latest gaps its pace is the median of */
    const RECENT_GAPS = 8;

    /** How many gaps a pace needs before it's only its own (see pace()) */
    const MIN_GAPS = 5;

    /** The pace before any Tank has two Teams started */
    const DEFAULT_PACE = 360;

    /** How many Teams behind one that hasn't started must have started for it to count as skipped */
    const SKIPPED_AFTER = 2;

    /** From a Team's first mark to the next Team starting, as the old live page assumed */
    const RESET = 120;

    /**
     * @param  array<int, int[]>  $tanks  each Tank's entity ids, in Draw order
     * @param  array<int, int>  $firstMarks  when each started entity got its first mark in the dry SERC
     * @param  array<int, int>  $finished  how many SERCs each entity has marks in
     */
    public function __construct(
        private array $tanks,
        private array $firstMarks,
        private array $finished,
        private ?int $sercStart,
        private int $now,
    ) {}

    public static function for(Competition $comp, ?int $now = null): self
    {
        $draws = $comp->getDraws()[0]['draws'] ?? [];

        $tanks = collect($draws)
            ->map(fn ($tank) => collect($tank)->filter(fn ($draw) => $draw->entity)->map(fn ($draw) => $draw->entity->id)->values()->all())
            ->filter()
            ->values()
            ->all();

        // How many SERCs each entity has marks in, and when it got its first mark in the dry SERC. Only the dry
        // one: the SERC order is its running order, and a wet SERC run in another order would make Teams look
        // started (and those ahead of them skipped) and its gaps would muddle the pace
        $finished = DB::select('SELECT sr.entity_id, COUNT(DISTINCT smp.serc) AS finished FROM serc_results sr INNER JOIN serc_marking_points smp ON smp.id=sr.marking_point INNER JOIN sercs s ON s.id=smp.serc WHERE s.competition=? GROUP BY sr.entity_id', [$comp->id]);

        $dry = self::drySerc($comp);
        $firstMarks = $dry ? DB::select('SELECT sr.entity_id, UNIX_TIMESTAMP(MIN(sr.created_at)) AS first_mark FROM serc_results sr INNER JOIN serc_marking_points smp ON smp.id=sr.marking_point WHERE smp.serc=? GROUP BY sr.entity_id', [$dry->id]) : [];

        return new self(
            $tanks,
            collect($firstMarks)->mapWithKeys(fn ($row) => [$row->entity_id => (int) $row->first_mark])->all(),
            collect($finished)->mapWithKeys(fn ($row) => [$row->entity_id => (int) $row->finished])->all(),
            $comp->serc_start_time?->timestamp,
            $now ?? now()->timestamp,
        );
    }

    /**
     * The SERC the SERC order runs: one named Dry, else the first of type DRY (the default type), else the first
     */
    public static function drySerc(Competition $comp): ?SERC
    {
        $sercs = $comp->getSERCs()->orderBy('id')->get();

        return $sercs->first(fn (SERC $serc) => stripos($serc->name, 'dry') !== false)
            ?? $sercs->firstWhere('type', 'DRY')
            ?? $sercs->first();
    }

    /**
     * Every Team in the SERC order: how many SERCs it has finished, whether it's next in its Tank, and its
     * Estimated start (null once started, or when it was skipped).
     *
     * @return array<int, array{finished: int, next: bool, start: int|null}>
     */
    public function teams(): array
    {
        $teams = [];

        foreach ($this->tanks as $tank) {
            $time = $this->anchor($tank);
            $pace = $this->pace($tank);
            $next = true;

            foreach ($tank as $i => $id) {
                $start = null;

                if (!$this->started($id) && !$this->skipped($tank, $i)) {
                    // Passed: due now, and the rest follow on from now
                    $start = max($time, $this->now);
                    $time = $start + $pace;
                }

                $teams[$id] = [
                    'finished' => $this->finished[$id] ?? 0,
                    'next' => $start !== null && $next,
                    'start' => $start,
                ];

                if ($start !== null) $next = false;
            }
        }

        return $teams;
    }

    /**
     * Whether a Team that hasn't started was skipped (late, withdrawn): SKIPPED_AFTER Teams behind it in its
     * Tank have started. Not just one, so a single mark entered for the wrong Team further down doesn't blank
     * the estimates of everyone ahead of it.
     */
    private function skipped(array $tank, int $index): bool
    {
        return count(array_filter(array_slice($tank, $index + 1), fn ($id) => $this->started($id))) >= self::SKIPPED_AFTER;
    }

    /**
     * A Tank's seconds between Teams: the median of its last RECENT_GAPS gaps. While it has fewer than
     * MIN_GAPS, copies of every Tank's pace (or DEFAULT_PACE) make up the number, so one long gap early on
     * (lunch after two Teams) can't drag the median, and the real gaps take over as they come in.
     */
    public function pace(array $tank): int
    {
        return $this->paddedMedian($this->recent($this->gaps($tank)), $this->overallPace());
    }

    /** Every Tank's gaps together, made up with DEFAULT_PACE */
    private function overallPace(): int
    {
        return $this->paddedMedian($this->recent(array_merge([], ...array_map(fn ($tank) => $this->gaps($tank), $this->tanks))), self::DEFAULT_PACE);
    }

    /** @return int[] the latest RECENT_GAPS gaps' lengths */
    private function recent(array $gaps): array
    {
        usort($gaps, fn ($a, $b) => $a[0] <=> $b[0]);

        return array_column(array_slice($gaps, -self::RECENT_GAPS), 1);
    }

    private function paddedMedian(array $values, int $fill): int
    {
        return $this->median(array_pad($values, max(count($values), self::MIN_GAPS), $fill));
    }

    /** When a Tank's next Team starts: a reset after its last first mark, or the SERC start time */
    private function anchor(array $tank): int
    {
        $marks = $this->marks($tank);

        return $marks ? max($marks) + min(self::RESET, $this->pace($tank)) : ($this->sercStart ?? $this->now);
    }

    /**
     * The gaps between a Tank's consecutive first marks, as [when it ended, its length]. In time order, not
     * Draw order, so a Team that swapped places can't make a negative gap.
     *
     * @return array<int, array{int, int}>
     */
    private function gaps(array $tank): array
    {
        $marks = $this->marks($tank);
        sort($marks);

        $gaps = [];
        for ($i = 1; $i < count($marks); $i++) {
            if ($marks[$i] > $marks[$i - 1]) $gaps[] = [$marks[$i], $marks[$i] - $marks[$i - 1]];
        }

        return $gaps;
    }

    /** @return int[] the first marks of a Tank's started Teams */
    private function marks(array $tank): array
    {
        return array_values(array_filter(array_map(fn ($id) => $this->firstMarks[$id] ?? null, $tank)));
    }

    private function started(int $id): bool
    {
        return isset($this->firstMarks[$id]);
    }

    private function median(array $values): int
    {
        sort($values);
        $mid = intdiv(count($values), 2);

        return (int) round(count($values) % 2 ? $values[$mid] : ($values[$mid - 1] + $values[$mid]) / 2);
    }
}
