<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\Activity\Activity;
use App\Models\CompetitionTeam;
use App\Models\League;
use App\Models\SERC;
use App\Models\SERC\MarkingPointTemplate;
use App\Models\SERCJudge;
use App\Models\SERCMarkingPoint;
use App\Models\SERCResult;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SercSetupTest extends TestCase
{
    use RefreshDatabase;

    private Competition $comp;
    private SERC $serc;
    private User $admin;
    private MarkingPointTemplate $template;

    // another organiser's competition
    private SERCJudge $otherJudge;
    private SERCMarkingPoint $otherMarkingPoint;

    protected function setUp(): void
    {
        parent::setUp();

        $this->template = MarkingPointTemplate::create(['name' => 'Default', 'settings' => []]);

        [$this->comp, $this->serc] = $this->competitionWithSerc('Our Competition');
        [, $otherSerc] = $this->competitionWithSerc('Their Competition');

        $this->otherJudge = new SERCJudge();
        $this->otherJudge->name = 'Their judge';
        $this->otherJudge->description = '';
        $this->otherJudge->serc = $otherSerc->id;
        $this->otherJudge->save();

        $this->otherMarkingPoint = new SERCMarkingPoint();
        $this->otherMarkingPoint->name = 'Their marking point';
        $this->otherMarkingPoint->weight = 1;
        $this->otherMarkingPoint->judge = $this->otherJudge->id;
        $this->otherMarkingPoint->serc = $otherSerc->id;
        $this->otherMarkingPoint->save();

        $this->admin = User::factory()->create();
        $this->comp->addAccount($this->admin, 'admin');
    }

    private function competitionWithSerc(string $name): array
    {
        $comp = new Competition();
        $comp->name = $name;
        $comp->when = now()->addWeek();
        $comp->where = 'Test Pool';
        $comp->max_lanes = 8;
        $comp->anytimepin = false;
        $comp->seed_per_event = false;
        $comp->heats_per_event = false;
        $comp->use_seeds = true;
        $comp->save();

        $serc = new SERC();
        $serc->name = 'Dry SERC';
        $serc->competition = $comp->id;
        $serc->save();

        return [$comp, $serc];
    }

    private function payload(array $judges, array $deleted = ['judges' => [], 'marking_points' => []], array $extra = []): array
    {
        return [
            'name' => 'Dry SERC',
            'type' => 'DRY',
            'target_entity' => 'team',
            'judges' => $judges,
            'deleted' => $deleted,
            ...$extra,
        ];
    }

    private function judge(?int $id = null, ?int $markingPointId = null, string $name = 'Casualty 1', ?array $leagues = null): array
    {
        return [
            'id' => $id,
            'name' => $name,
            ...($leagues === null ? [] : ['restricted_leagues' => $leagues]),
            'hint' => null,
            'marking_points' => [[
                'id' => $markingPointId,
                'description' => 'Checks for danger',
                'weight' => 1,
                'template_id' => $this->template->id,
            ]],
        ];
    }

    private function editUrl(SERC $serc): string
    {
        return route('manage.competitions.events.sercs.setup.save', [$this->comp, $serc]);
    }

    private function assertOtherSercUntouched(): void
    {
        $this->assertSame('Their judge', $this->otherJudge->fresh()?->name);
        $this->assertSame($this->otherJudge->serc, $this->otherJudge->fresh()->serc);
        $this->assertSame('Their marking point', $this->otherMarkingPoint->fresh()?->name);
        $this->assertSame($this->otherMarkingPoint->serc, $this->otherMarkingPoint->fresh()->serc);
    }

    public function test_adding_and_editing_a_serc_still_works(): void
    {
        $this->actingAs($this->admin)
            ->postJson(route('manage.competitions.events.sercs.store', $this->comp), $this->payload([$this->judge()]))
            ->assertRedirect();

        $serc = SERC::where('competition', $this->comp->id)->latest('id')->first();
        $judge = $serc->getJudges()->sole();
        $markingPoint = $judge->getMarkingPoints()->sole();

        $this->actingAs($this->admin)
            ->postJson($this->editUrl($serc), $this->payload([$this->judge($judge->id, $markingPoint->id)]))
            ->assertRedirect();

        $this->assertSame($serc->id, $judge->fresh()->serc);
        $this->assertSame($serc->id, $markingPoint->fresh()->serc);
    }

    public function test_cannot_take_over_another_competitions_judges_or_marking_points(): void
    {
        $this->actingAs($this->admin)
            ->postJson($this->editUrl($this->serc), $this->payload([$this->judge($this->otherJudge->id)]))
            ->assertStatus(422);

        $this->actingAs($this->admin)
            ->postJson($this->editUrl($this->serc), $this->payload([$this->judge(null, $this->otherMarkingPoint->id)]))
            ->assertStatus(422);

        $this->actingAs($this->admin)
            ->postJson(route('manage.competitions.events.sercs.store', $this->comp), $this->payload([$this->judge($this->otherJudge->id)]))
            ->assertStatus(422);

        $this->assertOtherSercUntouched();
    }

    public function test_cannot_delete_another_competitions_judges_or_marking_points(): void
    {
        $this->actingAs($this->admin)
            ->postJson($this->editUrl($this->serc), $this->payload([$this->judge()], [
                'judges' => [$this->otherJudge->id],
                'marking_points' => [$this->otherMarkingPoint->id],
            ]))
            ->assertStatus(422);

        $this->assertOtherSercUntouched();
    }

    public function test_cannot_edit_another_competitions_serc(): void
    {
        $otherSerc = SERC::find($this->otherJudge->serc);

        $this->actingAs($this->admin)
            ->postJson($this->editUrl($otherSerc), $this->payload([$this->judge()]))
            ->assertNotFound();

        $this->assertSame('Their Competition', $otherSerc->getCompetition->name);
        $this->assertSame(1, $otherSerc->getJudges()->count());
    }

    private function league(Competition $comp, string $name): League
    {
        $league = new League();
        $league->name = $name;
        $league->competition = $comp->id;
        $league->save();

        return $league;
    }

    public function test_league_objectives_are_saved_when_creating_and_editing_a_serc(): void
    {
        $novice = $this->league($this->comp, 'Novice');
        $open = $this->league($this->comp, 'Open');

        $this->actingAs($this->admin)
            ->postJson(route('manage.competitions.events.sercs.store', $this->comp), $this->payload([
                $this->judge(name: 'Casualty 1', leagues: [$novice->id]),
                $this->judge(name: 'Casualty 2', leagues: [$open->id]),
            ], extra: ['use_restricted_judges' => true]))
            ->assertRedirect();

        $serc = SERC::where('competition', $this->comp->id)->latest('id')->first();
        $this->assertTrue((bool) $serc->use_restricted_judges);
        $this->assertEquals([$novice->id], $serc->getJudges()->where('name', 'Casualty 1')->sole()->restrictedLeagues->pluck('id')->all());
        $this->assertEquals([$open->id], $serc->getJudges()->where('name', 'Casualty 2')->sole()->restrictedLeagues->pluck('id')->all());

        $first = $serc->getJudges()->where('name', 'Casualty 1')->sole();
        $this->actingAs($this->admin)
            ->postJson($this->editUrl($serc), $this->payload([
                $this->judge($first->id, $first->getMarkingPoints()->sole()->id, 'Casualty 1', [$novice->id, $open->id]),
            ], ['judges' => [$serc->getJudges()->where('name', 'Casualty 2')->sole()->id], 'marking_points' => []], ['use_restricted_judges' => false]))
            ->assertRedirect();

        $this->actingAs($this->admin)
            ->get(route('manage.competitions.events.sercs.setup', [$this->comp, $serc]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Competition/SercSetup')
                ->where('setup.leagueObjectives', false)
                ->where('leagues.0.name', 'Novice'));

        $this->assertFalse((bool) $serc->fresh()->use_restricted_judges);
        $this->assertEqualsCanonicalizing([$novice->id, $open->id], $first->fresh()->restrictedLeagues->pluck('id')->all());
    }

    public function test_a_league_cannot_be_on_two_objectives(): void
    {
        $novice = $this->league($this->comp, 'Novice');

        $this->actingAs($this->admin)
            ->postJson($this->editUrl($this->serc), $this->payload([
                $this->judge(name: 'Casualty 1', leagues: [$novice->id]),
                $this->judge(name: 'Casualty 2', leagues: [$novice->id]),
            ], extra: ['use_restricted_judges' => true]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('judges');

        $this->assertSame(0, $this->serc->getJudges()->count());
    }

    public function test_cannot_assign_another_competitions_league(): void
    {
        $theirLeague = $this->league(SERC::find($this->otherJudge->serc)->getCompetition, 'Their league');

        $this->actingAs($this->admin)
            ->postJson($this->editUrl($this->serc), $this->payload([
                $this->judge(leagues: [$theirLeague->id]),
            ], extra: ['use_restricted_judges' => true]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('judges.0.restricted_leagues.0');
    }

    public function test_changing_league_objectives_after_marking_needs_confirming_and_is_recorded(): void
    {
        $novice = $this->league($this->comp, 'Novice');

        $this->actingAs($this->admin)
            ->postJson($this->editUrl($this->serc), $this->payload([$this->judge(name: 'Casualty 1', leagues: [$novice->id])], extra: ['use_restricted_judges' => true]))
            ->assertRedirect();

        $objective = $this->serc->getJudges()->sole();
        $markingPoint = $objective->getMarkingPoints()->sole();

        $team = new CompetitionTeam();
        $team->competition = $this->comp->id;
        $team->team = 'A';
        $team->save();

        $mark = new SERCResult();
        $mark->marking_point = $markingPoint->id;
        $mark->entity()->associate($team);
        $mark->result = 5;
        $mark->save();

        // move Novice from Casualty 1 to a new Casualty 2
        $moveNovice = $this->payload([
            $this->judge($objective->id, $markingPoint->id, 'Casualty 1', []),
            $this->judge(name: 'Casualty 2', leagues: [$novice->id]),
        ], extra: ['use_restricted_judges' => true]);

        $this->actingAs($this->admin)
            ->postJson($this->editUrl($this->serc), $moveNovice)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['confirm_affected_marks' => '1 mark has already been entered']);

        $this->assertEquals([$novice->id], $objective->fresh()->restrictedLeagues->pluck('id')->all());
        $this->assertSame(1, $this->serc->getJudges()->count());

        $this->actingAs($this->admin)
            ->postJson($this->editUrl($this->serc), [...$moveNovice, 'confirm_affected_marks' => true])
            ->assertRedirect();

        $this->assertEquals([], $objective->fresh()->restrictedLeagues->pluck('id')->all());
        $this->assertSame(1, Activity::where('activity', 'SERC_LEAGUE_OBJECTIVES_CHANGED')->count());

        // switching League Objectives off also affects that Mark
        $this->actingAs($this->admin)
            ->postJson($this->editUrl($this->serc), [...$moveNovice, 'use_restricted_judges' => false, 'judges' => [
                $this->judge($objective->id, $markingPoint->id, 'Casualty 1', []),
                $this->judge($this->serc->getJudges()->where('name', 'Casualty 2')->sole()->id, $this->serc->getJudges()->where('name', 'Casualty 2')->sole()->getMarkingPoints()->sole()->id, 'Casualty 2', [$novice->id]),
            ]])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['confirm_affected_marks' => '1 mark has already been entered']);
    }

    private function teamIn(string $letter, array $leagues): CompetitionTeam
    {
        $team = new CompetitionTeam();
        $team->competition = $this->comp->id;
        $team->team = $letter;
        $team->save();
        $team->leagues()->attach(collect($leagues)->pluck('id'));

        return $team;
    }

    public function test_restriction_map_shows_each_entitys_objective_and_flags_problems(): void
    {
        $novice = $this->league($this->comp, 'Novice');
        $open = $this->league($this->comp, 'Open');
        $masters = $this->league($this->comp, 'Masters');

        $this->serc->scorable_entity = 'team';
        $this->serc->save();

        $this->actingAs($this->admin)
            ->postJson($this->editUrl($this->serc), $this->payload([
                $this->judge(name: 'Casualty 1', leagues: [$novice->id]),
                $this->judge(name: 'Casualty 2', leagues: [$open->id]),
                $this->judge(name: 'Casualty 3', leagues: []),
            ], extra: ['use_restricted_judges' => true]))
            ->assertRedirect();

        $noviceTeam = $this->teamIn('A', [$novice]);
        $noLeague = $this->teamIn('B', []);
        $twoObjectives = $this->teamIn('C', [$novice, $open]);
        $unassignedLeague = $this->teamIn('D', [$masters]);

        $this->actingAs($this->admin)
            ->get(route('comps.events.sercs.restriction-map', [$this->comp, $this->serc]))
            ->assertOk()
            ->assertViewHas('restrictionMap', function ($map) use ($noviceTeam, $noLeague, $twoObjectives, $unassignedLeague) {
                $byEntity = collect($map)->keyBy('entity_name');

                return $byEntity[$noviceTeam->getName()]['objective'] === 'Casualty 1' && $byEntity[$noviceTeam->getName()]['problem'] === null
                    && $byEntity[$noLeague->getName()]['objective'] === null && $byEntity[$noLeague->getName()]['problem'] === 'no_objective'
                    && $byEntity[$twoObjectives->getName()]['objective'] === null && $byEntity[$twoObjectives->getName()]['problem'] === 'several_objectives'
                    && $byEntity[$unassignedLeague->getName()]['objective'] === null && $byEntity[$unassignedLeague->getName()]['problem'] === 'no_objective';
            });
    }
}
