<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\Event\ScoringSchema;
use App\Models\Orders\Draw;
use App\Models\SERC;
use App\Models\SERC\MarkingPointTemplate;
use App\Models\SERCJudge;
use App\Models\SERCMarkingPoint;
use App\Models\SERCResult;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * A SERC's pages (Svelte, in CompetitionLayout): its results and Entities in draw order, its setup
 * (also New SERC), and its settings. A SERC Writer sees the results and edits the setup.
 */
class SercEventPageTest extends TestCase
{
    use RefreshDatabase;

    private User $organiser;

    private Competition $comp;

    private SERC $serc;

    /** @var CompetitionTeam[] */
    private array $teams = [];

    /** @var SERCMarkingPoint[] */
    private array $markingPoints = [];

    private MarkingPointTemplate $template;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organiser = User::factory()->create();

        $this->comp = new Competition();
        $this->comp->name = 'SERC Champs';
        $this->comp->when = now()->addWeek();
        $this->comp->where = 'Test Pool';
        $this->comp->max_lanes = 8;
        $this->comp->anytimepin = false;
        $this->comp->seed_per_event = false;
        $this->comp->heats_per_event = false;
        $this->comp->show_competition = true;
        $this->comp->save();
        $this->comp->addAccount($this->organiser, ['owner']);

        $this->template = MarkingPointTemplate::create(['name' => 'Default', 'settings' => []]);

        $this->serc = new SERC();
        $this->serc->name = 'Dry SERC';
        $this->serc->type = 'DRY';
        $this->serc->scorable_entity = 'team';
        $this->serc->competition = $this->comp->id;
        $this->serc->save();

        $objective = new SERCJudge();
        $objective->name = 'Casualty 1';
        $objective->description = '';
        $objective->serc = $this->serc->id;
        $objective->save();

        foreach (['Checks for danger', 'Calls for help'] as $name) {
            $mp = new SERCMarkingPoint();
            $mp->name = $name;
            $mp->weight = 1;
            $mp->judge = $objective->id;
            $mp->serc = $this->serc->id;
            $mp->marking_point_template_id = $this->template->id;
            $mp->save();
            $this->markingPoints[] = $mp;
        }

