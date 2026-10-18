<?php

namespace Tests\Feature\DigitalJudge;

use App\Events\Violation\ViolationStatusChanged;
use App\Events\Violation\ViolationSubmitted;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\DigitalJudge\Violation\ViolationSubmission;
use App\Models\DQCode;
use App\Models\SERC;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ViolationAlertsTest extends TestCase
{
    use RefreshDatabase;

    private Competition $comp;
    private SERC $serc;
    private CompetitionTeam $team;
    private DQCode $dq;
    private User $judge;
    private User $referee;

    protected function setUp(): void
    {
        parent::setUp();

        // channel auth signs locally, so dummy credentials are enough. The channels are
        // registered on the broadcaster resolved at boot, so register them again on the fresh one
        config([
            'broadcasting.default' => 'reverb',
            'broadcasting.connections.reverb.key' => 'test-key',
            'broadcasting.connections.reverb.secret' => 'test-secret',
            'broadcasting.connections.reverb.app_id' => 'test-app',
        ]);
        Broadcast::purge('reverb');
        require base_path('routes/channels.php');

        $this->comp = $this->makeCompetition();

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

        $this->referee = User::factory()->create(['judge_only' => true, 'name' => 'Referee Person']);
        $this->comp->addOfficial($this->referee, 'referee');
    }

    private function makeCompetition(): Competition
    {
        $comp = new Competition();
        $comp->name = 'Test Competition';
        $comp->when = now()->addWeek();
        $comp->where = 'Test Pool';
        $comp->max_lanes = 8;
        $comp->anytimepin = false;
        $comp->seed_per_event = false;
        $comp->heats_per_event = false;
        $comp->use_seeds = true;
        $comp->sercs_released_at = now();
        $comp->save();

        return $comp;
    }

    private function violationData(): array
    {
        return [
            'entity_id' => $this->team->id,
            'event' => ['id' => $this->serc->id, 'type' => 'serc'],
            'violation' => ['id' => $this->dq->id, 'vtype' => 'DQ'],
            'details' => ['details' => 'Saw it'],
            'submitter' => ['position' => 'SERC judge'],
            'seconder' => ['user_id' => null, 'position' => ''],
        ];
    }

    private function submit(?User $as = null)
    {
        return $this->actingAs($as ?? $this->judge)->post(
            route('judge.competition.violation.submit', ['competition' => $this->comp->getSlug()]),
            $this->violationData()
        );
    }

    private function updateState(ViolationSubmission $submission, string $state)
    {
        return $this->actingAs($this->referee)->postJson(
            route('judge.competition.violation.submission.update-state', [
                'competition' => $this->comp->getSlug(),
                'submission' => $submission->id,
            ]),
            ['state' => $state]
        );
    }

    private function authoriseReferees(Competition $comp)
    {
        return $this->post('/broadcasting/auth', [
            'channel_name' => 'private-judge.referees.' . $comp->id,
            'socket_id' => '1234.5678',
        ]);
    }

    // CHANNEL

    public function test_referees_can_listen_for_new_submissions(): void
    {
        $this->actingAs($this->referee)->authoriseReferees($this->comp)->assertOk();
    }

    public function test_officials_cannot_listen_for_new_submissions(): void
    {
        $this->actingAs($this->judge)->authoriseReferees($this->comp)->assertForbidden();
    }

    public function test_referees_cannot_listen_on_another_competition(): void
    {
        $this->actingAs($this->referee)->authoriseReferees($this->makeCompetition())->assertForbidden();
    }

    // SUBMITTING

    public function test_submitting_alerts_the_referees(): void
    {
        Event::fake([ViolationSubmitted::class]);

        $this->submit()->assertSessionHasNoErrors();

        $submission = ViolationSubmission::sole();

        Event::assertDispatched(ViolationSubmitted::class, function (ViolationSubmitted $event) use ($submission) {
            $this->assertSame('private-judge.referees.' . $this->comp->id, (string) $event->broadcastOn()[0]);
            $this->assertSame('violation.submitted', $event->broadcastAs());

            $payload = $event->broadcastWith();
            $this->assertSame($submission->id, $payload['submission_id']);
            $this->assertSame($this->comp->id, $payload['competition_id']);
            $this->assertSame('SUBMITTED', $payload['status']);
            $this->assertNull($payload['from']);
            $this->assertSame('DQ100', $payload['code']);
            $this->assertSame('DQ', $payload['vtype']);
            $this->assertSame('Dry SERC', $payload['event']);
            $this->assertSame(['id' => $this->judge->id, 'name' => 'Judge Person'], $payload['submitter']);

            return true;
        });
    }

    public function test_resubmitting_alerts_the_referees(): void
    {
        $this->submit();
        $submission = ViolationSubmission::sole();
        $this->updateState($submission, 'REJECTED')->assertOk();

        Event::fake([ViolationSubmitted::class]);

        $this->actingAs($this->judge)->post(
            route('judge.competition.violation.submission.resubmit', [
                'competition' => $this->comp->getSlug(),
                'submission' => $submission->id,
            ]),
            $this->violationData()
        )->assertSessionHasNoErrors();

        Event::assertDispatched(
            ViolationSubmitted::class,
            fn(ViolationSubmitted $event) => $event->broadcastWith()['from'] === 'REJECTED'
        );
    }

    // STATUS CHANGES

    public function test_a_status_change_alerts_the_submitter(): void
    {
        $this->submit();
        $submission = ViolationSubmission::sole();

        Event::fake([ViolationStatusChanged::class]);

        $this->updateState($submission, 'ACCEPTED')->assertOk();

        Event::assertDispatched(ViolationStatusChanged::class, function (ViolationStatusChanged $event) {
            $this->assertSame('private-App.Models.User.' . $this->judge->id, (string) $event->broadcastOn()[0]);
            $this->assertSame('violation.status-changed', $event->broadcastAs());
            $this->assertSame('ACCEPTED', $event->broadcastWith()['status']);
            $this->assertSame('SUBMITTED', $event->broadcastWith()['from']);

            return true;
        });
    }

    public function test_no_alert_when_the_status_is_unchanged(): void
    {
        $this->submit();
        $submission = ViolationSubmission::sole();

        Event::fake([ViolationStatusChanged::class]);

        $this->updateState($submission, 'SUBMITTED')->assertOk();

        Event::assertNotDispatched(ViolationStatusChanged::class);
    }

    public function test_referees_are_not_alerted_about_changing_their_own_submission(): void
    {
        $this->submit($this->referee);
        $submission = ViolationSubmission::sole();

        Event::fake([ViolationStatusChanged::class]);

        $this->updateState($submission, 'ACCEPTED')->assertOk();

        Event::assertNotDispatched(ViolationStatusChanged::class);
    }
}
