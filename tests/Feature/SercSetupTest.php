<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\SERC;
use App\Models\SERC\MarkingPointTemplate;
use App\Models\SERCJudge;
use App\Models\SERCMarkingPoint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    private function payload(array $judges, array $deleted = ['judges' => [], 'marking_points' => []]): array
    {
        return [
            'name' => 'Dry SERC',
            'type' => 'DRY',
            'target_entity' => 'team',
            'judges' => $judges,
            'deleted' => $deleted,
        ];
    }

    private function judge(?int $id = null, ?int $markingPointId = null): array
    {
        return [
            'id' => $id,
            'name' => 'Casualty 1',
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
        return route('comps.events.sercs.editPost', [$this->comp, $serc]);
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
            ->postJson(route('comps.events.sercs.addPost', $this->comp), $this->payload([$this->judge()]))
            ->assertOk();

        $serc = SERC::where('competition', $this->comp->id)->latest('id')->first();
        $judge = $serc->getJudges()->sole();
        $markingPoint = $judge->getMarkingPoints()->sole();

        $this->actingAs($this->admin)
            ->postJson($this->editUrl($serc), $this->payload([$this->judge($judge->id, $markingPoint->id)]))
            ->assertOk();

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
            ->postJson(route('comps.events.sercs.addPost', $this->comp), $this->payload([$this->judge($this->otherJudge->id)]))
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
}
