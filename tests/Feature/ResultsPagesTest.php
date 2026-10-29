<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\Event\ScoringSchema;
use App\Models\League;
use App\Models\SERC\MarkingPointTemplate;
use App\Models\MasterSchema;
use App\Models\ResultSchema;
use App\Models\ResultSchemaEvent;
use App\Models\SERC;
use App\Models\SERCJudge;
use App\Models\SERCMarkingPoint;
use App\Models\SERCResult;
use App\Models\SpeedEvent;
use App\Models\SpeedResult;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * A competition's Results (Svelte, in CompetitionLayout): its Results sheets and Master sheets, each
 * made, edited and deleted from its own pages, and publishing and finalising results.
 *
 * Team A wins the speed event and team B the SERC, so a sheet summing positions ties them.
 */
class ResultsPagesTest extends TestCase
{
    use RefreshDatabase;

    private User $organiser;

    private Competition $comp;

    private Club $club;

    private CompetitionSpeedEvent $speed;

    private SERC $serc;

    /** @var CompetitionTeam[] */
    private array $teams = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->organiser = User::factory()->create();

        $this->comp = new Competition();
        $this->comp->name = 'Results Champs';
        $this->comp->when = now()->addWeek();
        $this->comp->where = 'Test Pool';
        $this->comp->max_lanes = 8;
        $this->comp->anytimepin = false;
        $this->comp->seed_per_event = false;
        $this->comp->heats_per_event = false;
        $this->comp->show_competition = true;
        $this->comp->save();
        $this->comp->addAccount($this->organiser, ['owner']);

        $this->club = Club::create(['competition' => $this->comp->id, 'name' => 'Cardiff']);
        foreach (['A', 'B'] as $name) {
            $this->teams[] = CompetitionTeam::create(['competition' => $this->comp->id, 'club' => $this->club->id, 'team' => $name]);
        }

        // Lowest time wins
        $this->speed = $this->speedEvent('Swim & Tow', [62000, 65000]);

        // Highest mark wins. It shares the speed event's id, as a speed event and a SERC can
        $this->serc = new SERC();
        $this->serc->id = $this->speed->id;
        $this->serc->name = 'Dry SERC';
        $this->serc->type = 'DRY';
        $this->serc->scorable_entity = 'team';
        $this->serc->competition = $this->comp->id;
        $this->serc->scoring_schema = $this->scoringSchema(true)->id;
        $this->serc->save();

        $objective = new SERCJudge();
        $objective->name = 'Casualty 1';
        $objective->description = '';
        $objective->serc = $this->serc->id;
        $objective->save();

        $mp = new SERCMarkingPoint();
        $mp->name = 'Checks for danger';
        $mp->weight = 1;
        $mp->judge = $objective->id;
        $mp->serc = $this->serc->id;
        $mp->marking_point_template_id = MarkingPointTemplate::create(['name' => 'Default', 'settings' => []])->id;
        $mp->save();

