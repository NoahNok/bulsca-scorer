<?php

namespace Tests\Feature\Scoring;

use App\Scoring\ScoringRun;
use Illuminate\Support\Facades\DB;

/**
 * Within a request an Event is loaded and scored once, however many Results sheets include it, and
 * anything saved in between is still seen.
 */
class ScoringRunTest extends ScoringTestCase
{
    /**
     * How many of the queries `$scoring` runs read an Event's times
     */
    private function timesRead(callable $scoring): int
    {
        DB::flushQueryLog();
        DB::enableQueryLog();
        $scoring();
        DB::disableQueryLog();

        return count(array_filter(DB::getQueryLog(), fn(array $query) => str_contains($query['query'], '`speed_results`')));
    }

    public function test_sheets_that_include_the_same_event_share_one_loading_of_it(): void
    {
        $a = $this->league('A League');
        $t = ['A' => $this->team('A', league: $a), 'B' => $this->team('B'), 'C' => $this->team('C', league: $a)];
        $event = $this->speedEvent(self::RAW, [[$t['A'], 800], [$t['B'], 1000], [$t['C'], 900]]);

        $overall = $this->sheet([], [$event]);
        $again = $this->sheet(['equation' => 'item.position', 'rank_higher' => false], [$event]);
        $league = $this->sheet(['league' => $a->id, 'equation' => 'item.position', 'rank_higher' => false], [$event]);

        $this->assertSame(1, $this->timesRead(fn() => $overall->getResults()));
        $this->assertSame(0, $this->timesRead(fn() => $again->getResults()));
        $this->assertSame(0, $this->timesRead(fn() => $league->getResults()));

        $this->assertSame([['B', 1, 1000.0], ['C', 2, 900.0], ['A', 3, 800.0]], $this->standings($overall->getResults()));
        $this->assertSame([['B', 1, 1.0], ['C', 2, 2.0], ['A', 3, 3.0]], $this->standings($again->getResults()));
        // The League's two are placed against each other, not taken from everyone's places
        $this->assertSame([['C', 1, 1.0], ['A', 2, 2.0]], $this->standings($league->getResults()));
    }

    public function test_a_sheets_formula_does_not_change_what_another_sheet_is_given(): void
    {
        $t = $this->teams('A', 'B');
        $event = $this->speedEvent(self::RAW, [[$t['A'], 800], [$t['B'], 1000]]);

        $doubled = $this->sheet(['equation' => 'item.points * 2'], [$event]);
        $plain = $this->sheet([], [$event]);

        $this->assertSame([['B', 1, 2000.0], ['A', 2, 1600.0]], $this->standings($doubled->getResults()));
        $this->assertSame([['B', 1, 1000.0], ['A', 2, 800.0]], $this->standings($plain->getResults()));
        $this->assertSame([1000.0, 1000.0], $plain->getResults()->first()->events->map(fn($e) => $e->points)->push($plain->getResults()->first()->events[0]->adjustedPoints)->all());
    }

    public function test_something_saved_in_between_is_seen_by_the_next_sheet_scored(): void
    {
        $t = $this->teams('A', 'B');
        $event = $this->speedEvent(self::RAW, [[$t['A'], 800], [$t['B'], 1000]]);

        $first = $this->sheet([], [$event]);
        $second = $this->sheet([], [$event]);
        $this->assertSame([['B', 1, 1000.0], ['A', 2, 800.0]], $this->standings($first->getResults()));

        $time = $event->results()->forEntity($t['A'])->first();
        $time->result = 5000;
        $time->save();

        // The second sheet has never been scored, so this is the Event read again, not a cache cleared
        $this->assertSame([['A', 1, 5000.0], ['B', 2, 1000.0]], $this->standings($second->getResults()));
    }

    public function test_a_change_made_without_a_model_is_not_seen_until_the_run_is_forgotten(): void
    {
        $t = $this->teams('A', 'B');
        $event = $this->speedEvent(self::RAW, [[$t['A'], 800], [$t['B'], 1000]]);

        $first = $this->sheet([], [$event]);
        $second = $this->sheet([], [$event]);
        $third = $this->sheet([], [$event]);
        $first->getResults();

        DB::table('speed_results')->where('event', $event->id)->where('entity_id', $t['A']->id)->update(['result' => 5000]);

        $this->assertSame([['B', 1, 1000.0], ['A', 2, 800.0]], $this->standings($second->getResults()));

        app(ScoringRun::class)->forget();

        $this->assertSame([['A', 1, 5000.0], ['B', 2, 1000.0]], $this->standings($third->getResults()));
    }

    public function test_an_events_own_results_are_always_scored_afresh(): void
    {
        $t = $this->teams('A', 'B');
        $event = $this->speedEvent(self::RAW, [[$t['A'], 800], [$t['B'], 1000]]);
        $this->sheet([], [$event])->getResults();

        DB::table('speed_results')->where('event', $event->id)->where('entity_id', $t['A']->id)->update(['result' => 5000]);

        $this->assertSame([['A', 1, 5000.0], ['B', 2, 1000.0]], $this->placings($event->getRankedResults()));
    }
}
