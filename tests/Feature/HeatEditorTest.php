<?php

namespace Tests\Feature;

use App\Models\Activity\Activity;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\Orders\Heat;
use App\Models\SpeedEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * The heat editor (Svelte, full width): an event's Heats as a lane × Heat grid, where every edit saves at
 * once and is recorded as Activity. Pooled heats are covered in PooledHeatsEditingTest.
 */
class HeatEditorTest extends TestCase
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

    private function url(string $path = '', ?CompetitionSpeedEvent $event = null): string
    {
        $event ??= $this->events[0];

        return $this->appUrl('/comps/' . $this->comp->id . '/heats-and-draws/heats/' . $event->id . $path);
    }

    private function team(string $letter, int $heat = 0, int $lane = 0): CompetitionTeam
    {
        $team = CompetitionTeam::create(['competition' => $this->comp->id, 'team' => $letter]);

        if ($heat) {
            $row = new Heat();
            $row->speed_event = $this->events[0]->id;
            $row->entity()->associate($team);
            $row->heat = $heat;
            $row->lane = $lane;
            $row->save();
        }

        return $team;
    }

    private function heatOf(CompetitionTeam $team): ?Heat
    {
        return Heat::whereMorphedTo('entity', $team)->first();
    }

    /** Posts from the editor, as the page does */
    private function edit(string $action, array $data = [])
    {
        return $this->actingAs($this->organiser)->from($this->url('/edit'))->post($this->url('/' . $action), $data);
    }

    /** What the edit recorded as Activity */
    private function activity(string $code): ?string
    {
        return Activity::where('activity', $code)->value('description');
    }

    public function test_the_editor_shows_the_grid_and_who_isnt_in_it(): void
    {
        $a = $this->team('A', 1, 2);
        $b = $this->team('B');

        $this->actingAs($this->organiser)->get($this->url('/edit'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Competition/HeatEditor')
                ->where('event.id', $this->events[0]->id)
                ->where('grid.heats.0.lanes.1.name', $a->getName($this->comp))
                ->where('unassigned', [['id' => $b->id, 'name' => $b->getName($this->comp), 'seed' => null]])
                ->where('canReset', true));
    }

    public function test_without_heats_per_event_another_events_editor_opens_the_first(): void
    {
        $this->actingAs($this->organiser)->get($this->url('/edit', $this->events[1]))
            ->assertRedirect($this->url('/edit'));
    }

    public function test_swapping_two_lanes(): void
    {
        $a = $this->team('A', 1, 1);
        $b = $this->team('B', 2, 3);

        $this->edit('swap', ['from' => $this->heatOf($a)->id, 'to' => $this->heatOf($b)->id])
            ->assertRedirect($this->url('/edit'))
            ->assertSessionHas('success');

        $this->assertSame([2, 3], [$this->heatOf($a)->heat, $this->heatOf($a)->lane]);
        $this->assertSame([1, 1], [$this->heatOf($b)->heat, $this->heatOf($b)->lane]);
        $this->assertSame("Swapped {$a->getName($this->comp)} (Heat 1, Lane 1) and {$b->getName($this->comp)} (Heat 2, Lane 3)", $this->activity('HEAT_SWAP'));
    }

    public function test_assigning_moves_an_entity_into_an_open_lane(): void
    {
        $a = $this->team('A', 1, 1);
        $b = $this->team('B');

        $this->edit('assign', ['entity' => $b->id, 'heat' => 2, 'lane' => 4])->assertSessionHasNoErrors();
        $this->assertSame([2, 4], [$this->heatOf($b)->heat, $this->heatOf($b)->lane]);
        $this->assertSame("Moved {$b->getName($this->comp)} to Heat 2, Lane 4", $this->activity('HEAT_MOVED'));

        // An assigned Entity moves, rather than being in two lanes
        $this->edit('assign', ['entity' => $a->id, 'heat' => 1, 'lane' => 3]);
        $this->assertSame(1, Heat::whereMorphedTo('entity', $a)->count());
        $this->assertSame(3, $this->heatOf($a)->lane);
    }

    public function test_an_occupied_lane_cant_be_assigned(): void
    {
        $this->team('A', 1, 1);
        $b = $this->team('B');

        $this->edit('assign', ['entity' => $b->id, 'heat' => 1, 'lane' => 1])->assertSessionHasErrors('heats');
        $this->assertNull($this->heatOf($b));
    }

    public function test_unassigning_takes_an_entity_out_of_the_heats(): void
    {
        $a = $this->team('A', 1, 1);

        $this->edit('unassign', ['entity' => $a->id]);

        $this->assertNull($this->heatOf($a));
        $this->assertSame("Unassigned {$a->getName($this->comp)} from Heat 1, Lane 1", $this->activity('HEAT_UNASSIGNED'));
    }

    public function test_swapping_whole_heats(): void
    {
        $a = $this->team('A', 1, 1);
        $b = $this->team('B', 2, 2);

        $this->edit('swapHeats', ['first' => 1, 'second' => 2]);

        $this->assertSame(2, $this->heatOf($a)->heat);
        $this->assertSame(1, $this->heatOf($b)->heat);
        $this->assertSame('Swapped Heat 1 and Heat 2', $this->activity('HEATS_SWAPPED'));
    }

    public function test_inserting_a_heat_moves_later_ones_up(): void
    {
        $a = $this->team('A', 1, 1);
        $b = $this->team('B', 2, 1);

        $this->edit('insertHeat', ['position' => 2]);

        $this->assertSame(1, $this->heatOf($a)->heat);
        $this->assertSame(3, $this->heatOf($b)->heat);
        $this->assertSame('Added Heat 2', $this->activity('HEAT_ADDED'));
    }

    public function test_removing_a_heat_unassigns_its_entities_and_moves_later_ones_down(): void
    {
        $a = $this->team('A', 1, 1);
        $b = $this->team('B', 2, 1);

        $this->edit('deleteHeat', ['heat' => 1]);

        $this->assertNull($this->heatOf($a));
        $this->assertSame(1, $this->heatOf($b)->heat);
        $this->assertSame('Removed Heat 1 and unassigned 1 entry', $this->activity('HEAT_REMOVED'));
    }

    public function test_generating_and_resetting_lay_out_every_entity(): void
    {
        $teams = collect(['A', 'B', 'C', 'D', 'E'])->map(fn ($letter) => $this->team($letter));

        $this->actingAs($this->organiser)->post($this->appUrl('/comps/' . $this->comp->id . '/heats-and-draws/heats/generate'))
            ->assertSessionHas('success');

        $this->assertSame(5, Heat::where('speed_event', $this->events[0]->id)->count());
        $this->assertSame(2, (int) Heat::where('speed_event', $this->events[0]->id)->max('heat'));

        $this->edit('unassign', ['entity' => $teams[0]->id]);
        $this->edit('reset')->assertSessionHas('success');

        $this->assertNotNull($this->heatOf($teams[0]));
        $this->assertTrue(Activity::where('activity', 'HEATS_GENERATED')->exists());
        $this->assertSame('Reset Swim & Tow\'s heats', $this->activity('HEATS_RESET'));
    }

    public function test_generating_is_refused_once_there_are_heats(): void
    {
        $a = $this->team('A', 1, 3);

        $this->actingAs($this->organiser)->post($this->appUrl('/comps/' . $this->comp->id . '/heats-and-draws/heats/generate'))
            ->assertSessionHasErrors('heats');
        $this->assertSame(3, $this->heatOf($a)->lane);
    }

    public function test_another_competitions_event_is_not_found(): void
    {
        $other = new Competition();
        $other->forceFill($this->comp->only(['when', 'where', 'max_lanes', 'anytimepin', 'seed_per_event', 'heats_per_event']));
        $other->name = 'Other';
        $other->save();
        $event = $this->events[1]->replicate();
        $event->competition = $other->id;
        $event->save();

        $this->actingAs($this->organiser)->get($this->url('/edit', $event))->assertNotFound();
        $this->edit('insertHeat', ['position' => 1])->assertSessionHasNoErrors();
        $this->actingAs($this->organiser)->post($this->url('/insertHeat', $event), ['position' => 1])->assertNotFound();
    }

    public function test_generating_without_every_seed_says_why(): void
    {
        $this->comp->use_seeds = true;
        $this->comp->save();
        $this->team('A');

        $this->actingAs($this->organiser)->from($this->appUrl('/comps/' . $this->comp->id . '/heats-and-draws'))
            ->post($this->appUrl('/comps/' . $this->comp->id . '/heats-and-draws/heats/generate'))
            ->assertSessionHasErrors('heats');
    }

    public function test_editing_needs_a_post(): void
    {
        foreach (['generate' => '/comps/' . $this->comp->id . '/heats-and-draws/heats/generate'] as $path) {
            $this->actingAs($this->organiser)->get($this->appUrl($path))->assertStatus(405);
        }

        $this->actingAs($this->organiser)->get($this->url('/reset'))->assertStatus(405);
        $this->actingAs($this->organiser)->get($this->appUrl('/comps/' . $this->comp->id . '/heats-and-draws/visibility'))->assertStatus(405);
    }
}
