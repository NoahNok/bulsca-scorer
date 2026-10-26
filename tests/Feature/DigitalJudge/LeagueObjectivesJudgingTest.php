<?php

namespace Tests\Feature\DigitalJudge;

use App\Models\Competition;
use App\Models\DigitalJudge\JudgeNote;
use App\Models\DigitalJudge\OverallJudgeNote;
use App\Models\CompetitionTeam;
use App\Models\League;
use App\Models\Orders\Draw;
use App\Models\SERC;
use App\Models\SERCJudge;
use App\Models\SERCMarkingPoint;
use App\Models\SERCResult;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class LeagueObjectivesJudgingTest extends TestCase
{
    use RefreshDatabase;

    private Competition $comp;
    private SERC $serc;
    private User $official;
    private User $referee;

    private League $novice;
    private League $open;

    private SERCJudge $noviceObjective;
    private SERCMarkingPoint $noviceMarkingPoint;
    private SERCJudge $openObjective;
    private SERCMarkingPoint $openMarkingPoint;

    private CompetitionTeam $noviceTeam;
    private CompetitionTeam $openTeam;

    protected function setUp(): void
    {
        parent::setUp();

        $this->comp = new Competition();
        $this->comp->name = 'Test Competition';
        $this->comp->when = now()->addWeek();
        $this->comp->where = 'Test Pool';
        $this->comp->max_lanes = 8;
        $this->comp->anytimepin = false;
        $this->comp->seed_per_event = false;
        $this->comp->heats_per_event = false;
        $this->comp->use_seeds = true;
        $this->comp->sercs_released_at = now();
        $this->comp->show_teams_to_judges = true;
        $this->comp->save();

        $this->novice = $this->league('Novice');
        $this->open = $this->league('Open');

        $this->serc = new SERC();
        $this->serc->name = 'Dry SERC';
        $this->serc->competition = $this->comp->id;
        $this->serc->digitalJudgeEnabled = true;
        $this->serc->scorable_entity = 'team';
        $this->serc->use_restricted_judges = true;
        $this->serc->save();

        [$this->noviceObjective, $this->noviceMarkingPoint] = $this->objective('Casualty 1', [$this->novice]);
        [$this->openObjective, $this->openMarkingPoint] = $this->objective('Casualty 2', [$this->open]);

        $this->noviceTeam = $this->team('A', [$this->novice], 1);
        $this->openTeam = $this->team('B', [$this->open], 2);

        $this->official = User::factory()->create(['judge_only' => true]);
        $this->comp->addOfficial($this->official);

        $this->referee = User::factory()->create(['judge_only' => true]);
        $this->comp->addOfficial($this->referee, 'referee');
    }

    private function league(string $name): League
    {
        $league = new League();
        $league->name = $name;
        $league->competition = $this->comp->id;
        $league->save();

        return $league;
    }

    private function objective(string $name, array $leagues): array
    {
        $objective = new SERCJudge();
        $objective->name = $name;
        $objective->description = '';
        $objective->serc = $this->serc->id;
        $objective->save();
        $objective->restrictedLeagues()->attach(collect($leagues)->pluck('id'));

        $mp = new SERCMarkingPoint();
        $mp->name = "$name approach";
        $mp->weight = 1;
        $mp->judge = $objective->id;
        $mp->serc = $this->serc->id;
        $mp->save();

        return [$objective, $mp];
    }

    private function team(string $letter, array $leagues, int $draw): CompetitionTeam
    {
        $team = new CompetitionTeam();
        $team->competition = $this->comp->id;
        $team->team = $letter;
        $team->save();
        $team->leagues()->attach(collect($leagues)->pluck('id'));

        $allocation = new Draw();
        $allocation->serc = $this->serc->id;
        $allocation->entity()->associate($team);
        $allocation->tank = 1;
        $allocation->draw = $draw;
        $allocation->save();

        return $team;
    }

    private function params(array $extra = []): array
    {
        return ['competition' => $this->comp->getSlug(), 'serc' => $this->serc, ...$extra];
    }

    private function startJudging(User $as)
    {
        return $this->actingAs($as)->post(route('judge.competition.serc.confirm.post', $this->params()));
    }

    public function test_officials_start_judging_without_picking_an_objective(): void
    {
        $this->actingAs($this->official)
            ->get(route('judge.competition', ['competition' => $this->comp->getSlug()]))
            ->assertInertia(fn(AssertableInertia $page) => $page->where('sercs.0.league_objectives', true));

        $this->startJudging($this->official)->assertSessionHasNoErrors()->assertRedirect();

        $this->get(route('judge.competition.serc', $this->params()))
            ->assertOk()
            ->assertInertia(fn(AssertableInertia $page) => $page->where('league_objectives', true));
    }

    private function markUrl(CompetitionTeam $team): string
    {
        return route('judge.competition.serc.mark.entity', $this->params(['entity_id' => $team->id]));
    }

    public function test_each_entity_is_marked_against_its_leagues_objective(): void
    {
        $this->startJudging($this->official);

        $this->get($this->markUrl($this->noviceTeam))
            ->assertOk()
            ->assertInertia(fn(AssertableInertia $page) => $page
                ->component('Judge/Competition/SERC/JudgeEntity')
                ->has('judges', 1)
                ->where('judges.0.id', $this->noviceObjective->id)
                ->where('league_objective', ['objective' => 'Casualty 1', 'league' => 'Novice']));

        $this->get($this->markUrl($this->openTeam))
            ->assertInertia(fn(AssertableInertia $page) => $page
                ->has('judges', 1)
                ->where('judges.0.id', $this->openObjective->id)
                ->where('league_objective', ['objective' => 'Casualty 2', 'league' => 'Open']));
    }

    public function test_entities_without_a_single_objective_ask_for_the_organiser(): void
    {
        $noLeague = $this->team('C', [], 3);
        $bothLeagues = $this->team('D', [$this->novice, $this->open], 4);

        $this->startJudging($this->official);

        $this->get($this->markUrl($noLeague))
            ->assertOk()
            ->assertInertia(fn(AssertableInertia $page) => $page
                ->component('Judge/Competition/SERC/NoLeagueObjective')
                ->where('reason', 'no_objective')
                ->where('leagues', []));

        $this->get($this->markUrl($bothLeagues))
            ->assertOk()
            ->assertInertia(fn(AssertableInertia $page) => $page
                ->component('Judge/Competition/SERC/NoLeagueObjective')
                ->where('reason', 'several_objectives')
                ->where('leagues', ['Novice', 'Open']));
    }

    private function mark(SERCMarkingPoint $mp, CompetitionTeam $team, int $result = 5): void
    {
        $mark = new SERCResult();
        $mark->marking_point = $mp->id;
        $mark->entity()->associate($team);
        $mark->result = $result;
        $mark->save();
    }

    public function test_next_skips_entities_marked_on_any_objective_and_ones_without_an_objective(): void
    {
        $noLeague = $this->team('C', [], 3);
        $later = $this->team('D', [$this->novice], 4);

        $this->mark($this->noviceMarkingPoint, $this->noviceTeam);

        $this->startJudging($this->official);
        $next = route('judge.competition.serc.mark.next', $this->params());

        $this->get($next)->assertRedirect(route('judge.competition.serc.mark.entity', ['competition' => $this->comp, 'serc' => $this->serc, 'entity_id' => $this->openTeam->id]));

        $this->mark($this->openMarkingPoint, $this->openTeam);

        $this->get($next)->assertRedirect(route('judge.competition.serc.mark.entity', ['competition' => $this->comp, 'serc' => $this->serc, 'entity_id' => $later->id]));
    }

    private function storeMarks(CompetitionTeam $team, SERCJudge $objective, SERCMarkingPoint $mp, int $result, array $extra = [])
    {
        return $this->postJson(route('judge.competition.serc.mark.store', $this->params(['entity_id' => $team->id])), [
            'marks' => [$objective->id => [$mp->id => $result]],
            ...$extra,
        ]);
    }

    private function markFor(SERCMarkingPoint $mp, CompetitionTeam $team)
    {
        return SERCResult::where('marking_point', $mp->id)->whereMorphedTo('entity', $team)->value('result');
    }

    public function test_a_marked_entity_is_closed_to_officials_but_open_to_referees(): void
    {
        $this->mark($this->noviceMarkingPoint, $this->noviceTeam, 5);

        $this->startJudging($this->official);
        $this->get($this->markUrl($this->noviceTeam))->assertRedirect();
        $this->storeMarks($this->noviceTeam, $this->noviceObjective, $this->noviceMarkingPoint, 1)->assertStatus(409);

        $this->startJudging($this->referee);
        $this->get($this->markUrl($this->noviceTeam))->assertOk();
        $this->storeMarks($this->noviceTeam, $this->noviceObjective, $this->noviceMarkingPoint, 8)->assertOk();

        $this->assertEquals(8, $this->markFor($this->noviceMarkingPoint, $this->noviceTeam));
    }

    public function test_marks_for_another_leagues_objective_are_rejected(): void
    {
        $this->startJudging($this->official);

        $this->storeMarks($this->noviceTeam, $this->openObjective, $this->openMarkingPoint, 5)->assertStatus(422);
        $this->postJson(route('judge.competition.serc.mark.store', $this->params(['entity_id' => $this->noviceTeam->id])), [
            'notes' => [$this->openObjective->id => 'Wrong scoresheet'],
        ])->assertStatus(422);

        $this->assertNull($this->markFor($this->openMarkingPoint, $this->noviceTeam));

        $this->storeMarks($this->noviceTeam, $this->noviceObjective, $this->noviceMarkingPoint, 5)->assertOk();
        $this->assertEquals(5, $this->markFor($this->noviceMarkingPoint, $this->noviceTeam));
    }

    private function storeOverallNote(string $note)
    {
        return $this->post(route('judge.competition.serc.overall-notes.store', $this->params()), ['note' => $note]);
    }

    public function test_overall_notes_go_to_each_objective_marked_in_the_session(): void
    {
        $this->startJudging($this->official);
        $this->storeMarks($this->noviceTeam, $this->noviceObjective, $this->noviceMarkingPoint, 5)->assertOk();
        $this->storeOverallNote('Novice went well')->assertOk();

        $this->assertSame('Novice went well', OverallJudgeNote::where('judge', $this->noviceObjective->id)->value('note'));
        $this->assertNull(OverallJudgeNote::where('judge', $this->openObjective->id)->value('note'));

        $this->storeMarks($this->openTeam, $this->openObjective, $this->openMarkingPoint, 5)->assertOk();
        $this->storeOverallNote('Both went well')->assertOk();

        $this->assertSame('Both went well', OverallJudgeNote::where('judge', $this->noviceObjective->id)->value('note'));
        $this->assertSame('Both went well', OverallJudgeNote::where('judge', $this->openObjective->id)->value('note'));
    }

    public function test_legacy_judging_shows_the_organiser_error_for_an_entity_without_a_league(): void
    {
        $noLeague = $this->team('C', [], 3);

        $this->comp->digitalJudgeEnabled = true;
        $this->comp->save();

        $this->withoutVite()->withSession([
            'digitalJudgeClientAllowed' => true,
            'digitalJudgeClientComp' => $this->comp->id,
            'digitalJudgeJudgeId' => [$this->noviceObjective->id],
        ])
            ->get(route('dj.judging.judge-team', ['entity_id' => $noLeague->id]))
            ->assertOk()
            ->assertViewIs('digitaljudge.errors.500')
            ->assertSee('Please have your organiser correct this');
    }

    public function test_legacy_judging_lists_previous_notes_by_draw_when_teams_are_hidden(): void
    {
        $this->comp->digitalJudgeEnabled = true;
        $this->comp->show_teams_to_judges = false;
        $this->comp->save();

        $note = new JudgeNote();
        $note->judge = $this->noviceObjective->id;
        $note->entity()->associate($this->noviceTeam);
        $note->note = 'Missed the airway check';
        $note->save();

        $this->withoutVite()->withSession([
            'digitalJudgeClientAllowed' => true,
            'digitalJudgeClientComp' => $this->comp->id,
            'digitalJudgeJudgeId' => [$this->noviceObjective->id],
        ])
            ->get(route('dj.judging.judge-team', ['entity_id' => $this->noviceTeam->id]))
            ->assertOk()
            ->assertViewIs('digitaljudge.judging.judge-team')
            ->assertSee('1 / 2')
            ->assertSee('Missed the airway check');
    }
}
