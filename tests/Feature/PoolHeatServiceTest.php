<?php

namespace Tests\Feature;

use App\Exceptions\HeatException;
use App\Models\AbstractClasses\Entity;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\Competitor;
use App\Models\League;
use App\Models\Orders\EntityEventSeed;
use App\Models\Orders\Heat;
use App\Models\Pool;
use App\Models\SpeedEvent;
use App\Services\PoolHeatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pool heats place every entity in a pool's leagues into that pool's heats. Heat numbers
 * restart per pool, leagues never share a heat, the fastest heat of a league runs last and
 * the fastest entity in a heat swims in the middle lane.
 */
class PoolHeatServiceTest extends TestCase
{
    use RefreshDatabase;

    private Competition $comp;
    private CompetitionSpeedEvent $event;

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
        $this->comp->competitor_format = ':N';
        $this->comp->save();

        $this->event = $this->speedEvent('Rope Throw', 'competitor');
    }

    private function speedEvent(string $name, string $entity): CompetitionSpeedEvent
    {
        $base = new SpeedEvent();
        $base->name = $name;
        $base->save();

        $event = new CompetitionSpeedEvent();
        $event->event = $base->id;
        $event->competition = $this->comp->id;
        $event->record = 0;
        $event->scorable_entity = $entity;
        $event->save();

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

    private function pool(string $name, int $lanes, array $leagues, array $pairLeagues = []): Pool
    {
        $pool = new Pool();
        $pool->competition = $this->comp->id;
        $pool->name = $name;
        $pool->lanes = $lanes;
        $pool->save();

        foreach (array_values($leagues) as $order => $league) {
            $pool->leagues()->attach($league->id, [
                'order' => $order,
                'is_pair' => in_array($league, $pairLeagues, true),
            ]);
        }

        return $pool;
    }

    private function team(string $letter = 'A'): CompetitionTeam
    {
        $team = new CompetitionTeam();
        $team->competition = $this->comp->id;
        $team->team = $letter;
        $team->save();

        return $team;
    }

    /**
     * A competitor in the given leagues with a seed time in seconds (null for no seed).
     */
    private function competitor(string $name, array $leagues, ?int $seed, ?CompetitionTeam $team = null): Competitor
    {
        $competitor = new Competitor();
        $competitor->competition = $this->comp->id;
        $competitor->name = $name;
        $competitor->team = ($team ?? $this->team())->id;
        $competitor->save();

        $competitor->leagues()->attach(collect($leagues)->pluck('id'));

        if ($seed !== null) {
            $this->seedTime($competitor, $seed);
        }

        return $competitor;
    }

    private function seedTime(Entity $entity, int $seconds): void
    {
        EntityEventSeed::create([
            'seed' => $seconds * 1000,
            'entity_type' => $entity->getMorphClass(),
            'entity_id' => $entity->id,
            'speed_event' => $this->event->id,
        ]);
    }

    /**
     * Heats as [pool name => [heat => [lane => entity name]]].
     */
    private function heats(): array
    {
        $out = [];

        foreach (Heat::with('entity', 'getPool')->orderBy('pool')->orderBy('heat')->orderBy('lane')->get() as $heat) {
            $out[$heat->getPool->name][$heat->heat][$heat->lane] = $heat->entity->name ?? $heat->entity->team;
        }

        return $out;
    }

    public function test_standard_heats_run_slowest_heat_first_with_the_fastest_in_the_middle_lane(): void
    {
        $open = $this->league('Open');
        $this->pool('Main Pool', 4, [$open]);

        // Seeds 1..6, so A is fastest
        foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $i => $name) {
            $this->competitor($name, [$open], $i + 1);
        }

        app(PoolHeatService::class)->generate($this->comp, PoolHeatService::STYLE_STANDARD);

        $this->assertSame([
            'Main Pool' => [
                1 => [2 => 'E', 3 => 'F'],
                2 => [1 => 'C', 2 => 'A', 3 => 'B', 4 => 'D'],
            ],
        ], $this->heats());
    }

    public function test_each_pool_numbers_its_own_heats_and_leagues_run_in_order_without_mixing(): void
    {
        $junior = $this->league('Junior');
        $senior = $this->league('Senior');
        $masters = $this->league('Masters');
        $this->pool('Main Pool', 4, [$senior, $junior]);
        $this->pool('Diving Pool', 2, [$masters]);

        $this->competitor('J1', [$junior], 10);
        $this->competitor('S1', [$senior], 20);
        $this->competitor('S2', [$senior], 21);
        $this->competitor('M1', [$masters], 30);
        $this->competitor('M2', [$masters], 31);
        $this->competitor('M3', [$masters], 32);

        app(PoolHeatService::class)->generate($this->comp, PoolHeatService::STYLE_STANDARD);

        $this->assertSame([
            'Main Pool' => [
                1 => [2 => 'S1', 3 => 'S2'],
                2 => [2 => 'J1'],
            ],
            'Diving Pool' => [
                1 => [1 => 'M3'],
                2 => [1 => 'M1', 2 => 'M2'],
            ],
        ], $this->heats());
    }

    public function test_pairs_swim_back_to_back_in_the_same_lane_ordered_by_their_faster_seed(): void
    {
        $pairs = $this->league('Open Pairs');
        $individual = $this->league('Open');
        $this->pool('Main Pool', 3, [$pairs, $individual], [$pairs]);

        $slowTeam = $this->team('A');
        $fastTeam = $this->team('B');
        $this->competitor('Slow 1', [$pairs], 50, $slowTeam);
        $this->competitor('Slow 2', [$pairs], 40, $slowTeam);
        $this->competitor('Fast 1', [$pairs], 60, $fastTeam);
        $this->competitor('Fast 2', [$pairs], 10, $fastTeam);
        $this->competitor('Solo', [$individual], 30);

        app(PoolHeatService::class)->generate($this->comp, PoolHeatService::STYLE_PAIRS);

        $this->assertSame([
            'Main Pool' => [
                1 => [2 => 'Fast 1', 3 => 'Slow 1'],
                2 => [2 => 'Fast 2', 3 => 'Slow 2'],
                3 => [2 => 'Solo'],
            ],
        ], $this->heats());
    }

    public function test_pair_leagues_with_more_pairs_than_lanes_run_slowest_chunk_first(): void
    {
        $pairs = $this->league('Open Pairs');
        $this->pool('Main Pool', 1, [$pairs], [$pairs]);

        $fast = $this->team('A');
        $slow = $this->team('B');
        $this->competitor('Fast 1', [$pairs], 10, $fast);
        $this->competitor('Fast 2', [$pairs], 11, $fast);
        $this->competitor('Slow 1', [$pairs], 20, $slow);
        $this->competitor('Slow 2', [$pairs], 21, $slow);

        app(PoolHeatService::class)->generate($this->comp, PoolHeatService::STYLE_PAIRS);

        $this->assertSame([
            'Main Pool' => [
                1 => [1 => 'Slow 1'],
                2 => [1 => 'Slow 2'],
                3 => [1 => 'Fast 1'],
                4 => [1 => 'Fast 2'],
            ],
        ], $this->heats());
    }

    public function test_pair_flags_are_ignored_in_the_standard_style(): void
    {
        $pairs = $this->league('Open Pairs');
        $this->pool('Main Pool', 4, [$pairs], [$pairs]);

        $team = $this->team('A');
        $this->competitor('P1', [$pairs], 10, $team);
        $this->competitor('P2', [$pairs], 20, $team);

        app(PoolHeatService::class)->generate($this->comp, PoolHeatService::STYLE_STANDARD);

        $this->assertSame(['Main Pool' => [1 => [2 => 'P1', 3 => 'P2']]], $this->heats());
    }

    public function test_a_pair_league_team_without_exactly_two_competitors_fails(): void
    {
        $pairs = $this->league('Open Pairs');
        $this->pool('Main Pool', 4, [$pairs], [$pairs]);

        $team = $this->team('A');
        $this->competitor('P1', [$pairs], 10, $team);
        $this->competitor('P2', [$pairs], 20, $team);
        $this->competitor('P3', [$pairs], 30, $team);

        $this->expectException(HeatException::class);
        $this->expectExceptionMessage('has 3 competitors in Open Pairs');

        app(PoolHeatService::class)->generate($this->comp, PoolHeatService::STYLE_PAIRS);
    }

    public function test_pair_style_fails_for_team_events(): void
    {
        $this->event->scorable_entity = 'team';
        $this->event->save();
        $this->pool('Main Pool', 4, [$this->league('Open')]);

        $this->expectException(HeatException::class);
        $this->expectExceptionMessage('Pair heats only work');

        app(PoolHeatService::class)->generate($this->comp, PoolHeatService::STYLE_PAIRS);
    }

    public function test_an_entity_in_two_pooled_leagues_fails_without_touching_existing_heats(): void
    {
        $open = $this->league('Open');
        $masters = $this->league('Masters');
        $this->pool('Main Pool', 4, [$open]);
        $this->pool('Diving Pool', 4, [$masters]);
        $this->competitor('Both', [$open, $masters], 10);

        $existing = $this->competitor('Placed', [], 10);
        $heat = new Heat();
        $heat->speed_event = $this->event->id;
        $heat->entity()->associate($existing);
        $heat->heat = 1;
        $heat->lane = 1;
        $heat->save();

        try {
            app(PoolHeatService::class)->generate($this->comp, PoolHeatService::STYLE_STANDARD);
            $this->fail('Expected a league clash');
        } catch (HeatException $e) {
            $this->assertStringContainsString('Both: Open (Main Pool), Masters (Diving Pool)', $e->getMessage());
        }

        $this->assertNotNull(Heat::find($heat->id));
    }

    public function test_a_missing_seed_fails_when_seeds_are_used(): void
    {
        $open = $this->league('Open');
        $this->pool('Main Pool', 4, [$open]);
        $this->competitor('Unseeded', [$open], null);

        $this->expectException(HeatException::class);
        $this->expectExceptionMessage('has no seed time');

        app(PoolHeatService::class)->generate($this->comp, PoolHeatService::STYLE_STANDARD);
    }

    public function test_without_seeds_entities_keep_their_order(): void
    {
        $this->comp->use_seeds = false;
        $this->comp->save();
        $open = $this->league('Open');
        $this->pool('Main Pool', 4, [$open]);
        $this->competitor('First', [$open], null);
        $this->competitor('Second', [$open], null);

        app(PoolHeatService::class)->generate($this->comp, PoolHeatService::STYLE_STANDARD);

        $this->assertSame(['Main Pool' => [1 => [2 => 'First', 3 => 'Second']]], $this->heats());
    }

    public function test_heats_per_event_competitions_are_refused(): void
    {
        $this->comp->heats_per_event = true;
        $this->comp->save();
        $this->pool('Main Pool', 4, [$this->league('Open')]);

        $this->expectException(HeatException::class);

        app(PoolHeatService::class)->generate($this->comp, PoolHeatService::STYLE_STANDARD);
    }

    public function test_regenerating_replaces_the_previous_heats(): void
    {
        $open = $this->league('Open');
        $this->pool('Main Pool', 4, [$open]);
        $this->competitor('A', [$open], 10);

        app(PoolHeatService::class)->generate($this->comp, PoolHeatService::STYLE_STANDARD);
        app(PoolHeatService::class)->generate($this->comp, PoolHeatService::STYLE_STANDARD);

        $this->assertSame(1, Heat::count());
    }

    public function test_entities_outside_every_pooled_league_are_reported_as_unplaced(): void
    {
        $open = $this->league('Open');
        $this->pool('Main Pool', 4, [$open]);
        $this->competitor('Placed', [$open], 10);
        $this->competitor('Missing', [$this->league('Masters')], 10);

        $unplaced = app(PoolHeatService::class)->unplacedEntities($this->comp);

        $this->assertSame(['Missing'], $unplaced->pluck('name')->all());
    }

    public function test_the_command_regenerates_heats_from_the_saved_pool_setup(): void
    {
        $open = $this->league('Open');
        $this->pool('Main Pool', 4, [$open]);
        $this->competitor('A', [$open], 10);

        $this->artisan('heats-and-draws:pool-heats', ['competition_id' => $this->comp->id])
            ->expectsConfirmation('Use the saved pool setup?', 'yes')
            ->expectsChoice('Heat style', PoolHeatService::STYLE_STANDARD, [PoolHeatService::STYLE_STANDARD, PoolHeatService::STYLE_PAIRS])
            ->expectsOutput('Main Pool: 1 heats, 1 entries')
            ->assertSuccessful();

        $this->assertSame(['Main Pool' => [1 => [2 => 'A']]], $this->heats());
    }
}
