<?php

namespace Tests\Feature\DigitalJudge;

use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\Orders\Draw;
use App\Models\SERC;
use App\Models\SERCJudge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class SercTeamNamesTest extends TestCase
{
    use RefreshDatabase;

    private Competition $comp;
    private SERC $serc;
    private SERCJudge $judge;
    private CompetitionTeam $team;

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
        $this->comp->show_teams_to_judges = false;
        $this->comp->save();

        $this->serc = new SERC();
        $this->serc->name = 'Dry SERC';
        $this->serc->competition = $this->comp->id;
        $this->serc->digitalJudgeEnabled = true;
        $this->serc->scorable_entity = 'team';
        $this->serc->save();

        $this->judge = new SERCJudge();
        $this->judge->name = 'Casualty 1';
        $this->judge->description = '';
        $this->judge->serc = $this->serc->id;
        $this->judge->save();

        $this->team = new CompetitionTeam();
        $this->team->competition = $this->comp->id;
        $this->team->team = 'A';
        $this->team->save();

        $draw = new Draw();
        $draw->serc = $this->serc->id;
        $draw->entity()->associate($this->team);
        $draw->tank = 1;
        $draw->draw = 3;
        $draw->save();
    }

    private function judgeAs(User $user): void
    {
        $this->actingAs($user)->post(
            route('judge.competition.serc.confirm.post', ['competition' => $this->comp->getSlug(), 'serc' => $this->serc]),
            ['judge' => $this->judge->id]
        );
    }

    private function homeUrl(): string
    {
        return route('judge.competition.serc', ['competition' => $this->comp->getSlug(), 'serc' => $this->serc]);
    }

    private function markUrl(): string
    {
        return route('judge.competition.serc.mark.entity', ['competition' => $this->comp->getSlug(), 'serc' => $this->serc, 'entity_id' => $this->team->id]);
    }

    public function test_judges_get_draw_positions_instead_of_team_names(): void
    {
        $official = User::factory()->create(['judge_only' => true]);
        $this->comp->addOfficial($official);
        $this->judgeAs($official);

        $this->get($this->homeUrl())
            ->assertOk()
            ->assertDontSee($this->team->getName(), false)
            ->assertInertia(fn(AssertableInertia $page) => $page->where('draws.0.entity.name', 'Draw 3'));

        $this->get($this->markUrl())
            ->assertOk()
            ->assertDontSee($this->team->getName(), false)
            ->assertInertia(fn(AssertableInertia $page) => $page->where('entity.name', 'Draw 3'));
    }

    public function test_referees_still_see_team_names(): void
    {
        $referee = User::factory()->create(['judge_only' => true]);
        $this->comp->addOfficial($referee, 'referee');
        $this->judgeAs($referee);

        $this->get($this->homeUrl())
            ->assertInertia(fn(AssertableInertia $page) => $page->where('draws.0.entity.name', $this->team->getName()));
    }

    public function test_judges_see_team_names_when_the_competition_allows_it(): void
    {
        $this->comp->show_teams_to_judges = true;
        $this->comp->save();

        $official = User::factory()->create(['judge_only' => true]);
        $this->comp->addOfficial($official);
        $this->judgeAs($official);

        $this->get($this->homeUrl())
            ->assertInertia(fn(AssertableInertia $page) => $page->where('draws.0.entity.name', $this->team->getName()));
    }
}
