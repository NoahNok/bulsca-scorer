<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\Orders\Heat;
use App\Models\Pool;
use App\Models\SpeedEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Organisers edit pooled heats one pool at a time: every change stays inside a pool, and
 * pooled heats come from the pool-heats command rather than the Generate button.
 */
class PooledHeatsEditingTest extends TestCase
{
    use RefreshDatabase;

    private Competition $comp;
    private CompetitionSpeedEvent $event;
    private User $admin;
    private Pool $main;
    private Pool $diving;

    protected function setUp(): void
    {
        parent::setUp();

        $this->comp = new Competition();
        $this->comp->name = 'Pools Competition';
        $this->comp->when = now()->addWeek();
        $this->comp->where = 'Test Pool';
        $this->comp->max_lanes = 8;
        $this->comp->anytimepin = false;
        $this->comp->seed_per_event = false;
        $this->comp->heats_per_event = false;
        $this->comp->use_seeds = false;
        $this->comp->use_pools = true;
        $this->comp->team_format = ':N';
        $this->comp->save();

        $base = new SpeedEvent();
        $base->name = 'Swim & Tow';
        $base->save();

        $this->event = new CompetitionSpeedEvent();
        $this->event->event = $base->id;
        $this->event->competition = $this->comp->id;
        $this->event->record = 0;
        $this->event->save();

        $this->main = $this->pool('Main Pool', 8);
        $this->diving = $this->pool('Diving Pool', 4);

        $this->admin = User::factory()->create();
        $this->comp->addAccount($this->admin, 'admin');
    }

    private function pool(string $name, int $lanes): Pool
    {
        $pool = new Pool();
        $pool->competition = $this->comp->id;
        $pool->name = $name;
        $pool->lanes = $lanes;
        $pool->save();

        return $pool;
    }

    private function team(string $letter, ?Pool $pool = null, int $heat = 1, int $lane = 1): CompetitionTeam
    {
        $team = new CompetitionTeam();
        $team->competition = $this->comp->id;
        $team->team = $letter;
        $team->save();

        if ($pool) {
            $row = new Heat();
            $row->speed_event = $this->event->id;
            $row->entity()->associate($team);
            $row->heat = $heat;
            $row->lane = $lane;
            $row->pool = $pool->id;
            $row->save();
        }

        return $team;
    }

    private function heatOf(CompetitionTeam $team): Heat
    {
        return Heat::whereMorphedTo('entity', $team)->firstOrFail();
    }

    private function postHeats(string $route, array $data)
    {
        return $this->actingAs($this->admin)->postJson(route("comps.heats_and_draws.heats.$route", [$this->comp, $this->event]), $data);
    }

    public function test_generate_and_reset_are_refused_when_pools_are_in_use(): void
    {
        $team = $this->team('A', $this->diving, 1, 2);

        $this->actingAs($this->admin)
            ->get(route('comps.heats_and_draws.heats.generate', $this->comp))
            ->assertSessionHas('alert-error');

        $this->actingAs($this->admin)
            ->get(route('comps.heats_and_draws.heats.reset', [$this->comp, $this->event]))
            ->assertSessionHas('alert-error');

        $this->assertSame($this->diving->id, $this->heatOf($team)->pool);
    }

    public function test_swapping_entities_across_pools_is_rejected(): void
    {
        $main = $this->team('A', $this->main, 1, 4);
        $diving = $this->team('B', $this->diving, 1, 2);

        $this->postHeats('swap', ['team' => $this->heatOf($main)->id, 'target-heat' => $this->heatOf($diving)->id, 'target-heatlane' => '1:2', 'pool' => $this->main->id])
            ->assertStatus(422);

        $this->assertSame(4, $this->heatOf($main)->lane);
        $this->assertSame($this->main->id, $this->heatOf($main)->pool);
    }

    public function test_moving_an_entity_to_an_open_lane_keeps_it_in_its_pool(): void
    {
        $main = $this->team('A', $this->main, 1, 4);

        $this->postHeats('swap', ['team' => $this->heatOf($main)->id, 'target-heatlane' => '2:5', 'pool' => $this->main->id]);

        $heat = $this->heatOf($main);
        $this->assertSame([2, 5, $this->main->id], [$heat->heat, $heat->lane, $heat->pool]);

        $this->postHeats('swap', ['team' => $heat->id, 'target-heatlane' => '1:1', 'pool' => $this->diving->id])
            ->assertStatus(422);
    }

