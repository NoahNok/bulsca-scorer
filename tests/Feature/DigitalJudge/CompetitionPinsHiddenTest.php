<?php

namespace Tests\Feature\DigitalJudge;

use App\Models\Competition;
use App\Models\SERC;
use App\Models\SERCJudge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class CompetitionPinsHiddenTest extends TestCase
{
    use RefreshDatabase;

    private Competition $comp;
    private SERC $serc;
    private SERCJudge $judge;
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
        $this->comp->digitalJudgeEnabled = true;
        $this->comp->digitalJudgePin = '123456';
        $this->comp->digitalJudgeHeadPin = '654321';
        $this->comp->sercs_released_at = now();
        $this->comp->save();

        $this->serc = new SERC();
        $this->serc->name = 'Dry SERC';
        $this->serc->competition = $this->comp->id;
        $this->serc->digitalJudgeEnabled = true;
        $this->serc->save();

        $this->judge = new SERCJudge();
        $this->judge->name = 'Casualty 1';
        $this->judge->description = 'By the steps';
        $this->judge->serc = $this->serc->id;
        $this->judge->save();

        $this->official = User::factory()->create(['judge_only' => true]);
        $this->comp->addOfficial($this->official);
    }

    public function test_serialised_competition_hides_pins(): void
    {
        $this->assertArrayNotHasKey('digitalJudgePin', $this->comp->toArray());
        $this->assertArrayNotHasKey('digitalJudgeHeadPin', $this->comp->toArray());
    }

    public function test_officials_pages_only_get_jsonable_competition(): void
    {
        $slug = $this->comp->getSlug();

        $this->actingAs($this->official)
            ->post(route('judge.competition.serc.confirm.post', ['competition' => $slug, 'serc' => $this->serc]), ['judge' => $this->judge->id]);

        // Select Tank first: Home clears the selected judges
        $urls = [
            route('judge.competition.serc.select-tank', ['competition' => $slug, 'serc' => $this->serc]),
            route('judge.competition', ['competition' => $slug]),
            route('judge.competition.violation.submissions', ['competition' => $slug]),
        ];

        foreach ($urls as $url) {
            $this->actingAs($this->official)
                ->get($url)
                ->assertOk()
                ->assertInertia(fn(AssertableInertia $page) => $page
                    ->where('competition', $this->comp->jsonable())
                    ->etc());
        }
    }
}