        foreach ([4, 8] as $index => $mark) {
            $result = new SERCResult();
            $result->marking_point = $mp->id;
            $result->entity()->associate($this->teams[$index]);
            $result->result = $mark;
            $result->save();
        }
    }

    private function scoringSchema(bool $rankHigher): ScoringSchema
    {
        $schema = new ScoringSchema();
        $schema->name = 'Raw';
        $schema->schema = ['equation' => 'item.result', 'global_variables' => [], 'rank_higher' => $rankHigher];
        $schema->save();

        return $schema;
    }

    /**
     * A speed event scoring teams, with a time for each team in order
     */
    private function speedEvent(string $name, array $times): CompetitionSpeedEvent
    {
        $base = new SpeedEvent();
        $base->name = $name;
        $base->save();

        $event = new CompetitionSpeedEvent();
        $event->event = $base->id;
        $event->competition = $this->comp->id;
        $event->scorable_entity = 'team';
        $event->record = 0;
        $event->scoring_schema = $this->scoringSchema(false)->id;
        $event->save();

        foreach ($times as $index => $time) {
            $result = new SpeedResult();
            $result->event = $event->id;
            $result->entity()->associate($this->teams[$index]);
            $result->result = $time;
            $result->save();
        }

        return $event;
    }

    private function league(string $name): League
    {
        $league = new League();
        $league->name = $name;
        $league->competition = $this->comp->id;
        $league->save();

        return $league;
    }

    /**
     * A Results sheet as the old Add page made it: positions summed, lowest wins
     */
    private function sheet(string $name, array $settings = [], ?array $events = null): ResultSchema
    {
        $sheet = new ResultSchema();
        $sheet->name = $name;
        $sheet->competition = $this->comp->id;
        $sheet->schema = [
            'equation' => 'item.position',
            'global_variables' => [],
            'league' => null,
            'ignore_dq' => false,
            'group_on' => 'team',
            'rank_higher' => false,
            'break_ties' => [],
            ...$settings,
        ];
        $sheet->save();

        foreach ($events ?? [$this->speed, $this->serc] as $event) {
            $row = new ResultSchemaEvent();
            $row->schema = $sheet->id;
            $row->event_id = $event->id;
            $row->event_type = $event instanceof SERC ? '\App\Models\SERC' : '\App\Models\CompetitionSpeedEvent';
            $row->weight = 1;
            $row->save();
        }

        return $sheet;
    }

    private function url(string $path = ''): string
    {
        return $this->appUrl('/comps/' . $this->comp->id . '/results' . $path);
    }

    public function test_the_overview_lists_results_sheets_and_master_sheets(): void
    {
        $sheet = $this->sheet('Overall');
        $hidden = $this->sheet('Working');
        $hidden->viewable = false;
        $hidden->save();

        $master = new MasterSchema();
        $master->name = 'Clubs';
        $master->competition = $this->comp->id;
        $master->viewable = true;
        $master->schema = ['sum_over' => 'position', 'default_value' => 0, 'sheets' => [['id' => $sheet->id, 'weight' => 1]], 'group_on' => 'club', 'exclude' => []];
        $master->save();

        $this->actingAs($this->organiser)->get($this->url())
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Competition/Results')
                ->has('sheets', 2)
                ->where('sheets.0.name', 'Overall')
                ->where('sheets.0.league', null)
                ->where('sheets.0.viewable', true)
                ->where('sheets.1.viewable', false)
                ->has('masters', 1)
                ->where('masters.0.name', 'Clubs')
                ->where('published', false)
                ->where('provisional', true)
                ->where('canCreate', true)
                ->has('unconfirmed', 0));
    }

    public function test_a_results_sheet_shows_its_ranking_and_how_its_built(): void
    {
        $league = $this->league('Uni');
        $sheet = $this->sheet('Overall', ['league' => $league->id, 'break_ties' => [['type' => 'serc', 'id' => $this->serc->id]]]);

        $this->actingAs($this->organiser)->get($this->url('/' . $sheet->id))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Competition/ResultSheet')
                ->where('sheet.name', 'Overall')
                ->where('sheet.viewable', true)
                ->where('sheet.league', 'Uni')
                ->where('sheet.formula', 'Lowest position sum wins')
                ->where('sheet.events', [
                    ['name' => 'Swim & Tow', 'weight' => 1],
                    ['name' => 'Dry SERC', 'weight' => 1],
                ])
                ->where('sheet.breakTies', ['Dry SERC'])
                ->where('error', null)
                ->where('isAdmin', false)
                ->has('masters', 0));
    }

    /**
     * Each team's key in a sheet's results
     */
    private function keys(): array
    {
        return array_map(fn ($team) => 'team:' . $team->id, $this->teams);
    }

    private function ranking(ResultSchema $sheet): array
    {
        $ranking = [];
        $this->actingAs($this->organiser)->get($this->url('/' . $sheet->id))
            ->assertInertia(function (Assert $page) use (&$ranking) {
                $ranking = collect($page->toArray()['props']['results'])->map(fn ($row) => [$row['key'], $row['position']])->all();
            });

        return $ranking;
    }

    public function test_ties_stay_tied_without_tie_breaks(): void
    {
        [$a, $b] = $this->keys();

        $this->assertEquals([[$a, 1], [$b, 1]], $this->ranking($this->sheet('Overall')));
    }

    public function test_tie_breaks_use_the_event_picked_when_a_speed_event_and_a_serc_share_an_id(): void
    {
        $this->assertEquals($this->speed->id, $this->serc->id);
        [$a, $b] = $this->keys();

        $this->assertEquals([[$b, 1], [$a, 2]], $this->ranking($this->sheet('SERC first', ['break_ties' => [['type' => 'serc', 'id' => $this->serc->id]]])));
        $this->assertEquals([[$a, 1], [$b, 2]], $this->ranking($this->sheet('Speed first', ['break_ties' => [['type' => 'speed', 'id' => $this->speed->id]]])));
    }

    public function test_tie_breaks_rank_the_better_place_first_when_highest_wins(): void
    {
        [$a, $b] = $this->keys();

        // Highest points sum wins, with both teams on the same points
        $sheet = $this->sheet('Points', ['equation' => '10', 'rank_higher' => true, 'break_ties' => [$this->serc->id]], [$this->serc, $this->speed]);

        $this->assertEquals([[$b, 1], [$a, 2]], $this->ranking($sheet));
    }

    public function test_tie_breaks_saved_as_bare_ids_still_work(): void
    {
        [$a, $b] = $this->keys();
        $other = $this->speedEvent('Rope Throw', [70000, 60000]);

        $this->assertEquals([[$b, 1], [$a, 2]], $this->ranking($this->sheet('Old', ['break_ties' => [$other->id]], [$this->speed, $this->serc, $other])));
    }

    /**
     * What New and Edit post for a sheet of both Events, positions summed, lowest wins
     */
    private function sheetData(array $overrides = []): array
    {
        return [
            'name' => 'Overall',
            'league' => null,
            'events' => [
                ['type' => 'speed', 'id' => $this->speed->id, 'weight' => 1],
                ['type' => 'serc', 'id' => $this->serc->id, 'weight' => 2],
            ],
            'equation' => 'item.position',
            'global_variables' => [],
            'group_on' => 'team',
            'rank_higher' => false,
            'ignore_dq' => false,
            'break_ties' => [['type' => 'serc', 'id' => $this->serc->id]],
            ...$overrides,
        ];
    }

    private function unconfirmedDigitalJudge(): void
    {
        $this->comp->digitalJudgeEnabled = true;
        $this->comp->save();
        $this->serc->digitalJudgeEnabled = true;
        $this->serc->save();
    }

    public function test_new_results_sheet_offers_every_event_league_and_formula(): void
    {
        $this->league('Uni');

        $this->actingAs($this->organiser)->get($this->url('/create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Competition/ResultSheetForm')
                ->where('sheet', null)
                ->where('events', [
                    ['type' => 'speed', 'id' => $this->speed->id, 'name' => 'Swim & Tow', 'weight' => 1],
                    ['type' => 'serc', 'id' => $this->serc->id, 'name' => 'Dry SERC', 'weight' => 2],
                ])
                ->has('leagues', 1)
                ->has('builtIns', 6)
                ->where('builtIns.0', ['key' => 'points', 'label' => 'Highest points sum wins', 'equation' => 'item.points', 'rankHigher' => true])
                ->has('organisationFormulas', 0)
                ->where('published', false)
                ->has('unconfirmed', 0));
    }

    public function test_making_a_results_sheet_opens_it(): void
    {
        $response = $this->actingAs($this->organiser)->post($this->url(), $this->sheetData());

        $sheet = ResultSchema::sole();
        $response->assertRedirect($this->url('/' . $sheet->id));
        $this->assertEquals('Overall', $sheet->name);
        $this->assertEquals([['type' => 'serc', 'id' => $this->serc->id]], $sheet->schema['break_ties']);
        $this->assertFalse($sheet->schema['rank_higher']);
        $this->assertEquals([['speed', 1.0], ['serc', 2.0]], $sheet->getEvents->map(fn ($row) => [
            str_ends_with($row->event_type, 'SERC') ? 'serc' : 'speed',
            (float) $row->weight,
        ])->all());
    }

    public function test_repeating_a_results_sheet_for_every_league_makes_one_each_named_after_it(): void
    {
        $this->league('Uni');
        $this->league('Open');

        $this->actingAs($this->organiser)->post($this->url(), $this->sheetData(['name' => '', 'repeat_for_all_leagues' => true]))
            ->assertRedirect($this->url());

        $this->assertEquals(['Uni', 'Open'], ResultSchema::orderBy('id')->pluck('name')->all());
    }

    public function test_tie_breaks_must_be_events_on_the_sheet(): void
    {
        $this->actingAs($this->organiser)->post($this->url(), $this->sheetData([
            'events' => [['type' => 'speed', 'id' => $this->speed->id, 'weight' => 1]],
        ]))->assertSessionHasErrors('break_ties');

        $this->assertEquals(0, ResultSchema::count());
    }

    public function test_sheets_cant_be_made_while_a_digitally_judged_event_waits_for_confirmation(): void
    {
        $this->unconfirmedDigitalJudge();

        $this->actingAs($this->organiser)->get($this->url())
            ->assertInertia(fn (Assert $page) => $page
                ->where('canCreate', false)
                ->where('unconfirmed.0.name', 'Dry SERC'));

        $this->actingAs($this->organiser)->get($this->url('/create'))->assertRedirect($this->url());
        $this->actingAs($this->organiser)->post($this->url(), $this->sheetData())->assertSessionHasErrors('sheet');
        $this->assertEquals(0, ResultSchema::count());
    }

    private function master(string $name, array $sheets, string $sumOver = 'position'): MasterSchema
    {
        $master = new MasterSchema();
        $master->name = $name;
        $master->competition = $this->comp->id;
        $master->viewable = true;
        $master->schema = [
            'sum_over' => $sumOver,
            'default_value' => 0,
            'sheets' => array_map(fn ($sheet) => ['id' => $sheet->id, 'weight' => 1], $sheets),
            'group_on' => 'club',
            'exclude' => [],
        ];
        $master->save();

        return $master;
    }

    public function test_edit_results_sheet_starts_from_how_its_set_up(): void
    {
        $sheet = $this->sheet('Old', ['break_ties' => [$this->serc->id]], [$this->serc]);

        $this->actingAs($this->organiser)->get($this->url('/' . $sheet->id . '/edit'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Competition/ResultSheetForm')
                ->where('sheet.name', 'Old')
                ->where('sheet.events', [['type' => 'serc', 'id' => $this->serc->id]])
                ->where('sheet.break_ties', [['type' => 'serc', 'id' => $this->serc->id]])
                ->where('sheet.builtIn', 'position-low')
                ->where('events.0.weight', 1)
                ->where('events.1.weight', 1));
    }

    public function test_editing_a_results_sheet_changes_its_ranking_at_once(): void
    {
        $sheet = $this->sheet('Overall');
        [$a, $b] = $this->keys();
        $this->assertEquals([[$a, 1], [$b, 1]], $this->ranking($sheet));

        $this->actingAs($this->organiser)->post($this->url('/' . $sheet->id), $this->sheetData(['name' => 'Renamed']))
            ->assertRedirect($this->url('/' . $sheet->id));

        $sheet->refresh();
        $this->assertEquals('Renamed', $sheet->name);
        $this->assertEquals(2, $sheet->getEvents()->count());
        $this->assertEquals([[$b, 1], [$a, 2]], $this->ranking($sheet));
    }

    public function test_deleting_a_results_sheet_needs_its_name_and_takes_it_out_of_master_sheets(): void
    {
        $sheet = $this->sheet('Overall');
        $kept = $this->sheet('Other');
        $master = $this->master('Clubs', [$sheet, $kept]);

        $this->actingAs($this->organiser)->get($this->url('/' . $sheet->id))
            ->assertInertia(fn (Assert $page) => $page->where('masters', ['Clubs']));

        $this->actingAs($this->organiser)->delete($this->url('/' . $sheet->id), ['name' => 'Wrong'])->assertSessionHasErrors('name');
        $this->assertModelExists($sheet);

        $this->actingAs($this->organiser)->delete($this->url('/' . $sheet->id), ['name' => 'Overall'])->assertRedirect($this->url());
        $this->assertModelMissing($sheet);
        $this->assertDatabaseMissing('result_schema_events', ['schema' => $sheet->id]);
        $this->assertEquals([$kept->id], array_column($master->fresh()->schema['sheets'], 'id'));
    }

    public function test_a_results_sheet_is_shown_or_hidden_in_results(): void
    {
        $sheet = $this->sheet('Overall');

        $this->actingAs($this->organiser)->post($this->url('/' . $sheet->id . '/visibility'), ['viewable' => false])->assertSessionHas('success');

        $this->assertFalse((bool) $sheet->fresh()->viewable);
    }

    public function test_results_are_published_and_finalised_from_the_overview(): void
    {
        $this->actingAs($this->organiser)->post($this->url('/publish'), ['published' => true])->assertSessionHas('success');
        $this->actingAs($this->organiser)->post($this->url('/finalise'), ['provisional' => false])->assertSessionHas('success');

        $this->comp->refresh();
        $this->assertTrue((bool) $this->comp->public_results);
        $this->assertFalse((bool) $this->comp->results_provisional);
    }

    public function test_old_results_urls_go_to_the_new_pages(): void
    {
        $sheet = $this->sheet('Overall');

        $this->actingAs($this->organiser)->get($this->url('/add'))->assertRedirect($this->url('/create'))->assertStatus(301);
        $this->actingAs($this->organiser)->get($this->appUrl('/comp/results/view-schema/' . $sheet->id))->assertRedirect($this->url('/' . $sheet->id))->assertStatus(301);
    }

    public function test_results_need_results_access(): void
    {
        $sheet = $this->sheet('Overall');
        $other = User::factory()->create();
        $this->comp->addAccount($other, ['teams']);

        $this->actingAs($other)->get($this->url())->assertForbidden();
        $this->actingAs($other)->get($this->url('/' . $sheet->id))->assertForbidden();
        $this->actingAs($other)->get($this->appUrl('/comp/results/view-schema/' . $sheet->id))->assertForbidden();
        $this->actingAs($other)->post($this->url('/' . $sheet->id), $this->sheetData())->assertForbidden();
    }

    /**
     * Moves team B into a second club, so Master sheets (by club) have two to rank
     */
    private function secondClub(): Club
    {
        $club = Club::create(['competition' => $this->comp->id, 'name' => 'Swansea']);
        $this->teams[1]->club = $club->id;
        $this->teams[1]->save();

        return $club;
    }

    private function masterRanking(MasterSchema $master): array
    {
        $ranking = [];
        $this->actingAs($this->organiser)->get($this->url('/master/' . $master->id))
            ->assertOk()
            ->assertInertia(function (Assert $page) use (&$ranking) {
                $page->component('Manage/Competition/MasterSheet');
                $ranking = collect($page->toArray()['props']['results'])->map(fn ($row) => [$row['name'], $row['total'], $row['position']])->all();
            });

        return $ranking;
    }

    public function test_a_master_sheet_summing_points_ranks_highest_first(): void
    {
        $this->secondClub();
        // Times and marks summed as points: A 62004, B 65008
        $sheet = $this->sheet('Points', ['equation' => 'item.points', 'rank_higher' => true]);

        $this->assertEquals([['Swansea', 65008, 1], ['Cardiff', 62004, 2]], $this->masterRanking($this->master('By points', [$sheet], 'totalPoints')));
    }

    public function test_a_master_sheet_summing_positions_ranks_lowest_first(): void
    {
        $this->secondClub();
        $sheet = $this->sheet('Places', ['break_ties' => [['type' => 'serc', 'id' => $this->serc->id]]]);

        $this->assertEquals([['Swansea', 1, 1], ['Cardiff', 2, 2]], $this->masterRanking($this->master('By place', [$sheet])));
    }

    public function test_a_master_sheet_shows_how_its_built(): void
    {
        $swansea = $this->secondClub();
        $sheet = $this->sheet('Overall');
        $master = $this->master('Clubs', [$sheet]);
        $master->schema = [...$master->schema, 'exclude' => [$swansea->id]];
        $master->save();

        $this->actingAs($this->organiser)->get($this->url('/master/' . $master->id))
            ->assertInertia(fn (Assert $page) => $page
                ->where('master.name', 'Clubs')
                ->where('master.sumOver', 'Position')
                ->where('master.sheets', [['id' => $sheet->id, 'name' => 'Overall']])
                ->where('master.excluded', ['Swansea'])
                ->has('results', 1));
    }

    public function test_making_and_editing_a_master_sheet(): void
    {
        $swansea = $this->secondClub();
        $first = $this->sheet('First');
        $second = $this->sheet('Second');

        $this->actingAs($this->organiser)->get($this->url('/master/create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Competition/MasterSheetForm')
                ->where('master', null)
                ->has('sheets', 2)
                ->has('clubs', 2));

        $data = ['name' => 'Clubs', 'sum_over' => 'position', 'default_value' => 10, 'sheets' => [$first->id], 'exclude' => []];
        $response = $this->actingAs($this->organiser)->post($this->url('/master'), $data);
        $master = MasterSchema::sole();
        $response->assertRedirect($this->url('/master/' . $master->id));
        $this->assertEquals([['id' => $first->id, 'weight' => 1]], $master->schema['sheets']);
        $this->assertEquals('club', $master->schema['group_on']);

        $this->actingAs($this->organiser)->get($this->url('/master/' . $master->id . '/edit'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('master.name', 'Clubs')
                ->where('master.sheets', [$first->id])
                ->where('master.default_value', 10));

        $this->actingAs($this->organiser)->post($this->url('/master/' . $master->id), [...$data, 'name' => 'Renamed', 'sum_over' => 'totalPoints', 'sheets' => [$first->id, $second->id], 'exclude' => [$swansea->id]])
            ->assertRedirect($this->url('/master/' . $master->id));

        $master->refresh();
        $this->assertEquals('Renamed', $master->name);
        $this->assertEquals('totalPoints', $master->schema['sum_over']);
        $this->assertEquals([$first->id, $second->id], array_column($master->schema['sheets'], 'id'));
        $this->assertEquals([$swansea->id], $master->schema['exclude']);
    }

    public function test_a_master_sheet_only_takes_this_competitions_sheets(): void
    {
        $other = $this->comp->replicate();
        $other->save();
        $theirs = new ResultSchema();
        $theirs->name = 'Theirs';
        $theirs->competition = $other->id;
        $theirs->schema = [];
        $theirs->save();

        $this->actingAs($this->organiser)->post($this->url('/master'), ['name' => 'Clubs', 'sum_over' => 'position', 'default_value' => 0, 'sheets' => [$theirs->id], 'exclude' => []])
            ->assertSessionHasErrors('sheets.0');
    }

    public function test_deleting_and_hiding_a_master_sheet(): void
    {
        $master = $this->master('Clubs', [$this->sheet('Overall')]);

        $this->actingAs($this->organiser)->post($this->url('/master/' . $master->id . '/visibility'), ['viewable' => false])->assertSessionHas('success');
        $this->assertFalse((bool) $master->fresh()->viewable);

        $this->actingAs($this->organiser)->delete($this->url('/master/' . $master->id), ['name' => 'Wrong'])->assertSessionHasErrors('name');
        $this->actingAs($this->organiser)->delete($this->url('/master/' . $master->id), ['name' => 'Clubs'])->assertRedirect($this->url());
        $this->assertModelMissing($master);
    }

    public function test_the_old_new_master_sheet_url_goes_to_the_new_page(): void
    {
        $this->actingAs($this->organiser)->get($this->url('/master/add'))->assertRedirect($this->url('/master/create'))->assertStatus(301);
    }

    public function test_another_competitions_sheets_cant_be_reached_through_this_ones_urls(): void
    {
        $other = $this->comp->replicate();
        $other->save();
        $theirs = $this->sheet('Theirs');
        $theirs->competition = $other->id;
        $theirs->save();
        $master = $this->master('Their clubs', [$theirs]);
        $master->competition = $other->id;
        $master->save();

        $this->actingAs($this->organiser)->get($this->url('/' . $theirs->id))->assertNotFound();
        $this->actingAs($this->organiser)->post($this->url('/' . $theirs->id), $this->sheetData())->assertNotFound();
        $this->actingAs($this->organiser)->delete($this->url('/master/' . $master->id), ['name' => 'Their clubs'])->assertNotFound();
        $this->assertModelExists($master);
    }

    public function test_editing_waits_for_digitally_judged_events_too(): void
    {
        $sheet = $this->sheet('Overall');
        $master = $this->master('Clubs', [$sheet]);
        $this->unconfirmedDigitalJudge();

        $this->actingAs($this->organiser)->get($this->url('/' . $sheet->id))
            ->assertInertia(fn (Assert $page) => $page->where('canCreate', false)->where('unconfirmed.0.name', 'Dry SERC'));
        $this->actingAs($this->organiser)->get($this->url('/' . $sheet->id . '/edit'))->assertRedirect($this->url('/' . $sheet->id));
        $this->actingAs($this->organiser)->post($this->url('/' . $sheet->id), $this->sheetData(['name' => 'Renamed']))->assertSessionHasErrors('sheet');
        $this->assertEquals('Overall', $sheet->fresh()->name);

        $this->actingAs($this->organiser)->get($this->url('/master/create'))->assertRedirect($this->url());
        $this->actingAs($this->organiser)->get($this->url('/master/' . $master->id . '/edit'))->assertRedirect($this->url('/master/' . $master->id));
        $this->actingAs($this->organiser)->post($this->url('/master/' . $master->id), ['name' => 'Renamed', 'sum_over' => 'position', 'default_value' => 0, 'sheets' => [$sheet->id], 'exclude' => []])
            ->assertSessionHasErrors('sheet');
    }
}
