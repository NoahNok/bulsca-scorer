<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\SERC;
use App\Models\SpeedEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * A competition's Events page (Svelte, in CompetitionLayout): its speed events and SERCs as cards, by
 * access, and adding a speed event from its dialog.
 */
class CompetitionEventsTest extends TestCase
{
    use RefreshDatabase;

    private User $organiser;

    private Competition $comp;

    private SpeedEvent $base;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organiser = User::factory()->create();
        $this->comp = $this->competition('Events Champs');
        $this->comp->addAccount($this->organiser, ['owner']);

        $this->base = new SpeedEvent();
        $this->base->name = 'Swim & Tow';
        $this->base->record = 60000;
        $this->base->save();
    }

    private function competition(string $name): Competition
    {
        $comp = new Competition();
        $comp->name = $name;
        $comp->when = now()->addWeek();
        $comp->where = 'Test Pool';
        $comp->max_lanes = 8;
        $comp->anytimepin = false;
        $comp->seed_per_event = false;
        $comp->heats_per_event = false;
        $comp->show_competition = true;
        $comp->save();

        return $comp;
    }

    private function url(string $path = ''): string
    {
        return $this->appUrl('/comps/' . $this->comp->id . '/events' . $path);
    }

    private function speedEvent(): CompetitionSpeedEvent
    {
        $event = new CompetitionSpeedEvent();
        $event->event = $this->base->id;
        $event->competition = $this->comp->id;
        $event->record = 0;
        $event->save();

        return $event;
    }

    private function serc(): SERC
    {
        $serc = new SERC();
        $serc->name = 'Dry SERC';
        $serc->type = 'DRY';
        $serc->competition = $this->comp->id;
        $serc->scorable_entity = 'team';
        $serc->save();

        return $serc;
    }

    public function test_the_page_lists_speed_events_and_sercs_as_cards(): void
    {
        $event = $this->speedEvent();
        $event->viewable = false;
        $event->save();
        $serc = $this->serc();

        $this->actingAs($this->organiser)->get($this->url())
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Competition/Events')
                ->where('competition.name', 'Events Champs')
                ->has('speeds', 1)
                ->where('speeds.0.name', 'Swim & Tow')
                ->where('speeds.0.target', 'team')
                ->where('speeds.0.hidden', true)
                ->where('speeds.0.complete', false)
                ->has('sercs', 1)
                ->where('sercs.0.name', 'Dry SERC')
                ->where('sercs.0.type', 'DRY')
                ->where('sercs.0.url', route('manage.competitions.events.sercs.show', [$this->comp, $serc]))
                ->has('speedOptions', 1));
    }

    public function test_the_events_tab_is_a_svelte_page(): void
    {
        $this->actingAs($this->organiser)->get($this->url())
            ->assertInertia(fn (Assert $page) => $page
                ->where('competition.tabs', fn ($tabs) => collect($tabs)->contains(
                    fn ($tab) => $tab['label'] === 'Events' && $tab['current'] && $tab['svelte']
                )));
    }

    public function test_each_type_needs_its_own_access(): void
    {
        $this->speedEvent();
        $this->serc();

        $speeds = User::factory()->create();
        $this->comp->addAccount($speeds, ['speed']);

        $this->actingAs($speeds)->get($this->url())
            ->assertInertia(fn (Assert $page) => $page->has('speeds', 1)->where('sercs', null));

        $writer = User::factory()->create();
        $this->comp->addAccount($writer, ['serc_writer']);

        $this->actingAs($writer)->get($this->url())
            ->assertInertia(fn (Assert $page) => $page->where('speeds', null)->has('sercs', 1)->where('speedOptions', []));

        $this->actingAs($writer)
            ->post($this->url('/speeds'), ['event' => $this->base->id, 'target_entity' => 'team'])
            ->assertForbidden();
    }

    public function test_adding_a_speed_event_gives_every_entity_a_result_and_opens_it(): void
    {
        $club = Club::create(['competition' => $this->comp->id, 'name' => 'Cardiff']);
        CompetitionTeam::create(['competition' => $this->comp->id, 'club' => $club->id, 'team' => 'A']);
        CompetitionTeam::create(['competition' => $this->comp->id, 'club' => $club->id, 'team' => 'B']);

        $response = $this->actingAs($this->organiser)->from($this->url())
            ->post($this->url('/speeds'), ['event' => $this->base->id, 'target_entity' => 'team']);

        $event = CompetitionSpeedEvent::where('competition', $this->comp->id)->sole();
        $response->assertRedirect($this->url('/speeds/' . $event->id))->assertSessionHas('success');
        $this->assertSame('team', $event->scorable_entity);
        $this->assertEquals(60000, $event->record);
        $this->assertEquals(1, $event->weight);
        $this->assertSame(2, $event->results()->count());
    }

    public function test_adding_a_speed_event_needs_a_real_event(): void
    {
        $this->actingAs($this->organiser)->from($this->url())
            ->post($this->url('/speeds'), ['event' => 9999, 'target_entity' => 'team'])
            ->assertSessionHasErrors('event');

        $this->assertSame(0, CompetitionSpeedEvent::count());
    }

    public function test_another_competitions_organiser_cant_open_it(): void
    {
        $other = User::factory()->create();
        $this->competition('Theirs')->addAccount($other, ['owner']);

        $this->actingAs($other)->get($this->url())->assertForbidden();
    }
}