        $club = Club::create(['competition' => $this->comp->id, 'name' => 'Cardiff']);
        foreach (['A', 'B'] as $name) {
            $this->teams[] = CompetitionTeam::create(['competition' => $this->comp->id, 'club' => $club->id, 'team' => $name]);
        }
    }

    private function url(string $path = ''): string
    {
        return $this->appUrl('/comps/' . $this->comp->id . '/events/sercs/' . $this->serc->id . $path);
    }

    private function mark(CompetitionTeam $team, SERCMarkingPoint $mp, int $result): void
    {
        $mark = new SERCResult();
        $mark->marking_point = $mp->id;
        $mark->entity()->associate($team);
        $mark->result = $result;
        $mark->save();
    }

    private function writer(): User
    {
        $writer = User::factory()->create();
        $this->comp->addAccount($writer, ['serc_writer']);

        return $writer;
    }

    public function test_the_page_shows_every_entity_in_draw_order_with_its_marking(): void
    {
        [$a, $b] = $this->teams;
        $this->mark($a, $this->markingPoints[0], 7);

        // B is drawn first
        foreach ([$b, $a] as $index => $team) {
            $draw = new Draw(['entity_id' => $team->id, 'entity_type' => $team->getMorphClass(), 'tank' => 1, 'draw' => $index + 1]);
            $draw->serc = $this->serc->id;
            $draw->save();
        }

        $this->actingAs($this->organiser)->get($this->url())
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Competition/Serc')
                ->where('event.name', 'Dry SERC')
                ->where('event.canManage', true)
                ->where('results', null)
                ->where('drawn', true)
                ->where('marking', ['marked' => 1, 'possible' => 4])
                ->has('entities', 2)
                ->where('entities.0.id', $b->id)
                ->where('entities.0.draw', '1')
                ->where('entities.0.marked', 0)
                ->where('entities.1.id', $a->id)
                ->where('entities.1.marked', 1)
                ->where('entities.1.possible', 2));
    }

    public function test_results_are_ranked_once_it_has_scoring(): void
    {
        $schema = new ScoringSchema();
        $schema->name = 'Most marks';
        $schema->schema = ['equation' => '1', 'global_variables' => [], 'rank_higher' => true];
        $schema->save();
        $this->serc->scoring_schema = $schema->id;
        $this->serc->save();

        $this->mark($this->teams[0], $this->markingPoints[0], 7);
        $this->mark($this->teams[0], $this->markingPoints[1], 3);

        $this->actingAs($this->organiser)->get($this->url())
            ->assertInertia(fn (Assert $page) => $page
                ->where('drawn', false)
                ->has('results', 1)
                ->where('results.0.mark', 10)
                ->where('results.0.key', $this->teams[0]->getMorphClass() . ':' . $this->teams[0]->id));
    }

    public function test_league_objectives_count_only_the_entitys_objective(): void
    {
        $league = new \App\Models\League();
        $league->name = 'Novice';
        $league->competition = $this->comp->id;
        $league->save();
        $this->teams[0]->leagues()->attach($league->id);

        $this->serc->use_restricted_judges = true;
        $this->serc->save();
        $this->serc->getJudges()->first()->restrictedLeagues()->sync([$league->id]);

        $this->actingAs($this->organiser)->get($this->url())
            ->assertInertia(fn (Assert $page) => $page
                ->where('event.leagueObjectives', true)
                ->where('entities.0.possible', 2)
                // B's no League gives it no Objective
                ->where('entities.1.possible', null)
                ->where('marking.possible', 2));
    }

    public function test_setup_has_its_own_page_and_saves(): void
    {
        $objective = $this->serc->getJudges()->sole();

        $this->actingAs($this->organiser)->get($this->url('/setup'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Competition/SercSetup')
                ->where('setup.name', 'Dry SERC')
                ->has('setup.objectives', 1)
                ->has('setup.objectives.0.marking_points', 2)
                ->has('templates', 1));

        $this->actingAs($this->organiser)->from($this->url('/setup'))->post($this->url('/setup'), [
            'name' => 'Wet SERC',
            'type' => 'WET',
            'target_entity' => 'team',
            'judges' => [[
                'id' => $objective->id,
                'name' => 'Casualty 1',
                'hint' => '<p>Look</p>',
                'marking_points' => [[
                    'id' => $this->markingPoints[0]->id,
                    'description' => 'Checks for danger',
                    'weight' => 2,
                    'template_id' => $this->template->id,
                ]],
            ]],
            'deleted' => ['judges' => [], 'marking_points' => [$this->markingPoints[1]->id]],
        ])->assertRedirect($this->url('/setup'))->assertSessionHas('success');

        $this->assertSame('Wet SERC', $this->serc->fresh()->name);
        $this->assertSame('<p>Look</p>', $objective->fresh()->description);
        $this->assertEquals(2, $this->markingPoints[0]->fresh()->weight);
        $this->assertNull($this->markingPoints[1]->fresh());
    }

    public function test_a_new_serc_opens_once_added(): void
    {
        $this->actingAs($this->organiser)->get($this->appUrl('/comps/' . $this->comp->id . '/events/sercs/create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Competition/SercSetup')
                ->where('event', null)
                ->where('setup.objectives', []));

        $response = $this->actingAs($this->organiser)->post($this->appUrl('/comps/' . $this->comp->id . '/events/sercs'), [
            'name' => 'Wet SERC',
            'type' => 'WET',
            'target_entity' => 'club',
            'judges' => [[
                'id' => null,
                'name' => 'Casualty 1',
                'marking_points' => [['id' => null, 'description' => 'Swims', 'weight' => 1, 'template_id' => $this->template->id]],
            ]],
            'deleted' => ['judges' => [], 'marking_points' => []],
        ]);

        $serc = SERC::where('name', 'Wet SERC')->sole();
        $response->assertRedirect($this->appUrl('/comps/' . $this->comp->id . '/events/sercs/' . $serc->id));
        $this->assertSame(1, $serc->getMarkingPoints()->count());
    }

    public function test_it_can_be_hidden_from_results(): void
    {
        $this->actingAs($this->organiser)->from($this->url())->post($this->url('/visibility'), ['viewable' => false])
            ->assertRedirect($this->url());

        $this->assertFalse((bool) $this->serc->fresh()->viewable);
    }

    public function test_saving_scoring_sets_it_up(): void
    {
        $this->actingAs($this->organiser)->get($this->url('/settings'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Manage/Competition/SercSettings')->where('scoring.schema', null));

        $this->actingAs($this->organiser)->from($this->url('/settings'))->post($this->url('/scoring'), [
            'equation' => 'result',
            'global_variables' => [],
            'local_variables' => [],
            'auto_penalties' => [],
            'auto_disqualifications' => [],
            'rank_higher' => true,
            'allow_disqualified_to_rank' => false,
            'group_by' => [],
            'force_penalty' => false,
        ])->assertRedirect($this->url('/settings'))->assertSessionHas('success');

        $this->assertSame('result', $this->serc->fresh()->scoringSchema->schema['equation']);
    }

    public function test_deleting_it_needs_its_name(): void
    {
        $this->actingAs($this->organiser)->from($this->url('/settings'))->delete($this->url(), ['name' => 'Dry'])
            ->assertSessionHasErrors('name');
        $this->assertNotNull($this->serc->fresh());

        $this->actingAs($this->organiser)->delete($this->url(), ['name' => 'Dry SERC'])
            ->assertRedirect($this->appUrl('/comps/' . $this->comp->id . '/events'));
        $this->assertNull($this->serc->fresh());
    }

    public function test_old_pages_redirect_to_their_new_ones(): void
    {
        $this->actingAs($this->organiser)->get($this->url('/edit'))->assertRedirect($this->url('/setup'));
        $this->actingAs($this->organiser)->get($this->url('/scoring-settings'))->assertRedirect($this->url('/settings') . '#scoring');
        $this->actingAs($this->organiser)->get($this->appUrl('/comps/' . $this->comp->id . '/events/sercs/add'))
            ->assertRedirect($this->appUrl('/comps/' . $this->comp->id . '/events/sercs/create'));
    }

    public function test_an_entitys_marks_have_their_own_page(): void
    {
        [$a] = $this->teams;
        $this->mark($a, $this->markingPoints[0], 7);

        $this->actingAs($this->organiser)->get($this->url('/results/' . $a->id))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Competition/SercMarks')
                ->where('entity.id', $a->id)
                ->where('entity.index', 1)
                ->where('entity.of', 2)
                ->where('disqualification', '')
                ->has('objectives', 1)
                ->where('objectives.0.markingPoints.0.mark', 7)
                ->where('objectives.0.markingPoints.1.mark', null)
                ->where('nextUrl', $this->url('/results/' . $this->teams[1]->id)));
    }

    public function test_saving_marks_opens_the_next_entity_and_logs_the_changes(): void
    {
        [$a, $b] = $this->teams;
        $this->mark($a, $this->markingPoints[1], 4);

        $this->actingAs($this->organiser)->post($this->url('/results/' . $a->id), [
            'disqualification' => 'dq101',
            'penalties' => 'P100, p200',
            'marks' => [$this->markingPoints[0]->id => 8, $this->markingPoints[1]->id => null],
        ])->assertRedirect($this->url('/results/' . $b->id))->assertSessionHas('success');

        $this->assertEquals(8, SERCResult::where('marking_point', $this->markingPoints[0]->id)->forEntity($a)->sole()->result);
        // A blank mark takes it away
        $this->assertNull(SERCResult::where('marking_point', $this->markingPoints[1]->id)->forEntity($a)->first());
        $this->assertSame('DQ101', (string) $this->serc->getEntityDisqualifications($a)->sole());
        $this->assertSame(['P100', 'P200'], $this->serc->getEntityPenalties($a)->get()->map(fn ($p) => (string) $p)->all());
        $this->assertSame(1, \App\Models\Activity\Activity::where('activity', 'SERC_RESULT_UPDATED')->count());

        // Save goes back to the SERC
        $this->actingAs($this->organiser)->post($this->url('/results/' . $a->id), ['marks' => [], 'next' => false])->assertRedirect($this->url());

        // So does the last one
        $this->actingAs($this->organiser)->post($this->url('/results/' . $b->id), ['marks' => []])->assertRedirect($this->url());
    }

    public function test_marks_and_codes_are_checked(): void
    {
        $a = $this->teams[0];

        $this->actingAs($this->organiser)->from($this->url('/results/' . $a->id))->post($this->url('/results/' . $a->id), [
            'disqualification' => 'D1',
            'penalties' => 'P1,,',
            'marks' => [$this->markingPoints[0]->id => 11],
        ])->assertSessionHasErrors(['disqualification', 'penalties', 'marks.' . $this->markingPoints[0]->id]);

        $this->assertSame(0, SERCResult::count());
    }

    public function test_another_sercs_marking_points_cant_be_marked_through_this_one(): void
    {
        $other = new SERC();
        $other->name = 'Wet SERC';
        $other->competition = $this->comp->id;
        $other->save();

        $objective = new SERCJudge();
        $objective->name = 'Casualty';
        $objective->description = '';
        $objective->serc = $other->id;
        $objective->save();

        $mp = new SERCMarkingPoint();
        $mp->name = 'Swims';
        $mp->weight = 1;
        $mp->judge = $objective->id;
        $mp->serc = $other->id;
        $mp->save();

        $this->actingAs($this->organiser)->post($this->url('/results/' . $this->teams[0]->id), ['marks' => [$mp->id => 5]])->assertNotFound();
        $this->assertSame(0, SERCResult::count());
    }

    public function test_mark_splits_show_one_objectives_marks(): void
    {
        $this->mark($this->teams[1], $this->markingPoints[0], 6);
        $objective = $this->serc->getJudges()->sole();

        $this->actingAs($this->organiser)->get($this->url('/mark-splits'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Competition/SercMarkSplits')
                ->has('objectives', 1)
                ->where('splits', null));

        $this->actingAs($this->organiser)->get($this->url('/mark-splits?objective=' . $objective->id))
            ->assertInertia(fn (Assert $page) => $page
                ->where('objective', $objective->id)
                ->has('splits.markingPoints', 2)
                ->has('splits.rows', 2)
                ->where('splits.rows.1.marks.' . $this->markingPoints[0]->id, 6));
    }

    public function test_old_marking_pages_redirect(): void
    {
        $this->actingAs($this->organiser)->get($this->url('/results/' . $this->teams[0]->id . '/edit'))
            ->assertRedirect($this->url('/results/' . $this->teams[0]->id));
        $this->actingAs($this->organiser)->get($this->url('/restriction-map'))->assertRedirect($this->url('/league-objectives'));
    }

    public function test_a_serc_writer_sees_results_and_edits_setup_only(): void
    {
        $writer = $this->writer();

        $this->actingAs($writer)->get($this->url())
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('event.canManage', false)->where('sheetsUrl', null));
        $this->actingAs($writer)->get($this->url('/setup'))->assertOk();

        $this->actingAs($writer)->get($this->url('/settings'))->assertForbidden();
        $this->actingAs($writer)->post($this->url('/visibility'), ['viewable' => false])->assertForbidden();
        $this->actingAs($writer)->delete($this->url(), ['name' => 'Dry SERC'])->assertForbidden();
        $this->actingAs($writer)->get($this->url('/results/' . $this->teams[0]->id))->assertForbidden();
        $this->actingAs($writer)->get($this->url('/mark-splits'))->assertForbidden();
    }
}
