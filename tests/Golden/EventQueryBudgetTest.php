<?php

namespace Tests\Golden;

use App\Models\AbstractClasses\Event;
use App\Models\Competition;
use App\Scoring\Golden\CompetitionFixture;
use App\Scoring\Golden\GoldenJson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Scoring an Event reads the database a fixed handful of times, however many Entries it has: no
 * query per Entry, and none per League. Nationals 2025 is the largest fixture (133 competitors in
 * each speed event, 19 Leagues); Loughborough 2025-26 is a BULSCA league competition, with teams
 * and seed times. See docs/scoring-engine.md, "Efficiency".
 */
class EventQueryBudgetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A speed event entered by teams takes 10: the formula, penalties, disqualifications, results,
     * the teams, their Clubs, their Leagues, the competition, its speed events and seeds. One
     * entered by competitors takes two more, for their teams and their own Leagues. A SERC takes 7,
     * as it reads no seeds.
     *
     * Each fixture, with the most queries any of its Events may take and the fewest Entries each
     * should have.
     */
    private const FIXTURES = [
        'nationals-2025-50' => ['budget' => 12, 'entries' => 40, 'sheets' => 99999],
        'loughborough-2025-26-69' => ['budget' => 10, 'entries' => 20, 'sheets' => 99999],
    ];

    /** @return array<string, array{string}> */
    public static function fixtures(): array
    {
        return array_map(fn(string $fixture) => [$fixture], array_combine(array_keys(self::FIXTURES), array_keys(self::FIXTURES)));
    }

    private function load(string $fixture): Competition
    {
        return CompetitionFixture::load(GoldenJson::read(__DIR__ . "/fixtures/{$fixture}/input.json"));
    }

    private function queriesToScore(Event $event, mixed $league = null): int
    {
        // A model as a page would have it: nothing loaded yet
        $event = $event->fresh();

        DB::flushQueryLog();
        DB::enableQueryLog();
        $event->getRankedResults($league);
        DB::disableQueryLog();

        return count(DB::getQueryLog());
    }

    #[DataProvider('fixtures')]
    public function test_scoring_an_event_stays_within_its_query_budget(string $fixture): void
    {
        $comp = $this->load($fixture);

        $events = [...$comp->getSpeedEvents()->orderBy('id')->get(), ...$comp->getSERCs()->orderBy('id')->get()];
        $this->assertNotEmpty($events);

        foreach ($events as $event) {
            $entries = count($event->getRankedResults());
            $queries = $this->queriesToScore($event);

            $this->assertGreaterThan(self::FIXTURES[$fixture]['entries'], $entries, "{$event->getName()} should be a large Event");
            $this->assertLessThanOrEqual(self::FIXTURES[$fixture]['budget'], $queries, "{$event->getName()}: {$queries} queries for {$entries} entries");
        }
    }

    /**
     * Every Results sheet and Master sheet of a competition, scored from nothing in one request
     */
    private function queriesToScoreEverySheet(Competition $comp): int
    {
        $sheets = $comp->getResultSchemas()->orderBy('id')->get();
        $masters = $comp->getMasterSchemas()->orderBy('id')->get();

        DB::flushQueryLog();
        DB::enableQueryLog();
        foreach ($sheets as $sheet) {
            $sheet->getResults(true);
        }
        foreach ($masters as $master) {
            $master->getResults();
        }
        DB::disableQueryLog();
        fwrite(STDERR, "\n{$comp->name}: " . count(DB::getQueryLog()) . " queries for {$sheets->count()} sheets, {$masters->count()} masters");

        return count(DB::getQueryLog());
    }

    #[DataProvider('fixtures')]
    public function test_scoring_every_sheet_reads_each_event_once(string $fixture): void
    {
        $comp = $this->load($fixture);

        $this->assertLessThanOrEqual(self::FIXTURES[$fixture]['sheets'], $this->queriesToScoreEverySheet($comp));
    }

    #[DataProvider('fixtures')]
    public function test_a_league_view_takes_no_more_queries_than_the_whole_event(string $fixture): void
    {
        $comp = $this->load($fixture);

        $event = $comp->getSpeedEvents()->orderBy('id')->first();
        $whole = $this->queriesToScore($event);
        $this->assertLessThanOrEqual(self::FIXTURES[$fixture]['budget'], $whole);

        $leagues = $comp->getLeagues()->orderBy('id')->get();
        $this->assertNotEmpty($leagues);

        foreach ($leagues as $league) {
            $this->assertLessThanOrEqual($whole, $this->queriesToScore($event, $league), "League {$league->name}");
        }
    }
}
