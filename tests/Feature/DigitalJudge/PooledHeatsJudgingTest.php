<?php

namespace Tests\Feature\DigitalJudge;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\EventOOF;
use App\Models\Orders\Heat;
use App\Models\Pool;
use App\Models\SpeedEvent;
use App\Models\SpeedResult;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/**
 * With pools, heat numbers restart in each pool, so the judge app picks a heat by pool and
 * heat number together and only ever reads or writes that pool's lanes.
 */
class PooledHeatsJudgingTest extends TestCase
{
    use RefreshDatabase;

    private Competition $comp;
    private CompetitionSpeedEvent $event;
    private User $official;
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
        $this->comp->use_seeds = true;
        $this->comp->use_pools = true;
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

        $this->official = User::factory()->create(['judge_only' => true]);
        $this->comp->addOfficial($this->official);
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

    private function team(string $letter, Pool $pool, int $heat, int $lane): CompetitionTeam
    {
        $team = new CompetitionTeam();
        $team->competition = $this->comp->id;
        $team->team = $letter;
        $team->save();

        $row = new Heat();
        $row->speed_event = $this->event->id;
        $row->entity()->associate($team);
        $row->heat = $heat;
        $row->lane = $lane;
        $row->pool = $pool->id;
        $row->save();

        $result = new SpeedResult();
        $result->event = $this->event->id;
        $result->entity()->associate($team);
        $result->save();

        return $team;
    }

    private function params(int $heat, Pool $pool): array
    {
        return [
            'competition' => $this->comp->getSlug(),
            'event' => $this->event,
            'heat' => $heat,
            'pool' => $pool->id,
        ];
    }

    private function resultFor(CompetitionTeam $team): ?string
    {
        return SpeedResult::whereMorphedTo('entity', $team)->where('event', $this->event->id)->value('result');
    }

    public function test_heats_are_listed_per_pool_in_pool_order(): void
    {
        $this->team('D', $this->diving, 1, 2);
        $this->team('M', $this->main, 1, 4);
        $this->team('N', $this->main, 2, 4);

        $this->actingAs($this->official)
            ->get(route('judge.competition.event.time.select-heat', ['competition' => $this->comp->getSlug(), 'event' => $this->event]))
            ->assertInertia(fn(AssertableInertia $page) => $page
                ->has('heats', 3)
                ->where('heats.0.heat', 1)
                ->where('heats.0.pool.name', 'Main Pool')
                ->where('heats.1.heat', 2)
                ->where('heats.1.pool.name', 'Main Pool')
                ->where('heats.2.heat', 1)
                ->where('heats.2.pool.name', 'Diving Pool')
                ->where('heats.2.pool.lanes', 4));
    }

    public function test_marking_a_heat_shows_only_that_pools_lanes(): void
    {
        $this->team('M', $this->main, 1, 4);
        $diving = $this->team('D', $this->diving, 1, 2);

        $this->actingAs($this->official)
            ->get(route('judge.competition.event.time.mark', $this->params(1, $this->diving)))
            ->assertInertia(fn(AssertableInertia $page) => $page
                ->where('heat.pool.name', 'Diving Pool')
                ->where('heat.pool.lanes', 4)
                ->has('heat.lanes', 1)
                ->where('heat.lanes.0.entity.id', $diving->id));

        $this->actingAs($this->official)
            ->get(route('judge.competition.event.oof.mark', $this->params(1, $this->diving)))
            ->assertInertia(fn(AssertableInertia $page) => $page
                ->where('heat.pool.name', 'Diving Pool')
                ->has('heat.lanes', 1));
    }

    public function test_times_are_saved_against_the_chosen_pools_heat_only(): void
    {
        $main = $this->team('M', $this->main, 1, 4);
        $this->team('N', $this->main, 2, 4);
        $diving = $this->team('D', $this->diving, 1, 2);

        $this->actingAs($this->official)
            ->postJson(route('judge.competition.event.time.mark.store', $this->params(1, $this->diving)), ['mark' => [$main->id => '1:00.00']])
            ->assertStatus(422);

        $this->actingAs($this->official)
            ->postJson(route('judge.competition.event.time.mark.store', $this->params(1, $this->diving)), ['mark' => [$diving->id => '1:00.00']])
            ->assertOk()
            ->assertJson(['hasNextHeat' => false]);

        $this->actingAs($this->official)
            ->postJson(route('judge.competition.event.time.mark.store', $this->params(1, $this->main)), ['mark' => [$main->id => '1:01.00']])
            ->assertOk()
            ->assertJson(['hasNextHeat' => true]);

        $this->assertEquals(60000, $this->resultFor($diving));
        $this->assertEquals(61000, $this->resultFor($main));
    }

    public function test_places_are_saved_against_the_chosen_pools_heat_only(): void
    {
        $main = $this->team('M', $this->main, 1, 4);
        $diving = $this->team('D', $this->diving, 1, 2);

        $this->actingAs($this->official)
            ->postJson(route('judge.competition.event.oof.mark.store', $this->params(1, $this->diving)), [['entity' => ['id' => $main->id], 'oof' => 1]])
            ->assertStatus(422);

        $this->actingAs($this->official)
            ->postJson(route('judge.competition.event.oof.mark.store', $this->params(1, $this->diving)), [['entity' => ['id' => $diving->id], 'oof' => 1]])
            ->assertOk();

        $this->assertSame(1, EventOOF::count());
        $this->assertSame(Heat::whereMorphedTo('entity', $diving)->value('id'), EventOOF::first()->heat_lane);
    }

    public function test_violation_heat_picker_labels_heats_with_their_pool(): void
    {
        $this->team('M', $this->main, 1, 4);
        $this->team('D', $this->diving, 1, 2);

        $this->actingAs($this->official)
            ->getJson(route('judge.competition.violation.issue.getHeats', ['competition' => $this->comp->getSlug(), 'event' => $this->event]))
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonPath('0.pool.name', 'Main Pool')
            ->assertJsonPath('1.pool.name', 'Diving Pool');
    }

    public function test_confirm_results_shows_each_pools_heats_with_that_pools_lanes(): void
    {
        $this->team('M', $this->main, 1, 4);
        $this->team('D', $this->diving, 1, 2);

        $referee = User::factory()->create(['judge_only' => true]);
        $this->comp->addOfficial($referee, 'referee');

        $this->actingAs($referee)
            ->get(route('judge.competition.event.confirm-results', ['competition' => $this->comp->getSlug(), 'event' => $this->event]))
            ->assertInertia(fn(AssertableInertia $page) => $page
                ->where('summary.missingOof', 2)
                ->has('heats.data', 2)
                ->where('heats.data.0.pool.name', 'Main Pool')
                ->has('heats.data.0.lanes', 8)
                ->where('heats.data.1.pool.name', 'Diving Pool')
                ->has('heats.data.1.lanes', 4));
    }
}
