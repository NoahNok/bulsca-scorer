<?php

namespace Tests\Feature\DigitalJudge;

use App\Events\Announcement\AnnouncementSent;
use App\Events\Announcement\AnnouncementWithdrawn;
use App\Models\Competition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AnnouncementsTest extends TestCase
{
    use RefreshDatabase;

    private Competition $comp;
    private User $official;
    private User $referee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->comp = $this->makeCompetition();

        $this->official = User::factory()->create(['judge_only' => true, 'name' => 'Official Person']);
        $this->comp->addOfficial($this->official);

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
        $comp->save();

        return $comp;
    }

    private function send(User $as, string $text = 'Heat 4 delayed 10 min', ?Competition $comp = null)
    {
        return $this->actingAs($as)->postJson(
            route('judge.competition.announcements.store', ['competition' => ($comp ?? $this->comp)->getSlug()]),
            ['text' => $text]
        );
    }

    // SENDING

    public function test_a_referee_can_send_an_announcement(): void
    {
        Event::fake([AnnouncementSent::class]);

        $response = $this->send($this->referee)->assertCreated();

        $id = $response->json('id');
        $this->assertSame('Heat 4 delayed 10 min', $response->json('text'));
        $this->assertSame($this->referee->id, $response->json('sender.id'));
        $this->assertSame('Referee Person', $response->json('sender.name'));

        Event::assertDispatched(AnnouncementSent::class, function (AnnouncementSent $event) use ($id) {
            $this->assertSame('presence-judge.officials.' . $this->comp->id, (string) $event->broadcastOn()[0]);
            $this->assertSame('announcement.sent', $event->broadcastAs());

            $payload = $event->broadcastWith();
            $this->assertSame($id, $payload['id']);
            $this->assertSame('Heat 4 delayed 10 min', $payload['text']);
            $this->assertSame($this->referee->id, $payload['sender']['id']);

            return true;
        });
    }

    public function test_an_official_cannot_send_an_announcement(): void
    {
        Event::fake([AnnouncementSent::class]);

        $this->send($this->official)->assertForbidden();

        Event::assertNotDispatched(AnnouncementSent::class);
    }

    public function test_a_referee_cannot_send_to_another_competition(): void
    {
        $this->send($this->referee, comp: $this->makeCompetition())->assertForbidden();
    }

    public function test_an_announcement_needs_text(): void
    {
        $this->send($this->referee, '   ')->assertJsonValidationErrors('text');
    }

    public function test_an_announcement_is_at_most_280_characters(): void
    {
        $this->send($this->referee, str_repeat('a', 280))->assertCreated();
        $this->send($this->referee, str_repeat('a', 281))->assertJsonValidationErrors('text');
    }

    // LISTING

    private function list(User $as, ?Competition $comp = null)
    {
        return $this->actingAs($as)->getJson(
            route('judge.competition.announcements', ['competition' => ($comp ?? $this->comp)->getSlug()])
        );
    }

    public function test_officials_see_the_competitions_announcements_newest_first(): void
    {
        $this->send($this->referee, 'First');
        $this->travel(1)->minutes();
        $this->send($this->referee, 'Second');

        $other = $this->makeCompetition();
        $other->addOfficial($this->referee, 'referee');
        $this->send($this->referee, 'Elsewhere', $other);

        $this->list($this->official)
            ->assertOk()
            ->assertJsonPath('*.text', ['Second', 'First']);
    }

    public function test_strangers_cannot_list_announcements(): void
    {
        $this->list(User::factory()->create(['judge_only' => true]))->assertForbidden();
    }

    // WITHDRAWING

    private function withdraw(User $as, int $id, ?Competition $comp = null)
    {
        return $this->actingAs($as)->postJson(route('judge.competition.announcements.withdraw', [
            'competition' => ($comp ?? $this->comp)->getSlug(),
            'announcement' => $id,
        ]));
    }

    private function otherReferee(): User
    {
        $referee = User::factory()->create(['judge_only' => true, 'name' => 'Other Referee']);
        $this->comp->addOfficial($referee, 'referee');

        return $referee;
    }

    public function test_any_referee_can_withdraw_an_announcement(): void
    {
        $id = $this->send($this->referee)->json('id');

        Event::fake([AnnouncementWithdrawn::class]);

        $this->withdraw($this->otherReferee(), $id)->assertNoContent();

        Event::assertDispatched(AnnouncementWithdrawn::class, function (AnnouncementWithdrawn $event) use ($id) {
            $this->assertSame('presence-judge.officials.' . $this->comp->id, (string) $event->broadcastOn()[0]);
            $this->assertSame('announcement.withdrawn', $event->broadcastAs());
            $this->assertSame(['id' => $id], $event->broadcastWith());

            return true;
        });
    }

    public function test_nobody_sees_a_withdrawn_announcement(): void
    {
        $id = $this->send($this->referee, 'Oops')->json('id');
        $this->send($this->referee, 'Still on');
        $this->withdraw($this->referee, $id);

        $this->list($this->official)->assertJsonPath('*.text', ['Still on']);
        $this->list($this->referee)->assertJsonPath('*.text', ['Still on']);
    }

    public function test_an_official_cannot_withdraw_an_announcement(): void
    {
        $id = $this->send($this->referee)->json('id');

        $this->withdraw($this->official, $id)->assertForbidden();

        $this->list($this->official)->assertJsonCount(1);
    }

    public function test_an_announcement_can_only_be_withdrawn_once(): void
    {
        $id = $this->send($this->referee)->json('id');
        $this->withdraw($this->referee, $id);

        Event::fake([AnnouncementWithdrawn::class]);

        $this->withdraw($this->otherReferee(), $id)->assertNotFound();

        Event::assertNotDispatched(AnnouncementWithdrawn::class);
    }

    public function test_cannot_withdraw_another_competitions_announcement(): void
    {
        $other = $this->makeCompetition();
        $other->addOfficial($this->referee, 'referee');
        $id = $this->send($this->referee, comp: $other)->json('id');

        $this->withdraw($this->referee, $id)->assertNotFound();
    }
}
