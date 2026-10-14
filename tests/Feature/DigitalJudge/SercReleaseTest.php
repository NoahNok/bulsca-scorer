<?php

namespace Tests\Feature\DigitalJudge;

use App\Models\Competition;
use App\Models\SERC;
use App\Models\SERCJudge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class SercReleaseTest extends TestCase
{
    use RefreshDatabase;

    private Competition $comp;
    private SERC $serc;
    private SERCJudge $judge;
    private User $official;
    private User $referee;

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
        $this->comp->save();

        $this->serc = new SERC();
        $this->serc->name = 'Dry SERC';
        $this->serc->competition = $this->comp->id;
        $this->serc->digitalJudgeEnabled = true;
        $this->serc->save();

        $this->judge = new SERCJudge();
        $this->judge->name = 'Casualty 1 (Unconscious)';
        $this->judge->description = 'Unconscious casualty by the steps';
        $this->judge->serc = $this->serc->id;
        $this->judge->save();

        $this->official = User::factory()->create(['judge_only' => true]);
        $this->comp->addOfficial($this->official);

        $this->referee = User::factory()->create(['judge_only' => true]);
        $this->comp->addOfficial($this->referee, 'referee');
    }

    private function confirmJudgeUrl(): string
    {
        return route('judge.competition.serc.confirm', [
            'competition' => $this->comp->getSlug(),
            'serc' => $this->serc,
            'judge' => $this->judge,
        ]);
    }

    private function releaseUrl(): string
    {
        return route('judge.competition.sercs.release', ['competition' => $this->comp->getSlug()]);
    }

    public function test_competitions_start_with_sercs_locked(): void
    {
        $this->assertFalse($this->comp->fresh()->sercsReleased());
    }

    public function test_officials_see_locked_sercs_without_their_judges(): void
    {
        $this->actingAs($this->official)
            ->get(route('judge.competition', ['competition' => $this->comp->getSlug()]))
            ->assertOk()
            ->assertInertia(fn(AssertableInertia $page) => $page
                ->where('sercsReleased', false)
                ->where('sercs.0.name', 'Dry SERC')
                ->where('sercs.0.locked', true)
                ->missing('sercs.0.judges'));
    }

    public function test_officials_cannot_open_serc_routes_before_release(): void
    {
        $this->actingAs($this->official)
            ->get($this->confirmJudgeUrl())
            ->assertRedirect(route('judge.competition', ['competition' => $this->comp->getSlug()]));

        $this->actingAs($this->official)
            ->get(route('judge.competition.serc', ['competition' => $this->comp->getSlug(), 'serc' => $this->serc]))
            ->assertRedirect(route('judge.competition', ['competition' => $this->comp->getSlug()]));

        $this->actingAs($this->official)
            ->post(route('judge.competition.serc.confirm.post', ['competition' => $this->comp->getSlug(), 'serc' => $this->serc]), [
                'judge' => $this->judge->id,
            ])
            ->assertForbidden();

        $this->actingAs($this->official)
            ->getJson(route('judge.competition.violation.issue.getDraw', ['competition' => $this->comp->getSlug(), 'serc' => $this->serc]))
            ->assertForbidden();
    }

    public function test_officials_cannot_issue_serc_violations_before_release(): void
    {
        $this->actingAs($this->official)
            ->get(route('judge.competition.violation.issue', ['competition' => $this->comp->getSlug()]))
            ->assertOk()
            ->assertInertia(fn(AssertableInertia $page) => $page->has('sercs', 0));
    }

    public function test_site_admins_officiating_as_officials_cannot_see_sercs_before_release(): void
    {
        $admin = User::factory()->create(['admin' => true]);
        $this->comp->addOfficial($admin);

        $this->actingAs($admin)
            ->get($this->confirmJudgeUrl())
            ->assertRedirect(route('judge.competition', ['competition' => $this->comp->getSlug()]));
    }

    public function test_referees_can_see_sercs_before_release(): void
    {
        $this->actingAs($this->referee)
            ->get(route('judge.competition', ['competition' => $this->comp->getSlug()]))
            ->assertOk()
            ->assertInertia(fn(AssertableInertia $page) => $page
                ->missing('sercs.0.locked')
                ->where('sercs.0.judges.0.name', 'Casualty 1 (Unconscious)'));

        $this->actingAs($this->referee)
            ->get($this->confirmJudgeUrl())
            ->assertOk();
    }

    public function test_officials_cannot_release_sercs(): void
    {
        $this->actingAs($this->official)
            ->post($this->releaseUrl(), ['released' => true])
            ->assertForbidden();

        $this->assertFalse($this->comp->fresh()->sercsReleased());
    }

    public function test_referees_can_release_and_lock_sercs(): void
    {
        $this->actingAs($this->referee)
            ->post($this->releaseUrl(), ['released' => true])
            ->assertRedirect();

        $this->assertTrue($this->comp->fresh()->sercsReleased());

        $this->actingAs($this->official)
            ->get($this->confirmJudgeUrl())
            ->assertOk();

        $this->actingAs($this->referee)
            ->post($this->releaseUrl(), ['released' => false])
            ->assertRedirect();

        $this->assertFalse($this->comp->fresh()->sercsReleased());

        $this->actingAs($this->official)
            ->get($this->confirmJudgeUrl())
            ->assertRedirect(route('judge.competition', ['competition' => $this->comp->getSlug()]));
    }

    public function test_competition_admins_can_release_and_lock_sercs_from_the_main_site(): void
    {
        $admin = User::factory()->create();
        $this->comp->addAccount($admin, 'admin');

        $this->actingAs($admin)
            ->post(route('dj.sercs-release', $this->comp), ['released' => true])
            ->assertRedirect();

        $this->assertTrue($this->comp->fresh()->sercsReleased());

        $this->actingAs($admin)
            ->post(route('dj.sercs-release', $this->comp), ['released' => false])
            ->assertRedirect();

        $this->assertFalse($this->comp->fresh()->sercsReleased());
    }

    public function test_viewers_cannot_release_sercs_from_the_main_site(): void
    {
        $viewer = User::factory()->create();
        $this->comp->addAccount($viewer, 'view');

        $this->actingAs($viewer)
            ->post(route('dj.sercs-release', $this->comp), ['released' => true])
            ->assertForbidden();

        $this->assertFalse($this->comp->fresh()->sercsReleased());
    }
}
