<?php

namespace Tests\Feature\DigitalJudge;

use App\Models\Competition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Broadcast;
use Tests\TestCase;

class OfficialsPresenceTest extends TestCase
{
    use RefreshDatabase;

    private Competition $comp;
    private User $official;
    private User $referee;

    protected function setUp(): void
    {
        parent::setUp();

        // presence auth signs the channel data locally, so dummy credentials are enough.
        // The channels are registered on the broadcaster resolved at boot, so register
        // them again on the fresh one
        config([
            'broadcasting.default' => 'reverb',
            'broadcasting.connections.reverb.key' => 'test-key',
            'broadcasting.connections.reverb.secret' => 'test-secret',
            'broadcasting.connections.reverb.app_id' => 'test-app',
        ]);
        Broadcast::purge('reverb');
        require base_path('routes/channels.php');

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

        $this->official = User::factory()->create(['judge_only' => true]);
        $this->comp->addOfficial($this->official);

        $this->referee = User::factory()->create(['judge_only' => true]);
        $this->comp->addOfficial($this->referee, 'referee');
    }

    private function authorise()
    {
        return $this->post('/broadcasting/auth', [
            'channel_name' => 'presence-judge.officials.' . $this->comp->id,
            'socket_id' => '1234.5678',
        ]);
    }

    private function channelData($response): array
    {
        return json_decode($response->json('channel_data'), true);
    }

    public function test_officials_join_with_their_name_and_role(): void
    {
        $response = $this->actingAs($this->official)->authorise()->assertOk();

        // Pusher sends the member key as a string
        $this->assertSame([
            'user_id' => (string) $this->official->id,
            'user_info' => [
                'id' => $this->official->id,
                'name' => $this->official->name,
                'role' => 'Official',
                'photo' => null,
            ],
        ], $this->channelData($response));
    }

    public function test_officials_join_with_their_photo(): void
    {
        $this->official->forceFill(['profile_photo' => 'avatars/1-abc'])->save();

        $response = $this->actingAs($this->official)->authorise()->assertOk();

        $this->assertSame(
            $this->official->profilePhotoUrls(),
            $this->channelData($response)['user_info']['photo'],
        );
    }

    public function test_referees_join_as_referee(): void
    {
        $response = $this->actingAs($this->referee)->authorise()->assertOk();

        $this->assertSame('Referee', $this->channelData($response)['user_info']['role']);
    }

    public function test_admins_join_without_being_an_official(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['admin' => true])->save();

        $response = $this->actingAs($admin)->authorise()->assertOk();

        $this->assertSame('Admin', $this->channelData($response)['user_info']['role']);
    }

    public function test_organisers_watch_from_the_management_app_without_showing_as_officials(): void
    {
        $organiser = User::factory()->create();
        $this->comp->addAccount($organiser, 'admin');

        $response = $this->actingAs($organiser)->post($this->appUrl('/broadcasting/auth'), [
            'channel_name' => 'presence-judge.officials.' . $this->comp->id,
            'socket_id' => '1234.5678',
        ])->assertOk();

        $this->assertSame(
            ['id' => $organiser->id, 'name' => $organiser->name, 'watching' => true],
            $this->channelData($response)['user_info'],
        );
    }

    public function test_only_organisers_can_watch_from_the_management_app(): void
    {
        $viewer = User::factory()->create();
        $this->comp->addAccount($viewer, 'view');

        foreach ([$this->official, $viewer] as $user) {
            $this->actingAs($user)->post($this->appUrl('/broadcasting/auth'), [
                'channel_name' => 'presence-judge.officials.' . $this->comp->id,
                'socket_id' => '1234.5678',
            ])->assertForbidden();
        }
    }

    public function test_other_users_cannot_join(): void
    {
        $this->actingAs(User::factory()->create(['judge_only' => true]))
            ->authorise()
            ->assertForbidden();
    }

    public function test_guests_cannot_join(): void
    {
        $this->authorise()->assertForbidden();
    }

    public function test_officials_can_list_everyone_officiating(): void
    {
        $this->actingAs($this->official)
            ->getJson(route('judge.competition.officials', ['competition' => $this->comp->getSlug()]))
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonFragment(['id' => $this->official->id, 'name' => $this->official->name, 'role' => 'Official'])
            ->assertJsonFragment(['id' => $this->referee->id, 'name' => $this->referee->name, 'role' => 'Referee']);
    }

    public function test_the_officials_list_includes_photos(): void
    {
        $this->referee->forceFill(['profile_photo' => 'avatars/2-abc'])->save();

        $response = $this->actingAs($this->official)
            ->getJson(route('judge.competition.officials', ['competition' => $this->comp->getSlug()]))
            ->assertOk();

        $photos = collect($response->json())->pluck('photo', 'id');
        $this->assertNull($photos[$this->official->id]);
        $this->assertStringEndsWith('/storage/avatars/2-abc.webp', $photos[$this->referee->id]['webp']);
        $this->assertStringEndsWith('/storage/avatars/2-abc.jpg', $photos[$this->referee->id]['jpg']);
    }

    public function test_other_users_cannot_list_officials(): void
    {
        $this->actingAs(User::factory()->create(['judge_only' => true]))
            ->getJson(route('judge.competition.officials', ['competition' => $this->comp->getSlug()]))
            ->assertForbidden();
    }
}
