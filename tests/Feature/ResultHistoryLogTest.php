<?php

namespace Tests\Feature;

use App\Models\Activity\Activity;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\SpeedEvent;
use App\Models\SpeedResult;
use App\Models\User;
use App\Traits\RecordActivity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResultHistoryLogTest extends TestCase
{
    use RefreshDatabase, RecordActivity;

    private Competition $comp;
    private CompetitionSpeedEvent $event;
    private CompetitionTeam $teamA;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->comp = $this->competition('Our Competition');

        $base = new SpeedEvent();
        $base->name = 'Swim & Tow';
        $base->save();

        $this->event = new CompetitionSpeedEvent();
        $this->event->event = $base->id;
        $this->event->competition = $this->comp->id;
        $this->event->record = 0;
        $this->event->save();

        $this->teamA = $this->team($this->comp, 'A');

        $this->admin = User::factory()->create();
        $this->comp->addAccount($this->admin, 'admin');
    }

    private function competition(string $name): Competition
    {
        $comp = new Competition();
        $comp->name = $name;
        $comp->when = now()->addWeek();
        $comp->where = 'Test Pool';
        $comp->max_lanes = 8;
        $comp->anytimepin = false;
        $comp->seed_per_event = false;
        $comp->heats_per_event = true;
        $comp->use_seeds = true;
        $comp->save();

        return $comp;
    }

    private function team(Competition $comp, string $letter): CompetitionTeam
    {
        $team = new CompetitionTeam();
        $team->competition = $comp->id;
        $team->team = $letter;
        $team->save();

        return $team;
    }

    public function test_an_organiser_entering_dnf_is_recorded(): void
    {
        $result = new SpeedResult();
        $result->event = $this->event->id;
        $result->entity()->associate($this->teamA);
        $result->result = 62500;
        $result->save();

        $this->actingAs($this->admin)->post(route('manage.competitions.events.speeds.results', [$this->comp, $this->event]), [
            'rows' => [$result->id => ['disqualification' => '', 'result' => 'DNF']],
        ])->assertRedirect();

        $activity = Activity::where('activity', 'SPEED_RESULT_UPDATED')->sole();

        $this->assertEquals('01:02.500', $activity->context['changes']['result']['old']);
        $this->assertEquals('DNF', $activity->context['changes']['result']['new']);
        $this->assertEquals($this->admin->id, $activity->user_id);
    }
}
