<?php

namespace Tests\Feature\DigitalJudge;

use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\DigitalJudge\JudgeNote;
use App\Models\Orders\Draw;
use App\Models\SERC;
use App\Models\SERCJudge;
use App\Models\SERCMarkingPoint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SercTankFilterTest extends TestCase
{
    use RefreshDatabase;

    private Competition $comp;
    private SERC $serc;
    private SERCJudge $judge;

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

        $mp = new SERCMarkingPoint();
        $mp->name = 'Approach';
        $mp->judge = $this->judge->id;
        $mp->serc = $this->serc->id;
        $mp->weight = 1;
        $mp->save();

        foreach ([['A', 1, 1], ['B', 2, 2]] as [$letter, $tank, $position]) {
            $team = new CompetitionTeam();
            $team->competition = $this->comp->id;
            $team->team = $letter;
            $team->save();

            $draw = new Draw();
            $draw->serc = $this->serc->id;
            $draw->entity()->associate($team);
            $draw->tank = $tank;
            $draw->draw = $position;
            $draw->save();

            $note = new JudgeNote();
            $note->judge = $this->judge->id;
            $note->entity()->associate($team);
            $note->note = "Note for {$letter}";
            $note->save();
        }

        $official = User::factory()->create(['judge_only' => true]);
        $this->comp->addOfficial($official);
        $this->actingAs($official)->post(
            route('judge.competition.serc.confirm.post', ['competition' => $this->comp->getSlug(), 'serc' => $this->serc]),
            ['judge' => $this->judge->id]
        );
    }

    private function previousMarks()
    {
        return $this->getJson(route('judge.competition.serc.previous-marks', [
            'competition' => $this->comp->getSlug(),
            'serc' => $this->serc,
            'judge_id' => $this->judge->id,
        ]))->assertOk();
    }

    public function test_previous_marks_only_show_the_judges_tank(): void
    {
        $this->get(route('judge.competition.serc.set-tank', ['competition' => $this->comp->getSlug(), 'serc' => $this->serc, 'tank' => 2]));

        $this->previousMarks()
            ->assertJsonCount(1, '0.marks')
            ->assertJsonPath('0.marks.0.entity.name', 'Draw 2');
    }

    public function test_previous_marks_show_every_entry_without_a_tank(): void
    {
        $this->previousMarks()->assertJsonCount(2, '0.marks');
    }

    private function notes()
    {
        return $this->getJson(route('judge.competition.serc.mark.notes', [
            'competition' => $this->comp->getSlug(),
            'serc' => $this->serc,
        ]))->assertOk();
    }

    public function test_notes_only_show_the_judges_tank(): void
    {
        $this->get(route('judge.competition.serc.set-tank', ['competition' => $this->comp->getSlug(), 'serc' => $this->serc, 'tank' => 2]));

        $this->notes()
            ->assertJsonCount(1, '0.notes')
            ->assertJsonPath('0.notes.0.note', 'Note for B');
    }

    public function test_notes_show_every_entry_without_a_tank(): void
    {
        $this->notes()->assertJsonCount(2, '0.notes');
    }
}
