<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\Event\ScoringSchema;
use App\Models\Orders\Heat;
use App\Models\SpeedEvent;
use App\Models\SpeedResult;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * A speed event's page (Svelte, in CompetitionLayout): its results, the grid to enter them, its setup,
 * scoring and deleting it, each saved on its own.
 */
class SpeedEventPageTest extends TestCase
{
    use RefreshDatabase;

    private User $organiser;

    private Competition $comp;

    private CompetitionSpeedEvent $event;

    /** @var SpeedResult[] */
    private array $results = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->organiser = User::factory()->create();

        $this->comp = new Competition();
        $this->comp->name = 'Speed Champs';
        $this->comp->when = now()->addWeek();
        $this->comp->where = 'Test Pool';
        $this->comp->max_lanes = 8;
        $this->comp->anytimepin = false;
        $this->comp->seed_per_event = false;
        $this->comp->heats_per_event = false;
        $this->comp->show_competition = true;
        $this->comp->save();
        $this->comp->addAccount($this->organiser, ['owner']);

        $club = Club::create(['competition' => $this->comp->id, 'name' => 'Cardiff']);
        $this->event = $this->speedEvent('Swim & Tow');

        foreach (['A', 'B'] as $name) {
            $team = CompetitionTeam::create(['competition' => $this->comp->id, 'club' => $club->id, 'team' => $name]);
            $result = new SpeedResult();
            $result->event = $this->event->id;
            $result->entity()->associate($team);
            $result->save();
            $this->results[] = $result;
        }
    }

    private function speedEvent(string $name): CompetitionSpeedEvent
    {
        $base = new SpeedEvent();
        $base->name = $name;
        $base->save();

        $event = new CompetitionSpeedEvent();
        $event->event = $base->id;
        $event->competition = $this->comp->id;
        $event->scorable_entity = 'team';
        $event->record = 0;
        $event->save();

        return $event;
    }

    private function url(string $path = '', ?CompetitionSpeedEvent $event = null): string
    {
        return $this->appUrl('/comps/' . $this->comp->id . '/events/speeds/' . ($event ?? $this->event)->id . $path);
    }

    private function saveRows(array $rows, ?CompetitionSpeedEvent $event = null)
    {
        return $this->actingAs($this->organiser)->from($this->url('/results', $event))->post($this->url('/results', $event), ['rows' => $rows]);
    }

    public function test_the_page_shows_its_results(): void
    {
        $this->actingAs($this->organiser)->get($this->url())
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Competition/SpeedEvent')
                ->where('event.name', 'Swim & Tow')
                ->where('event.target', 'team')
                ->where('results', null)
                ->has('conflicts', 0)
                ->missing('grid'));
    }

    public function test_results_are_edited_on_their_own_page(): void
    {
        $this->actingAs($this->organiser)->get($this->url('/results'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Competition/SpeedEventResults')
                ->where('event.name', 'Swim & Tow')
                ->has('grid', 2)
                ->where('grid.0.name', fn ($name) => str_contains($name, 'A'))
                ->where('grid.0.result', ''));
    }

    public function test_its_settings_have_their_own_page(): void
    {
        $this->actingAs($this->organiser)->get($this->url('/settings'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Competition/SpeedEventSettings')
                ->where('event.target', 'team')
                ->where('scoring.schema', null));
    }

    public function test_it_opens_when_its_heats_hold_entities_it_doesnt_score(): void
    {
        // Without heats per event, every event uses the first one's heats, here of teams
        $heat = new Heat();
        $heat->speed_event = $this->event->id;
        $heat->entity()->associate($this->results[0]->entity);
        $heat->heat = 1;
        $heat->lane = 1;
        $heat->save();

        $clubs = $this->speedEvent('Rope Throw');
        $clubs->scorable_entity = 'club';
        $clubs->save();

        $this->actingAs($this->organiser)->get($this->url('', $clubs))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->has('conflicts', 0));
        $this->actingAs($this->organiser)->get($this->url('/results', $clubs))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->has('grid', 0));
    }

    public function test_results_are_ranked_once_it_has_scoring(): void
    {
        $schema = new ScoringSchema();
        $schema->name = 'Fastest';
        $schema->schema = ['equation' => '1', 'global_variables' => [], 'rank_higher' => false];
        $schema->save();
        $this->event->scoring_schema = $schema->id;
        $this->event->save();

        $this->saveRows([$this->results[0]->id => ['result' => '01:02.50', 'disqualification' => '']]);

        $this->actingAs($this->organiser)->get($this->url())
            ->assertInertia(fn (Assert $page) => $page
                ->has('results', 1)
                ->where('results.0.time', '01:02.500'));

        $this->actingAs($this->organiser)->get($this->url('/settings'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('scoring.schema.equation', '1')
                ->where('scoring.schema.group_by', []));
    }

    public function test_saving_results_saves_times_and_codes(): void
    {
        [$a, $b] = $this->results;

        $this->saveRows([
            $a->id => ['result' => '01:02.50', 'disqualification' => 'dq101'],
            $b->id => ['result' => 'DNS', 'disqualification' => ''],
        ])->assertRedirect($this->url('/results'))->assertSessionHas('success');

        $this->assertEquals(62500, $a->fresh()->result);
        $this->assertEquals([101], $this->event->disqualifications()->where('entity_id', $a->entity_id)->pluck('code')->all());
        $this->assertEquals([99904], $this->event->disqualifications()->where('entity_id', $b->entity_id)->pluck('code')->all());

        $this->actingAs($this->organiser)->get($this->url('/results'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('grid.0.result', '01:02.500')
                ->where('grid.0.disqualification', 'DQ101')
                ->where('grid.1.result', 'DNS')
                ->where('grid.1.disqualification', ''));
    }

    public function test_a_wrong_cell_saves_no_row_and_says_which(): void
    {
        [$a, $b] = $this->results;

        $this->saveRows([
            $a->id => ['result' => '01:02.50', 'disqualification' => ''],
            $b->id => ['result' => '1.2', 'disqualification' => 'X'],
        ])->assertSessionHasErrors(["rows.{$b->id}.result", "rows.{$b->id}.disqualification"]);

        $this->assertNull($a->fresh()->result);
    }

    public function test_penalties_are_saved_and_cleared(): void
    {
        $this->event->has_penalties = true;
        $this->event->save();
        $a = $this->results[0];

        $this->saveRows([$a->id => ['result' => '', 'disqualification' => '', 'penalties' => 'p100, P200']]);
        $this->assertEquals([100, 200], $this->event->penalties()->where('entity_id', $a->entity_id)->orderBy('code')->pluck('code')->all());

        $this->saveRows([$a->id => ['result' => '', 'disqualification' => '', 'penalties' => 'P1x']])
            ->assertSessionHasErrors("rows.{$a->id}.penalties");

        $this->saveRows([$a->id => ['result' => '', 'disqualification' => '', 'penalties' => '']]);
        $this->assertSame(0, $this->event->penalties()->count());
    }

    public function test_rope_throw_takes_ropes_in_or_a_time(): void
    {
        $rope = $this->speedEvent('Rope Throw');
        $result = new SpeedResult();
        $result->event = $rope->id;
        $result->entity()->associate($this->results[0]->entity);
        $result->save();

        $this->saveRows([$result->id => ['result' => '2', 'disqualification' => '']], $rope);
        $this->assertEquals(2, $result->fresh()->result);

        $this->saveRows([$result->id => ['result' => '00:45.123', 'disqualification' => '']], $rope);
        $this->assertEquals(45123, $result->fresh()->result);

        $this->saveRows([$result->id => ['result' => '5', 'disqualification' => '']], $rope)
            ->assertSessionHasErrors("rows.{$result->id}.result");
    }

    public function test_another_events_results_cant_be_saved_through_this_one(): void
    {
        $other = $this->speedEvent('Rope Throw');
        $theirs = new SpeedResult();
        $theirs->event = $other->id;
        $theirs->entity()->associate($this->results[0]->entity);
        $theirs->save();

        $this->saveRows([$theirs->id => ['result' => '00:30.00', 'disqualification' => '']])->assertNotFound();
        $this->assertNull($theirs->fresh()->result);
    }

    public function test_scoring_other_entities_starts_its_results_again(): void
    {
        $this->saveRows([$this->results[0]->id => ['result' => '01:02.50', 'disqualification' => '']]);

        $this->actingAs($this->organiser)->from($this->url('/settings'))
            ->post($this->url('/setup'), ['target_entity' => 'club', 'has_penalties' => true])
            ->assertRedirect($this->url('/settings'));

        $this->event->refresh();
        $this->assertSame('club', $this->event->scorable_entity);
        $this->assertTrue((bool) $this->event->has_penalties);
        $this->assertSame(1, $this->event->results()->count());
        $this->assertNull($this->event->results()->first()->result);
    }

    public function test_keeping_its_entities_keeps_its_results(): void
    {
        $this->saveRows([$this->results[0]->id => ['result' => '01:02.50', 'disqualification' => '']]);

        $this->actingAs($this->organiser)->post($this->url('/setup'), ['target_entity' => 'team', 'has_penalties' => false]);

        $this->assertEquals(62500, $this->results[0]->fresh()->result);
    }

    public function test_it_can_be_hidden_from_results(): void
    {
        $this->actingAs($this->organiser)->post($this->url('/visibility'), ['viewable' => false])->assertSessionHas('success');
        $this->assertFalse((bool) $this->event->fresh()->viewable);

        $this->actingAs($this->organiser)->post($this->url('/visibility'), ['viewable' => true]);
        $this->assertTrue((bool) $this->event->fresh()->viewable);
    }

    public function test_saving_scoring_sets_it_up(): void
    {
        $this->actingAs($this->organiser)->from($this->url('/settings'))->post($this->url('/scoring'), [
            'equation' => 'result',
            'global_variables' => [],
            'local_variables' => [],
            'auto_penalties' => [],
            'auto_disqualifications' => [],
            'rank_higher' => false,
            'allow_disqualified_to_rank' => false,
            'group_by' => ['league'],
            'force_penalty' => false,
        ])->assertRedirect($this->url('/settings'))->assertSessionHas('success');

        $schema = $this->event->fresh()->scoringSchema;
        $this->assertSame('result', $schema->schema['equation']);
        $this->assertSame(['league'], $schema->schema['group_by']);

        $this->actingAs($this->organiser)->post($this->url('/scoring'), ['equation' => ''])->assertSessionHasErrors('equation');
    }

    public function test_deleting_it_goes_back_to_events(): void
    {
        $this->actingAs($this->organiser)->delete($this->url(), ['name' => 'Swim & Tow'])
            ->assertRedirect($this->appUrl('/comps/' . $this->comp->id . '/events'));

        $this->assertNull($this->event->fresh());
    }

    public function test_deleting_it_needs_its_name(): void
    {
        $this->actingAs($this->organiser)->from($this->url('/settings'))->delete($this->url(), ['name' => 'Swim'])
            ->assertSessionHasErrors('name');

        $this->assertNotNull($this->event->fresh());
    }

    public function test_old_pages_redirect_to_their_new_ones(): void
    {
        $this->actingAs($this->organiser)->get($this->url('/edit'))->assertRedirect($this->url('/settings'));
        $this->actingAs($this->organiser)->get($this->url('/edit-result'))->assertRedirect($this->url('/results'));
        $this->actingAs($this->organiser)->get($this->url('/scoring-settings'))->assertRedirect($this->url('/settings') . '#scoring');
    }

    public function test_it_needs_speed_access(): void
    {
        $writer = User::factory()->create();
        $this->comp->addAccount($writer, ['serc_writer']);

        $this->actingAs($writer)->get($this->url())->assertForbidden();
        $this->actingAs($writer)->get($this->url('/results'))->assertForbidden();
        $this->actingAs($writer)->get($this->url('/settings'))->assertForbidden();
        $this->actingAs($writer)->post($this->url('/results'), ['rows' => []])->assertForbidden();
    }
}
