<?php

namespace Tests\Feature\Scoring;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\Event\ScoringSchema;
use App\Models\ResultSchema;
use App\Models\ResultSchemaEvent;
use App\Models\SERC;
use App\Models\SERCMarkingPoint;
use App\Models\SERCResult;
use App\Scoring\ResultsVersion;
use App\Services\CompetitionEntries;
use Illuminate\Support\Facades\DB;

/**
 * A sheet's results are remembered against the competition's results version, and every kind of
 * write that can change a result moves the version on: what a sheet shows is never out of date.
 *
 * Teams A, B and C score 1000, 900 and 800 in the speed event and 500, 700 and 600 in the SERC, so
 * the sheet over both starts as B 1600, A 1500, C 1400.
 */
class ResultsVersionTest extends ScoringTestCase
{
    private const START = [['B', 1, 1600.0], ['A', 2, 1500.0], ['C', 3, 1400.0]];

    /** @var CompetitionTeam[] */
    private array $t;

    private CompetitionSpeedEvent $speed;

    private SERC $serc;

    private ResultSchema $sheet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->t = $this->teams('A', 'B', 'C');
        $this->speed = $this->speedEvent(self::RAW, [[$this->t['A'], 1000], [$this->t['B'], 900], [$this->t['C'], 800]]);
        $this->serc = $this->serc(self::RAW, [1, 1], [[$this->t['A'], [500]], [$this->t['B'], [700]], [$this->t['C'], [600]]]);
        $this->sheet = $this->sheet([], [$this->speed, $this->serc]);

