<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\Orders\Heat;
use App\Models\Pool;
use App\Models\SpeedEvent;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pools split a competition's speed heats across separate pools, each with its own heat
 * numbering, so a pool can't be removed while heats still use it and the use_pools toggle
 * can't be combined with heats per event.
 */
class PoolsTest extends TestCase
{
    use RefreshDatabase;

    private Competition $comp;
    private CompetitionSpeedEvent $event;
    private User $admin;

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
        $this->comp->use_seeds = true;
        $this->comp->save();

        $base = new SpeedEvent();
        $base->name = 'Rope Throw';
        $base->save();

        $this->event = new CompetitionSpeedEvent();
        $this->event->event = $base->id;
        $this->event->competition = $this->comp->id;
        $this->event->record = 0;
        $this->event->save();

        $this->admin = User::factory()->create();
        $this->comp->addAccount($this->admin, 'admin');
    }

    private function pool(string $name = 'Main Pool', int $lanes = 8): Pool
    {
        $pool = new Pool();
        $pool->competition = $this->comp->id;
        $pool->name = $name;
        $pool->lanes = $lanes;
        $pool->save();

        return $pool;
    }

    private function pooledHeat(Pool $pool): Heat
    {
        $team = new CompetitionTeam();
        $team->competition = $this->comp->id;
        $team->team = 'A';
        $team->save();

        $heat = new Heat();
        $heat->speed_event = $this->event->id;
        $heat->entity()->associate($team);
        $heat->heat = 1;
        $heat->lane = 4;
        $heat->pool = $pool->id;
        $heat->save();

        return $heat;
    }

    private function saveSettings(array $settings)
    {
        return $this->actingAs($this->admin)->postJson(route('comps.settings', $this->comp), $settings);
    }

    public function test_pools_cannot_be_turned_on_with_heats_per_event(): void
    {
        $this->saveSettings(['heats_per_event' => true, 'use_pools' => true])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['use_pools' => 'Pools can only be used when heats per event is off.']);

        $this->assertFalse((bool) $this->comp->fresh()->use_pools);
    }

    public function test_heats_per_event_cannot_be_turned_on_while_pools_are_in_use(): void
    {
        $this->comp->use_pools = true;
        $this->comp->save();

        $this->saveSettings(['heats_per_event' => true, 'use_pools' => true])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['heats_per_event' => 'Heats per event cannot be turned on while pools are in use.']);

        $this->assertFalse((bool) $this->comp->fresh()->heats_per_event);
    }

    public function test_pools_can_be_turned_on_and_off_when_no_heats_use_them(): void
    {
        $this->saveSettings(['heats_per_event' => false, 'use_pools' => true])->assertOk();
        $this->assertTrue((bool) $this->comp->fresh()->use_pools);

        $this->saveSettings(['use_pools' => false])->assertOk();
        $this->assertFalse((bool) $this->comp->fresh()->use_pools);
    }

    public function test_pools_cannot_be_turned_off_while_heats_are_assigned_to_pools(): void
    {
        $this->comp->use_pools = true;
        $this->comp->save();
        $this->pooledHeat($this->pool());

        $this->saveSettings(['use_pools' => false])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['use_pools' => 'Pools cannot be turned off while heats are assigned to pools.']);

        $this->assertTrue((bool) $this->comp->fresh()->use_pools);
    }

    public function test_a_pool_with_heats_cannot_be_deleted(): void
    {
        $pool = $this->pool();
        $this->pooledHeat($pool);

        $this->expectException(QueryException::class);

        $pool->delete();
    }

    public function test_a_pool_without_heats_can_be_deleted(): void
    {
        $pool = $this->pool();

        $pool->delete();

        $this->assertNull(Pool::find($pool->id));
    }

    public function test_a_competition_with_pooled_heats_can_be_deleted(): void
    {
        $pool = $this->pool();
        $heat = $this->pooledHeat($pool);

        $this->comp->delete();

        $this->assertNull(Pool::find($pool->id));
        $this->assertNull(Heat::find($heat->id));
    }
}
