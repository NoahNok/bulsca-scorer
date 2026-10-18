<?php

namespace Tests\Feature\DigitalJudge;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\Orders\Heat;
use App\Models\SpeedEvent;
use App\Models\SpeedResult;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpeedTimesTest extends TestCase
{
    use RefreshDatabase;

    private Competition $comp;
    private CompetitionSpeedEvent $event;
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
        $this->comp->heats_per_event = true;
        $this->comp->use_seeds = true;
        $this->comp->save();

        $this->event = new CompetitionSpeedEvent();
        $this->event->event = $this->speedEvent('Swim & Tow');
        $this->event->competition = $this->comp->id;
        $this->event->record = 0;
        $this->event->save();

        $this->official = User::factory()->create(['judge_only' => true]);
        $this->comp->addOfficial($this->official);
    }

    private function speedEvent(string $name): int
    {
        $event = new SpeedEvent();
        $event->name = $name;
        $event->save();

        return $event->id;
    }

    private function team(string $letter, ?int $heat = null, bool $withResult = true): CompetitionTeam
    {
        $team = new CompetitionTeam();
        $team->competition = $this->comp->id;
        $team->team = $letter;
        $team->save();

        if ($heat) {
            $lane = new Heat();
            $lane->speed_event = $this->event->id;
            $lane->entity()->associate($team);
            $lane->heat = $heat;
            $lane->lane = $team->id;
            $lane->save();
        }

        if ($withResult) {
            $result = new SpeedResult();
            $result->event = $this->event->id;
            $result->entity()->associate($team);
            $result->save();
        }

        return $team;
    }

    private function store(array $marks)
    {
        return $this->actingAs($this->official)->postJson(route('judge.competition.event.time.mark.store', [
            'competition' => $this->comp->getSlug(),
            'event' => $this->event,
            'heat' => 1,
        ]), ['mark' => $marks]);
    }

    private function resultFor(CompetitionTeam $team): ?string
    {
        return SpeedResult::whereMorphedTo('entity', $team)->where('event', $this->event->id)->value('result');
    }

    public function test_times_are_saved_for_the_heat(): void
    {
        $a = $this->team('A', heat: 1);
        $b = $this->team('B', heat: 1);

        $this->store([$a->id => '1:02.50', $b->id => '0:58.10'])->assertOk();

        $this->assertEquals(62500, $this->resultFor($a));
        $this->assertEquals(58100, $this->resultFor($b));
    }

    public function test_a_team_from_another_heat_is_rejected_and_nothing_is_saved(): void
    {
        $a = $this->team('A', heat: 1);
        $other = $this->team('B', heat: 2);

        $this->store([$a->id => '1:02.50', $other->id => '0:58.10'])->assertStatus(422);

        $this->assertNull($this->resultFor($a));
        $this->assertNull($this->resultFor($other));
    }

    public function test_a_team_with_no_result_row_is_rejected_and_nothing_is_saved(): void
    {
        $a = $this->team('A', heat: 1);
        $missing = $this->team('B', heat: 1, withResult: false);

        $this->store([$a->id => '1:02.50', $missing->id => '0:58.10'])->assertStatus(422);

        $this->assertNull($this->resultFor($a));
    }

    public function test_an_unknown_team_is_rejected(): void
    {
        $this->team('A', heat: 1);

        $this->store([999999 => '1:02.50'])->assertStatus(422);
    }
}