        $this->assertSame(self::START, $this->standings($this->sheet->getResults()));
    }

    /**
     * The sheet as a later request would see it: nothing kept but the cache
     */
    private function standingsNow(?ResultSchema $sheet = null): array
    {
        return $this->standings(ResultSchema::find(($sheet ?? $this->sheet)->id)->getResults());
    }

    private function version(?Competition $comp = null): int
    {
        return (int) DB::table('competitions')->where('id', ($comp ?? $this->comp)->id)->value('results_version');
    }

    private function queries(callable $run): int
    {
        DB::flushQueryLog();
        DB::enableQueryLog();
        $run();
        DB::disableQueryLog();

        return count(array_filter(DB::getQueryLog(), fn(array $query) => str_contains($query['query'], '`speed_results`')));
    }

    public function test_results_are_not_worked_out_again_while_nothing_has_changed(): void
    {
        $this->assertSame(0, $this->queries(fn() => $this->assertSame(self::START, $this->standingsNow())));
    }

    public function test_a_save_that_changes_nothing_leaves_the_version_alone(): void
    {
        $before = $this->version();

        $this->speed->fresh()->save();
        $this->speed->results()->first()->save();

        $this->assertSame($before, $this->version());
    }

    public function test_the_version_only_ever_goes_up(): void
    {
        $before = $this->version();

        app(ResultsVersion::class)->bump($this->comp);
        $once = $this->version();
        app(ResultsVersion::class)->bump($this->comp);

        $this->assertGreaterThan($before, $once);
        $this->assertGreaterThan($once, $this->version());
        $this->assertSame($this->version(), app(ResultsVersion::class)->of($this->comp));
    }

    public function test_a_change_to_one_competition_leaves_anothers_version_alone(): void
    {
        $other = $this->comp->replicate();
        $other->name = 'Another';
        $other->save();
        $before = $this->version($other);

        $this->time($this->speed, $this->team('D'), 2000);

        $this->assertSame($before, $this->version($other));
    }

    public function test_a_time_entered_for_the_first_time_is_seen(): void
    {
        $this->time($this->speed, $this->team('D'), 2000);

        $this->assertSame([['D', 1, 2000.0], ['B', 2, 1600.0], ['A', 3, 1500.0], ['C', 4, 1400.0]], $this->standingsNow());
    }

    public function test_an_edited_time_is_seen(): void
    {
        $time = $this->speed->results()->forEntity($this->t['C'])->first();
        $time->result = 5000;
        $time->save();

        $this->assertSame([['C', 1, 5600.0], ['B', 2, 1600.0], ['A', 3, 1500.0]], $this->standingsNow());
    }

    public function test_a_mark_entered_for_the_first_time_is_seen(): void
    {
        // Q8, as it was: only an edit to an existing Mark, a penalty or a DQ refreshed a sheet
        $mark = new SERCResult();
        $mark->marking_point = $this->serc->getMarkingPoints()->orderByDesc('serc_marking_points.id')->value('serc_marking_points.id');
        $mark->entity()->associate($this->t['C']);
        $mark->result = 300;
        $mark->save();

        $this->assertSame([['C', 1, 1700.0], ['B', 2, 1600.0], ['A', 3, 1500.0]], $this->standingsNow());

        $mark->delete();

        $this->assertSame(self::START, $this->standingsNow());
    }

    public function test_a_marking_points_new_weight_is_seen(): void
    {
        $point = SERCMarkingPoint::where('serc', $this->serc->id)->orderBy('id')->first();
        $point->weight = 2;
        $point->save();

        $this->assertSame([['B', 1, 2300.0], ['A', 2, 2000.0], ['C', 2, 2000.0]], $this->standingsNow());
    }

    public function test_a_penalty_or_dq_given_and_taken_away_is_seen(): void
    {
        $this->speed->addEntityDisqualification($this->t['B'], 201);

        $this->assertSame([['A', 1, 1500.0], ['C', 2, 1400.0], ['B', 3, 700.0]], $this->standingsNow());

        // Taken away without a model event
        $this->speed->clearEntityDisqualifications($this->t['B']);

        $this->assertSame(self::START, $this->standingsNow());
    }

    public function test_an_edited_event_formula_is_seen(): void
    {
        $formula = ScoringSchema::find($this->speed->scoring_schema);
        $formula->schema = ['equation' => 'item.resolvedResult * 2', 'global_variables' => []];
        $formula->save();

        $this->assertSame([['A', 1, 2500.0], ['B', 1, 2500.0], ['C', 3, 2200.0]], $this->standingsNow());
    }

    public function test_an_event_given_another_formula_is_seen(): void
    {
        $this->speed->scoring_schema = $this->formula(['equation' => 'item.resolvedResult * 2', 'global_variables' => []])->id;
        $this->speed->save();

        $this->assertSame([['A', 1, 2500.0], ['B', 1, 2500.0], ['C', 3, 2200.0]], $this->standingsNow());
    }

    public function test_an_edited_sheet_and_its_events_are_seen(): void
    {
        $this->sheet->schema = [...$this->sheet->schema, 'equation' => 'item.points * 2'];
        $this->sheet->save();

        $this->assertSame([['B', 1, 3200.0], ['A', 2, 3000.0], ['C', 3, 2800.0]], $this->standingsNow());

        $row = ResultSchemaEvent::where('schema', $this->sheet->id)->where('event_id', $this->speed->id)->where('event_type', 'like', '%CompetitionSpeedEvent')->first();
        $row->weight = 0;
        $row->save();

        $this->assertSame([['B', 1, 1400.0], ['C', 2, 1200.0], ['A', 3, 1000.0]], $this->standingsNow());
    }

    public function test_an_entry_joining_or_leaving_a_league_is_seen(): void
    {
        $league = $this->league('A League');
        $ofLeague = $this->sheet(['league' => $league->id], [$this->speed, $this->serc]);
        $this->assertSame([], $this->standingsNow($ofLeague));

        $this->t['A']->leagues()->attach($league);
        $this->t['C']->leagues()->sync([$league->id]);

        $this->assertSame([['A', 1, 1500.0], ['C', 2, 1400.0]], $this->standingsNow($ofLeague));

        $this->t['A']->leagues()->detach($league);

        $this->assertSame([['C', 1, 1400.0]], $this->standingsNow($ofLeague));
    }

    public function test_a_seed_time_entered_and_removed_is_seen(): void
    {
        // The seed time, where there is one, is what counts
        $formula = ScoringSchema::find($this->speed->scoring_schema);
        $formula->schema = [...self::RAW, 'penalty_func' => 'item.seed > 0 ? item.seed : item.result', 'force_penalty' => true];
        $formula->save();
        $this->assertSame(self::START, $this->standingsNow());

        $this->comp->use_seeds = true;
        $this->comp->save();

        $entries = new CompetitionEntries($this->comp);
        $entries->saveSeeds($this->t['C'], [$this->speed->id => '0:03.000']);

        $this->assertSame([['C', 1, 3600.0], ['B', 2, 1600.0], ['A', 3, 1500.0]], $this->standingsNow());

        // Removed without a model event
        $entries->saveSeeds($this->t['C'], [$this->speed->id => '']);

        $this->assertSame(self::START, $this->standingsNow());
    }

    public function test_a_removed_entry_is_seen(): void
    {
        $this->t['B']->delete();

        $this->assertSame([['A', 1, 1500.0], ['C', 2, 1400.0]], $this->standingsNow());
    }

    public function test_a_master_sheet_follows_its_sheets(): void
    {
        $other = $this->club('Swansea');
        $this->time($this->speed, $this->team('D', $other), 100);
        $master = $this->master([], [$this->sheet]);

        $clubs = fn() => $master->fresh()->getResults()->map(fn($row) => [$row->entity->name, $row->position])->all();
        $this->assertSame([['Cardiff', 1], ['Swansea', 2]], $clubs());

        $time = $this->speed->results()->forEntity(CompetitionTeam::where('team', 'D')->first())->first();
        $time->result = 9000;
        $time->save();

        $this->assertSame([['Swansea', 1], ['Cardiff', 2]], $clubs());
    }

    public function test_a_change_made_without_a_model_is_not_seen_until_the_version_is_moved_on(): void
    {
        DB::table('speed_results')->where('event', $this->speed->id)->where('entity_id', $this->t['C']->id)->update(['result' => 5000]);

        $this->assertSame(self::START, $this->standingsNow());

        app(ResultsVersion::class)->bump($this->comp);

        $this->assertSame([['C', 1, 5600.0], ['B', 2, 1600.0], ['A', 3, 1500.0]], $this->standingsNow());
    }

    public function test_results_remembered_inside_a_transaction_that_is_rolled_back_are_not_used_after_it(): void
    {
        DB::beginTransaction();
        $time = $this->speed->results()->forEntity($this->t['C'])->first();
        $time->result = 5000;
        $time->save();
        $this->assertSame('C', $this->standingsNow()[0][0]);
        DB::rollBack();

        $this->assertSame(self::START, $this->standingsNow());

        // The next change is not given the rolled-back version, which the cache still holds
        $time = $this->speed->results()->forEntity($this->t['A'])->first();
        $time->result = 1001;
        $time->save();

        $this->assertSame([['B', 1, 1600.0], ['A', 2, 1501.0], ['C', 3, 1400.0]], $this->standingsNow());
    }
}
