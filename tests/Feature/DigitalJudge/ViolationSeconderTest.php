<?php

namespace Tests\Feature\DigitalJudge;

use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\DigitalJudge\Violation\ViolationSubmission;
use App\Models\DQCode;
use App\Models\SERC;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ViolationSeconderTest extends TestCase
{
    use RefreshDatabase;

    private Competition $comp;
    private SERC $serc;
    private CompetitionTeam $team;
    private DQCode $dq;
    private User $judge;
    private User $seconder;

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

        $this->serc = new SERC();
        $this->serc->name = 'Dry SERC';
        $this->serc->competition = $this->comp->id;
        $this->serc->digitalJudgeEnabled = true;
        $this->serc->save();

        $this->team = new CompetitionTeam();
        $this->team->competition = $this->comp->id;
        $this->team->team = 'A';
        $this->team->save();

        $this->dq = new DQCode();
        $this->dq->code = 100;
        $this->dq->description = 'Test DQ';
        $this->dq->save();

        $this->judge = User::factory()->create(['judge_only' => true, 'name' => 'Judge Person']);
        $this->comp->addOfficial($this->judge);

        $this->seconder = User::factory()->create(['judge_only' => true, 'name' => 'Seconder Person']);
        $this->comp->addOfficial($this->seconder);
    }

    private function submit(array $seconder)
    {
        return $this->actingAs($this->judge)->post(
            route('judge.competition.violation.submit', ['competition' => $this->comp->getSlug()]),
            [
                'entity_id' => $this->team->id,
                'event' => ['id' => $this->serc->id, 'type' => 'serc'],
                'violation' => ['id' => $this->dq->id, 'vtype' => 'DQ'],
                'details' => ['details' => 'Saw it'],
                'submitter' => ['position' => 'SERC judge'],
                'seconder' => $seconder,
            ]
        );
    }

    public function test_issue_page_lists_the_other_officials(): void
    {
        // Has an account but isn't an official on this competition
        User::factory()->create(['judge_only' => true]);

        $this->actingAs($this->judge)
            ->get(route('judge.competition.violation.issue', ['competition' => $this->comp->getSlug()]))
            ->assertOk()
            ->assertInertia(fn(AssertableInertia $page) => $page
                ->has('officials', 1)
                ->where('officials.0.id', $this->seconder->id)
                ->where('officials.0.name', 'Seconder Person'));
    }

    public function test_an_official_can_be_picked_as_the_seconder(): void
    {
        $this->submit(['user_id' => $this->seconder->id, 'position' => 'Turn judge'])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $submission = ViolationSubmission::sole();
        $this->assertSame($this->seconder->id, $submission->seconder_id);
        $this->assertSame('Turn judge', $submission->seconder_position);
        $this->assertNull($submission->seconder_name);
        $this->assertTrue($submission->seconder->is($this->seconder));
    }

    public function test_the_seconder_is_optional(): void
    {
        $this->submit(['user_id' => null, 'position' => ''])
            ->assertSessionHasNoErrors();

        $this->assertNull(ViolationSubmission::sole()->seconder_id);
    }

    public function test_the_seconder_must_be_an_official_on_the_competition(): void
    {
        $outsider = User::factory()->create(['judge_only' => true]);

        $this->submit(['user_id' => $outsider->id])
            ->assertSessionHasErrors('seconder.user_id');

        $this->assertSame(0, ViolationSubmission::count());
    }

    public function test_judges_cannot_second_their_own_submission(): void
    {
        $this->submit(['user_id' => $this->judge->id])
            ->assertSessionHasErrors('seconder.user_id');

        $this->assertSame(0, ViolationSubmission::count());
    }
}