    public function test_swapping_whole_heats_only_touches_the_chosen_pool(): void
    {
        $mainOne = $this->team('A', $this->main, 1, 4);
        $mainTwo = $this->team('B', $this->main, 2, 4);
        $diving = $this->team('C', $this->diving, 1, 2);

        $this->postHeats('swapHeats', ['first' => 1, 'second' => 2, 'pool' => $this->main->id])->assertOk();

        $this->assertSame(2, $this->heatOf($mainOne)->heat);
        $this->assertSame(1, $this->heatOf($mainTwo)->heat);
        $this->assertSame(1, $this->heatOf($diving)->heat);
    }

    public function test_inserting_and_deleting_heats_only_renumbers_the_chosen_pool(): void
    {
        $main = $this->team('A', $this->main, 1, 4);
        $diving = $this->team('B', $this->diving, 1, 2);

        $this->postHeats('insertHeat', ['position' => 1, 'pool' => $this->main->id])->assertOk();

        $this->assertSame(2, $this->heatOf($main)->heat);
        $this->assertSame(1, $this->heatOf($diving)->heat);

        $this->postHeats('deleteHeats', ['heat' => 1, 'pool' => $this->main->id])->assertOk();

        $this->assertSame(1, $this->heatOf($main)->heat);
        $this->assertSame(1, $this->heatOf($diving)->heat);
    }

    public function test_assigning_needs_a_pool_and_places_the_entity_in_it(): void
    {
        $this->team('A', $this->main, 1, 2);
        $unassigned = $this->team('B');

        $this->postHeats('assign', ['entity' => $unassigned->id, 'heat' => 1, 'lane' => 2])->assertStatus(422);

        $this->postHeats('assign', ['entity' => $unassigned->id, 'heat' => 1, 'lane' => 2, 'pool' => $this->diving->id])->assertOk();

        $heat = $this->heatOf($unassigned);
        $this->assertSame([1, 2, $this->diving->id], [$heat->heat, $heat->lane, $heat->pool]);
    }

    public function test_a_pool_from_another_competition_is_rejected(): void
    {
        $this->team('A', $this->main, 1, 4);

        $other = new Competition();
        $other->forceFill($this->comp->only(['when', 'where', 'max_lanes', 'anytimepin', 'seed_per_event', 'heats_per_event', 'use_seeds']));
        $other->name = 'Other';
        $other->save();
        $foreign = new Pool();
        $foreign->competition = $other->id;
        $foreign->name = 'Foreign';
        $foreign->lanes = 8;
        $foreign->save();

        $this->postHeats('insertHeat', ['position' => 1, 'pool' => $foreign->id])->assertNotFound();
    }

    public function test_the_edit_page_shows_one_pool_at_a_time(): void
    {
        $this->team('Main Team', $this->main, 1, 4);
        $this->team('Diving Team', $this->diving, 1, 2);

        $this->actingAs($this->admin)
            ->get(route('comps.heats_and_draws.heats.edit', [$this->comp, $this->event]) . '?pool=' . $this->diving->id)
            ->assertOk()
            ->assertSee('Diving Team')
            ->assertDontSee('Main Team')
            ->assertSee('Main Pool');

        $this->actingAs($this->admin)
            ->get(route('comps.heats_and_draws.heats.edit', [$this->comp, $this->event]))
            ->assertOk()
            ->assertSee('Main Team');
    }

    public function test_the_heats_page_lists_heats_per_pool(): void
    {
        $this->team('Main Team', $this->main, 1, 4);
        $this->team('Diving Team', $this->diving, 1, 2);

        $this->actingAs($this->admin)
            ->get(route('comps.heats_and_draws', $this->comp))
            ->assertOk()
            ->assertSeeInOrder(['Main Pool', 'Main Team', 'Diving Pool', 'Diving Team'])
            ->assertDontSee('Generate Heats');
    }
}
