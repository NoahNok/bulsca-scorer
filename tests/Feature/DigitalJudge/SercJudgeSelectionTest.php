<?php

namespace Tests\Feature\DigitalJudge;

use App\Models\Competition;
use App\Models\SERC;
use App\Models\SERCJudge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class SercJudgeSelectionTest extends TestCase
{
    use RefreshDatabase;

    private Competition $comp;
    private SERC $sercA;
    private SERC $sercB;
    private SERCJudge $judgeA;
    private SERCJudge $judgeB;
    private User $official;

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
        $this->comp->save();

        [$this->sercA, $this->judgeA] = $this->sercWithJudge('Dry SERC');
        [$this->sercB, $this->judgeB] = $this->sercWithJudge('Wet SERC');

        $this->official = User::factory()->create(['judge_only' => true]);
        $this->comp->addOfficial($this->official);
    }

    private function sercWithJudge(string $name): array
    {
        $serc = new SERC();
        $serc->name = $name;
        $serc->competition = $this->comp->id;
        $serc->digitalJudgeEnabled = true;
        $serc->save();

        $judge = new SERCJudge();
        $judge->name = "$name casualty";
        $judge->description = '';
        $judge->serc = $serc->id;
        $judge->save();

        return [$serc, $judge];
    }

    private function params(SERC $serc, array $extra = []): array
    {
        return ['competition' => $this->comp->getSlug(), 'serc' => $serc, ...$extra];
    }

    private function confirm(SERC $serc, SERCJudge $judge)
    {
        return $this->actingAs($this->official)
            ->post(route('judge.competition.serc.confirm.post', $this->params($serc)), ['judge' => $judge->id]);
    }

    public function test_confirming_a_judge_on_another_serc_starts_a_fresh_selection(): void
    {
        $this->confirm($this->sercA, $this->judgeA);
        $this->confirm($this->sercB, $this->judgeB);

        $this->get(route('judge.competition.serc', $this->params($this->sercB)))
            ->assertOk()
            ->assertInertia(fn(AssertableInertia $page) => $page
                ->has('judges', 1)
                ->where('judges.0.id', $this->judgeB->id));
    }

    public function test_confirming_the_same_judge_twice_selects_it_once(): void
    {
        $this->confirm($this->sercA, $this->judgeA);
        $this->confirm($this->sercA, $this->judgeA);

        $this->get(route('judge.competition.serc', $this->params($this->sercA)))
            ->assertInertia(fn(AssertableInertia $page) => $page->has('judges', 1));
    }

    public function test_opening_another_sercs_pages_with_judges_selected_goes_back_to_competition_home(): void
    {
        $this->confirm($this->sercA, $this->judgeA);

        $home = route('judge.competition', ['competition' => $this->comp->getSlug()]);

        $this->get(route('judge.competition.serc', $this->params($this->sercB)))->assertRedirect($home);
        $this->get(route('judge.competition.serc.mark.next', $this->params($this->sercB)))->assertRedirect($home);
    }

    public function test_cannot_attach_a_judge_from_another_serc(): void
    {
        $this->confirm($this->sercA, $this->judgeA);

        $this->get(route('judge.competition.serc.attach-judge', $this->params($this->sercA, ['judge' => $this->judgeB])))
            ->assertNotFound();

        $this->get(route('judge.competition.serc', $this->params($this->sercA)))
            ->assertInertia(fn(AssertableInertia $page) => $page
                ->has('judges', 1)
                ->where('judges.0.id', $this->judgeA->id));
    }
}
