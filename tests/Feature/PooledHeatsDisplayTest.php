<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\Orders\Heat;
use App\Models\Pool;
use App\Models\SpeedEvent;
use App\Models\SpeedResult;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Heat numbers restart in each pool, so every page that lists heats names the pool alongside
 * the heat, and the legacy Digital Judge, which only knows heat numbers, is closed to pools.
 */
class PooledHeatsDisplayTest extends TestCase
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
        $this->comp->show_heats = true;
        $this->comp->show_competition = true;
        $this->comp->digitalJudgeEnabled = true;
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

        $this->team('Main Team', $this->main, 1, 4);
        $this->team('Diving Team', $this->diving, 1, 2);

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

    private function team(string $name, Pool $pool, int $heat, int $lane): CompetitionTeam
    {
        $team = new CompetitionTeam();
        $team->competition = $this->comp->id;
        $team->team = $name;
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

    public function test_the_public_heats_page_labels_heats_with_their_pool(): void
    {
        $this->get(route('landing.competition.heats-draws', $this->comp))
            ->assertOk()
            ->assertSeeInOrder(['Main Pool · Heat 1', 'Main Team', 'Diving Pool · Heat 1', 'Diving Team']);
    }

    public function test_the_chief_timekeeper_pack_has_a_section_per_pool(): void
    {
        $this->actingAs($this->admin)
            ->get(route('comps.printables.chief-timekeeper-pack', $this->comp))
            ->assertOk()
            ->assertSeeInOrder(['Main Pool Heats', 'Diving Pool Heats'])
            ->assertSeeInOrder(['Main Pool', 'Main Team', 'Diving Pool', 'Diving Team']);
    }

    public function test_the_results_editor_filters_heats_by_pool(): void
    {
        $this->actingAs($this->admin)
            ->get(route('comps.events.speeds.editResult', [$this->comp, $this->event]))
            ->assertOk()
            ->assertSee('data-heat="' . $this->main->id . ':1"', false)
            ->assertSee('table-pool-filter="scores" data-pool="' . $this->main->id . '"', false)
            ->assertSee('table-pool-filter="scores" data-pool="' . $this->diving->id . '"', false)
            ->assertSee('data-pool="' . $this->diving->id . '" class="se-btn">Heat 1', false)
            ->assertSee('Main Pool · H1 L4')
            ->assertSee('Diving Pool · H1 L2');
    }

    public function test_the_legacy_digital_judge_speed_pages_are_closed_when_pools_are_in_use(): void
    {
        $this->withSession(['digitalJudgeClientAllowed' => true, 'digitalJudgeClientComp' => $this->comp->id])
            ->get(route('dj.speeds.times.index', $this->event))
            ->assertRedirect(route('dj.home'))
            ->assertSessionHas('alert-error');

        $this->withSession(['digitalJudgeClientAllowed' => true, 'digitalJudgeClientComp' => $this->comp->id])
            ->get(route('dj.dq.issue'))
            ->assertRedirect(route('dj.home'));
    }

    public function test_the_legacy_digital_judge_still_works_without_pools(): void
    {
        Heat::query()->update(['pool' => null]);
        $this->comp->use_pools = false;
        $this->comp->save();

        $this->withSession(['digitalJudgeClientAllowed' => true, 'digitalJudgeClientComp' => $this->comp->id])
            ->get(route('dj.speeds.times.index', $this->event))
            ->assertOk();
    }

    public function test_the_event_page_loads_when_a_heat_entity_has_no_result_row(): void
    {
        // Imported entities can be placed in heats without a result row for the event
        SpeedResult::query()->delete();

        $this->actingAs($this->admin)
            ->get(route('comps.events.speeds.view', [$this->comp, $this->event]))
            ->assertOk();
    }
}
