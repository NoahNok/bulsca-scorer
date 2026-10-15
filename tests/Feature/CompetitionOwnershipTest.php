<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\League;
use App\Models\Orders\Draw;
use App\Models\Orders\Heat;
use App\Models\SERC;
use App\Models\SpeedEvent;
use App\Models\SpeedResult;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * An organiser of one competition can't reach another competition's events, results, heats or
 * draws by putting its ids into their own competition's urls.
 */
class CompetitionOwnershipTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private array $ours;
    private array $theirs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ours = $this->competition('Our Competition');
        $this->theirs = $this->competition('Their Competition');

        $this->admin = User::factory()->create();
        $this->ours['comp']->addAccount($this->admin, 'admin');
    }

    private function competition(string $name): array
    {
        $comp = new Competition();
        $comp->name = $name;
        $comp->when = now()->addWeek();
        $comp->where = 'Test Pool';
        $comp->max_lanes = 8;
        $comp->anytimepin = false;
        $comp->seed_per_event = false;
        $comp->heats_per_event = true;
        $comp->use_seeds = true;
        $comp->save();

        $base = new SpeedEvent();
        $base->name = "$name event";
        $base->save();

        $event = new CompetitionSpeedEvent();
        $event->event = $base->id;
        $event->competition = $comp->id;
        $event->record = 0;
        $event->save();

        $serc = new SERC();
        $serc->name = 'Dry SERC';
        $serc->competition = $comp->id;
        $serc->scorable_entity = 'team';
        $serc->save();

        $league = new League();
        $league->name = 'Open';
        $league->competition = $comp->id;
        $league->save();

        $teams = [];
        $heats = [];
        $draws = [];
        $results = [];

        foreach (['A', 'B'] as $i => $letter) {
            $team = new CompetitionTeam();
            $team->competition = $comp->id;
            $team->team = $letter;
            $team->save();
            $teams[] = $team;

            $heat = new Heat();
            $heat->speed_event = $event->id;
            $heat->entity()->associate($team);
            $heat->heat = 1;
            $heat->lane = $i + 1;
            $heat->save();
            $heats[] = $heat;

            $draw = new Draw();
            $draw->serc = $serc->id;
            $draw->entity()->associate($team);
            $draw->tank = 1;
            $draw->draw = $i + 1;
            $draw->save();
            $draws[] = $draw;

            $result = new SpeedResult();
            $result->event = $event->id;
            $result->entity()->associate($team);
            $result->result = 60000;
            $result->save();
            $results[] = $result;
        }

        return compact('comp', 'event', 'serc', 'league', 'teams', 'heats', 'draws', 'results');
    }

    private function resultRow(SpeedResult $result, string $time): array
    {
        return ['id' => $result->id, 'values' => ['disqualification' => '', 'result' => $time]];
    }

    public function test_cannot_open_or_delete_another_competitions_events_or_leagues(): void
    {
        $ours = $this->ours['comp'];
        $this->actingAs($this->admin);

        $this->get(route('comps.events.speeds.view', [$ours, $this->theirs['event']]))->assertNotFound();
        $this->get(route('comps.events.sercs.view', [$ours, $this->theirs['serc']]))->assertNotFound();
        $this->get(route('comps.leagues.view', [$ours, $this->theirs['league']]))->assertNotFound();

        $this->delete(route('comps.view.events.speeds.delete', [$ours, $this->theirs['event']]), ['eid' => $this->theirs['event']->id])->assertNotFound();
        $this->delete(route('comps.view.events.sercs.delete', [$ours, $this->theirs['serc']]), ['sid' => $this->theirs['serc']->id])->assertNotFound();
        $this->delete(route('comps.leagues.delete', [$ours, $this->theirs['league']]))->assertNotFound();

        $this->assertNotNull($this->theirs['event']->fresh());
        $this->assertNotNull($this->theirs['serc']->fresh());
        $this->assertNotNull($this->theirs['league']->fresh());
    }

    public function test_can_still_open_and_delete_our_own_events(): void
    {
        $ours = $this->ours['comp'];
        $this->actingAs($this->admin);

        $this->get(route('comps.leagues.view', [$ours, $this->ours['league']]))->assertOk();

        $this->delete(route('comps.view.events.speeds.delete', [$ours, $this->ours['event']]), ['eid' => $this->ours['event']->id])->assertRedirect();
        $this->assertNull($this->ours['event']->fresh());
    }

    public function test_cannot_change_another_competitions_speed_results(): void
    {
        $this->actingAs($this->admin)
            ->post(route('comps.view.events.speeds.editResultPost', [$this->ours['comp'], $this->ours['event']]), [
                'data' => json_encode([$this->resultRow($this->theirs['results'][0], '0:30.000')]),
            ])
            ->assertNotFound();

        $this->assertEquals(60000, $this->theirs['results'][0]->fresh()->result);
    }

    public function test_cannot_change_another_competitions_serc_results(): void
    {
        $this->actingAs($this->admin)
            ->get(route('comps.events.sercs.editResults', [$this->ours['comp'], $this->ours['serc'], $this->theirs['teams'][0]->id]))
            ->assertNotFound();

        $this->actingAs($this->admin)
            ->post(route('comps.view.events.sercs.editResultsPost', [$this->ours['comp'], $this->ours['serc'], $this->theirs['teams'][0]->id]), ['data' => '[]'])
            ->assertNotFound();
    }

    public function test_cannot_move_another_competitions_heats_or_draws(): void
    {
        [$theirA, $theirB] = $this->theirs['heats'];

        $this->actingAs($this->admin)
            ->post(route('comps.heats_and_draws.heats.swap', [$this->ours['comp'], $this->ours['event']]), [
                'team' => $theirA->id,
                'target-heat' => $theirB->id,
            ])
            ->assertNotFound();

        $this->assertSame(1, $theirA->fresh()->lane);

        [$drawA, $drawB] = $this->theirs['draws'];

        $this->actingAs($this->admin)
            ->postJson(route('comps.heats_and_draws.draws.swap', [$this->ours['comp'], $this->ours['serc']]), [
                'swap_from' => $drawA->id,
                'swap_to' => $drawB->id,
            ])
            ->assertStatus(400);

        $this->assertSame(1, $drawA->fresh()->draw);
    }

    public function test_can_still_move_our_own_heats(): void
    {
        [$ourA, $ourB] = $this->ours['heats'];

        $this->actingAs($this->admin)
            ->post(route('comps.heats_and_draws.heats.swap', [$this->ours['comp'], $this->ours['event']]), [
                'team' => $ourA->id,
                'target-heat' => $ourB->id,
            ])
            ->assertRedirect();

        $this->assertSame(2, $ourA->fresh()->lane);
    }
}
