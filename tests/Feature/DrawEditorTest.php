<?php

namespace Tests\Feature;

use App\Models\Activity\Activity;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\League;
use App\Models\Orders\Draw;
use App\Models\SERC;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * The draw editor and Tank setup (Svelte, full width). A competition has one set of Draws, held by its
 * first SERC and shared by them all, so neither page names a SERC.
 */
class DrawEditorTest extends TestCase
{
    use RefreshDatabase;

    private User $organiser;

    private Competition $comp;

    private SERC $serc;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organiser = User::factory()->create();

        $this->comp = new Competition();
        $this->comp->name = 'Draw Champs';
        $this->comp->when = now()->addWeek();
        $this->comp->where = 'Test Pool';
        $this->comp->max_lanes = 4;
        $this->comp->anytimepin = false;
        $this->comp->seed_per_event = false;
        $this->comp->heats_per_event = false;
        $this->comp->save();
        $this->comp->addAccount($this->organiser, ['owner']);

        $this->serc = $this->serc('Dry SERC');
    }

    private function serc(string $name): SERC
    {
        $serc = new SERC();
        $serc->name = $name;
        $serc->type = 'DRY';
        $serc->scorable_entity = 'team';
        $serc->competition = $this->comp->id;
        $serc->save();

        return $serc;
    }

    private function useTanks(): void
    {
        $settings = $this->comp->getScoringSettings;
        $settings->use_tanks = true;
        $settings->save();
    }

    private function league(string $name): League
    {
        $league = new League();
        $league->competition = $this->comp->id;
        $league->name = $name;
        $league->save();

        return $league;
    }

    private function url(string $path = ''): string
    {
        return $this->appUrl('/comps/' . $this->comp->id . '/heats-and-draws/draws' . $path);
    }

    private function team(string $letter, ?int $tank = null, int $draw = 0): CompetitionTeam
    {
        $team = CompetitionTeam::create(['competition' => $this->comp->id, 'team' => $letter]);

        if ($tank !== null) {
            $this->serc->draw()->create(['entity_id' => $team->id, 'entity_type' => $team->getMorphClass(), 'tank' => $tank, 'draw' => $draw]);
        }

        return $team;
    }

    private function drawOf(CompetitionTeam $team): ?Draw
    {
        return $this->serc->draw()->whereMorphedTo('entity', $team)->first();
    }

    /** Posts from the editor, as the page does */
    private function edit(string $action, array $data = [])
    {
        return $this->actingAs($this->organiser)->from($this->url('/edit'))->post($this->url('/' . $action), $data);
    }

    public function test_the_editor_shows_the_draw_and_who_isnt_in_it(): void
    {
        $a = $this->team('A', 0, 1);
        $b = $this->team('B');

        $this->actingAs($this->organiser)->get($this->url('/edit'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Competition/DrawEditor')
                ->where('useTanks', false)
                ->where('tanks.0.number', 0)
                ->where('tanks.0.draws.0.name', $a->getName($this->comp))
                ->where('unassigned', [['id' => $b->id, 'name' => $b->getName($this->comp)]]));
    }

    public function test_the_old_per_serc_editor_url_redirects(): void
    {
        $this->actingAs($this->organiser)->get($this->url('/' . $this->serc->id . '/edit'))
            ->assertRedirect($this->url('/edit'));
    }

    public function test_generating_draws_every_entity_in_the_first_sercs_draw(): void
    {
        $this->serc('Wet SERC');
        $this->team('A');
        $this->team('B');

        $this->actingAs($this->organiser)->post($this->url('/generate'))->assertSessionHas('success');

        $this->assertSame([1, 2], $this->serc->draw()->orderBy('draw')->pluck('draw')->all());
        $this->assertSame(2, Draw::count());
    }

    public function test_generating_is_refused_once_there_is_a_draw(): void
    {
        $a = $this->team('A', 0, 5);

        $this->actingAs($this->organiser)->post($this->url('/generate'))->assertSessionHasErrors('draw');
        $this->assertSame(5, $this->drawOf($a)->draw);
    }

    public function test_with_tanks_generating_opens_tank_setup(): void
    {
        $this->useTanks();

        $this->actingAs($this->organiser)->post($this->url('/generate'))->assertRedirect($this->url('/tank-setup'));
    }

    public function test_swapping_two_draws(): void
    {
        $a = $this->team('A', 0, 1);
        $b = $this->team('B', 0, 2);

        $this->edit('swap', ['from' => $this->drawOf($a)->id, 'to' => $this->drawOf($b)->id])->assertRedirect($this->url('/edit'));

        $this->assertSame(2, $this->drawOf($a)->draw);
        $this->assertSame(1, $this->drawOf($b)->draw);
        $this->assertTrue(Activity::where('activity', 'DRAW_SWAP')->exists());
    }

    public function test_moving_and_adding_before_after_and_to_the_end(): void
    {
        $a = $this->team('A', 0, 1);
        $b = $this->team('B', 0, 2);
        $c = $this->team('C');

        $this->edit('move', ['source_type' => 'draw', 'source' => $this->drawOf($b)->id, 'target' => $this->drawOf($a)->id, 'placement' => 'before']);
        $this->assertSame([1, 2], [$this->drawOf($b)->draw, $this->drawOf($a)->draw]);

        $this->edit('move', ['source_type' => 'entity', 'source' => $c->id, 'target_tank' => 0, 'placement' => 'end']);
        $this->assertSame(3, $this->drawOf($c)->draw);
    }

    public function test_an_entity_already_in_the_draw_cant_be_added_again(): void
    {
        $a = $this->team('A', 0, 1);

        $this->edit('assign', ['entity' => $a->id, 'tank' => 0])->assertSessionHasErrors('draw');
        $this->assertSame(1, $this->serc->draw()->count());
    }

    public function test_removing_a_draw_leaves_its_position_open_until_compacted(): void
    {
        $a = $this->team('A', 0, 1);
        $b = $this->team('B', 0, 2);
        $c = $this->team('C', 0, 3);

        $this->edit('remove', ['draw' => $this->drawOf($b)->id]);
        $this->assertNull($this->drawOf($b));
        $this->assertSame(3, $this->drawOf($c)->draw);

        $this->edit('compact-tanks');
        $this->assertSame([1, 2], [$this->drawOf($a)->draw, $this->drawOf($c)->draw]);
    }

    public function test_tanks_can_be_added_and_removed(): void
    {
        $this->useTanks();
        $a = $this->team('A', 1, 1);
        $this->serc->drawTanks()->create(['tank' => 1]);

        $this->edit('add-tank');
        $this->assertSame([1, 2], $this->serc->drawTanks()->orderBy('tank')->pluck('tank')->all());

        $this->edit('remove-tank', ['tank' => 1]);
        $this->assertSame([1], $this->serc->drawTanks()->pluck('tank')->all());
        $this->assertNull($this->drawOf($a));
    }

    public function test_tank_actions_need_tanks(): void
    {
        $this->edit('add-tank')->assertSessionHasErrors('draw');
        $this->assertSame(0, $this->serc->drawTanks()->count());
    }

    public function test_regenerating_redraws_without_tanks_only(): void
    {
        $a = $this->team('A');
        $this->edit('regenerate')->assertSessionHas('success');
        $this->assertNotNull($this->drawOf($a));

        $this->useTanks();
        $this->edit('regenerate')->assertRedirect($this->url('/tank-setup'));
    }

    public function test_tank_setup_shows_each_league_and_the_tanks_it_is_in(): void
    {
        $this->useTanks();
        $league = $this->league('A League');
        $a = $this->team('A', 1, 1);
        $a->leagues()->attach($league->id);
        $this->team('B');

        $this->actingAs($this->organiser)->get($this->url('/tank-setup'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Competition/TankSetup')
                ->where('leagues', [
                    ['id' => $league->id, 'name' => 'A League', 'count' => 1],
                    ['id' => -1, 'name' => 'No League', 'count' => 1],
                ])
                ->where('tanks', [[$league->id]]));
    }

    public function test_saving_tank_setup_draws_each_league_in_its_tank(): void
    {
        $this->useTanks();
        $league = $this->league('A League');
        $a = $this->team('A');
        $a->leagues()->attach($league->id);
        $b = $this->team('B');

        $this->actingAs($this->organiser)->post($this->url('/tank-setup'), ['tanks' => [[-1], [$league->id]]])
            ->assertRedirect($this->url('/edit'));

        $this->assertSame(2, $this->drawOf($a)->tank);
        $this->assertSame(1, $this->drawOf($b)->tank);
        $this->assertSame([1, 2], $this->serc->drawTanks()->orderBy('tank')->pluck('tank')->all());
    }

    public function test_tank_setup_needs_every_league_in_exactly_one_tank(): void
    {
        $this->useTanks();
        $league = $this->league('A League');
        $this->team('A')->leagues()->attach($league->id);
        $this->team('B');

        $this->actingAs($this->organiser)->from($this->url('/tank-setup'))->post($this->url('/tank-setup'), ['tanks' => [[$league->id]]])
            ->assertSessionHasErrors('tanks');
        $this->actingAs($this->organiser)->post($this->url('/tank-setup'), ['tanks' => [[$league->id, -1], [-1]]])
            ->assertSessionHasErrors('tanks');

        $this->assertSame(0, $this->serc->draw()->count());
    }

    public function test_editing_needs_a_post(): void
    {
        foreach (['/generate', '/regenerate', '/swap', '/compact-tanks'] as $path) {
            $this->actingAs($this->organiser)->get($this->url($path))->assertStatus(405);
        }
    }
}
