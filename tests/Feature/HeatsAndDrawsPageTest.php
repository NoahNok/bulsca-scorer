<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\Orders\Heat;
use App\Models\SERC;
use App\Models\SpeedEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * Heats & Draws' overview (Svelte, in CompetitionLayout): the competition's Heats one event and Pool at a
 * time, its Draw, and switches to show either publicly.
 */
class HeatsAndDrawsPageTest extends TestCase
{
    use RefreshDatabase;

    private User $organiser;

    private Competition $comp;

    /** @var CompetitionSpeedEvent[] */
    private array $events = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->organiser = User::factory()->create();

        $this->comp = new Competition();
        $this->comp->name = 'Heats Champs';
        $this->comp->when = now()->addWeek();
        $this->comp->where = 'Test Pool';
        $this->comp->max_lanes = 4;
        $this->comp->anytimepin = false;
        $this->comp->seed_per_event = false;
        $this->comp->heats_per_event = false;
        $this->comp->use_seeds = false;
        $this->comp->save();
        $this->comp->addAccount($this->organiser, ['owner']);

        foreach (['Swim & Tow', 'Obstacle'] as $name) {
            $base = new SpeedEvent();
            $base->name = $name;
            $base->save();

            $event = new CompetitionSpeedEvent();
            $event->event = $base->id;
            $event->competition = $this->comp->id;
            $event->record = 0;
            $event->save();
            $this->events[] = $event;
        }
    }

    private function url(string $path = ''): string
    {
        return $this->appUrl('/comps/' . $this->comp->id . '/heats-and-draws' . $path);
    }

    private function team(string $letter, ?CompetitionSpeedEvent $event = null, int $heat = 1, int $lane = 1): CompetitionTeam
    {
        $team = CompetitionTeam::create(['competition' => $this->comp->id, 'team' => $letter]);

        if ($event) {
            $row = new Heat();
            $row->speed_event = $event->id;
            $row->entity()->associate($team);
            $row->heat = $heat;
            $row->lane = $lane;
            $row->save();
        }

        return $team;
    }

    private function serc(): SERC
    {
        $serc = new SERC();
        $serc->name = 'Dry SERC';
        $serc->type = 'DRY';
        $serc->scorable_entity = 'team';
        $serc->competition = $this->comp->id;
        $serc->save();

        return $serc;
    }

    public function test_it_shows_the_heats_as_lanes_by_heat(): void
    {
        $a = $this->team('A', $this->events[0], 1, 2);
        $b = $this->team('B', $this->events[0], 2, 4);

        $this->actingAs($this->organiser)->get($this->url())
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Competition/HeatsAndDraws')
                ->where('heats.events', [['id' => $this->events[0]->id, 'name' => 'Swim & Tow']])
                ->where('heats.event', $this->events[0]->id)
                ->where('heats.grid.lanes', 4)
                ->has('heats.grid.heats', 2)
                ->where('heats.grid.heats.0.number', 1)
                ->where('heats.grid.heats.0.lanes.0', null)
                ->where('heats.grid.heats.0.lanes.1.name', $a->getName($this->comp))
                ->where('heats.grid.heats.1.lanes.3.name', $b->getName($this->comp))
                ->where('heats.canGenerate', false));
    }

    public function test_with_heats_per_event_it_shows_the_chosen_events_heats(): void
    {
        $this->comp->heats_per_event = true;
        $this->comp->save();
        $this->team('A', $this->events[0], 1, 1);
        $b = $this->team('B', $this->events[1], 3, 1);

        $this->actingAs($this->organiser)->get($this->url('?event=' . $this->events[1]->id))
            ->assertInertia(fn (Assert $page) => $page
                ->has('heats.events', 2)
                ->where('heats.event', $this->events[1]->id)
                ->has('heats.grid.heats', 3)
                ->where('heats.grid.heats.2.lanes.0.name', $b->getName($this->comp)));
    }

    public function test_an_event_that_holds_no_heats_is_not_found(): void
    {
        // Without heats per event, only the first event holds Heats
        $this->actingAs($this->organiser)->get($this->url('?event=' . $this->events[1]->id))->assertNotFound();
    }

    public function test_generate_is_offered_only_while_there_are_no_heats(): void
    {
        $this->actingAs($this->organiser)->get($this->url())
            ->assertInertia(fn (Assert $page) => $page->where('heats.canGenerate', true)->where('heats.generated', false));
    }

    public function test_without_speed_events_there_is_nothing_to_show_or_generate(): void
    {
        foreach ($this->events as $event) {
            $event->delete();
        }

        $this->actingAs($this->organiser)->get($this->url())
            ->assertInertia(fn (Assert $page) => $page
                ->where('heats.events', [])
                ->where('heats.grid', null)
                ->where('heats.canGenerate', false)
                ->where('draw.hasSerc', false));
    }

    public function test_it_shows_the_draw_in_running_order(): void
    {
        $serc = $this->serc();
        $a = $this->team('A');
        $b = $this->team('B');
        $serc->draw()->create(['entity_id' => $a->id, 'entity_type' => $a->getMorphClass(), 'tank' => 0, 'draw' => 2]);
        $serc->draw()->create(['entity_id' => $b->id, 'entity_type' => $b->getMorphClass(), 'tank' => 0, 'draw' => 1]);

        $this->actingAs($this->organiser)->get($this->url())
            ->assertInertia(fn (Assert $page) => $page
                ->where('draw.hasSerc', true)
                ->where('draw.generated', true)
                ->has('draw.tanks', 1)
                ->where('draw.tanks.0.draws.0.name', $b->getName($this->comp))
                ->where('draw.tanks.0.draws.1.name', $a->getName($this->comp)));
    }

    public function test_heats_and_the_draw_can_be_shown_publicly(): void
    {
        $this->actingAs($this->organiser)->from($this->url())->post($this->url('/visibility'), ['show_heats' => true])
            ->assertRedirect($this->url());
        $this->actingAs($this->organiser)->post($this->url('/visibility'), ['show_draws' => false]);

        $this->comp->refresh();
        $this->assertTrue((bool) $this->comp->show_heats);
        $this->assertFalse((bool) $this->comp->show_draws);

        $this->actingAs($this->organiser)->post($this->url('/visibility'), ['show_heats' => false]);
        $this->assertFalse((bool) $this->comp->fresh()->show_heats);
    }
}
