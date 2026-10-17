<?php

namespace Tests\Feature;

use App\Models\Activity\Activity;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\SERC;
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
    private SERC $serc;
    private CompetitionTeam $teamA;
    private CompetitionTeam $teamB;
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

        $this->serc = new SERC();
        $this->serc->name = 'Dry SERC';
        $this->serc->competition = $this->comp->id;
        $this->serc->scorable_entity = 'team';
        $this->serc->save();

        $this->teamA = $this->team($this->comp, 'A');
        $this->teamB = $this->team($this->comp, 'B');

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

    public function test_filtering_by_event_and_team_shows_only_activity_for_that_event_and_team(): void
    {
        $this->recordActivity('SPEED_RESULT_MARKED', 'A on speed', related: [$this->event, $this->teamA, $this->comp]);
        $this->recordActivity('SPEED_RESULT_MARKED', 'B on speed', related: [$this->event, $this->teamB, $this->comp]);
        $this->recordActivity('SERC_MARKED', 'A on serc', related: [$this->serc, $this->teamA, $this->comp]);
        $this->recordActivity('DRAW_GENERATED', 'Draw for the whole competition', related: $this->comp);

        $this->actingAs($this->admin)
            ->get(route('comps.activityLog', [$this->comp, 'related' => "speed:{$this->event->id}|team:{$this->teamA->id}"]))
            ->assertOk()
            ->assertSee('A on speed')
            ->assertDontSee('B on speed')
            ->assertDontSee('A on serc')
            ->assertDontSee('Draw for the whole competition');
    }

    public function test_the_competition_log_never_shows_another_competitions_activity(): void
    {
        $other = $this->competition('Their Competition');
        $theirTeam = $this->team($other, 'Z');

        $this->recordActivity('SERC_MARKED', 'Their marks', related: [$theirTeam, $other]);

        $this->actingAs($this->admin)
            ->get(route('comps.activityLog', [$this->comp, 'related' => "team:{$theirTeam->id}"]))
            ->assertOk()
            ->assertDontSee('Their marks');
    }

    public function test_filter_chips_name_what_the_log_is_filtered_to(): void
    {
        $this->actingAs($this->admin)
            ->get(route('comps.activityLog', [$this->comp, 'related' => "serc:{$this->serc->id}|team:{$this->teamA->id}"]))
            ->assertOk()
            ->assertSee('Showing activity for')
            ->assertSee('SERC: Dry SERC')
            ->assertSee('Team: ' . $this->teamA->getName($this->comp))
            ->assertSee('related=serc%3A' . $this->serc->id, false);
    }

    public function test_filter_chips_dont_name_another_competitions_team(): void
    {
        $theirTeam = $this->team($this->competition('Their Competition'), 'Z');

        $this->actingAs($this->admin)
            ->get(route('comps.activityLog', [$this->comp, 'related' => "team:{$theirTeam->id}"]))
            ->assertOk()
            ->assertSee("Team: #{$theirTeam->id}")
            ->assertDontSee($theirTeam->getName());
    }

    public function test_an_organiser_entering_dnf_is_recorded(): void
    {
        $result = new SpeedResult();
        $result->event = $this->event->id;
        $result->entity()->associate($this->teamA);
        $result->result = 62500;
        $result->save();

        $this->actingAs($this->admin)->post(route('comps.view.events.speeds.editResultPost', [$this->comp, $this->event]), [
            'data' => json_encode([
                ['id' => $result->id, 'values' => ['disqualification' => '', 'result' => 'DNF']],
            ]),
        ])->assertOk();

        $activity = Activity::where('activity', 'SPEED_RESULT_UPDATED')->sole();

        $this->assertEquals('01:02.500', $activity->context['changes']['result']['old']);
        $this->assertEquals('DNF', $activity->context['changes']['result']['new']);
        $this->assertEquals($this->admin->id, $activity->user_id);
    }
}
