<?php

namespace Tests\Feature;

use App\Models\LoginToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class QrLoginTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $official;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->forceFill(['admin' => true])->save();

        $this->official = User::factory()->create(['email_verified_at' => null]);
        $this->official->forceFill(['judge_only' => true])->save();
    }

    public function test_admins_get_a_qr_code_on_the_user_page(): void
    {
        $page = $this->appUrl("/admin/users/{$this->official->id}");

        $this->actingAs($this->admin)
            ->from($page)
            ->post($this->appUrl("/admin/users/{$this->official->id}/login-qr"))
            ->assertRedirect($page);

        $this->get($page)->assertInertia(fn (Assert $p) => $p
            ->component('Admin/User')
            ->where('loginQr.svg', fn ($svg) => str_contains($svg, '<svg'))
            ->has('loginQr.expiresAt'));

        // flashed, so a reload drops it
        $this->get($page)->assertInertia(fn (Assert $p) => $p->where('loginQr', null));

        $this->assertDatabaseHas('login_tokens', ['user_id' => $this->official->id, 'created_by' => $this->admin->id]);
    }

    public function test_non_admins_cannot_make_qr_codes(): void
    {
        $this->actingAs($this->official)
            ->post($this->appUrl("/admin/users/{$this->official->id}/login-qr"))
            ->assertRedirect('/');

        $this->assertDatabaseCount('login_tokens', 0);
    }

    public function test_admin_accounts_cannot_get_qr_codes(): void
    {
        $other = User::factory()->create();
        $other->forceFill(['admin' => true])->save();

        $this->actingAs($this->admin)
            ->post($this->appUrl("/admin/users/{$other->id}/login-qr"))
            ->assertSessionHas('alert-error');

        $this->assertDatabaseCount('login_tokens', 0);
    }

    public function test_a_token_signs_in_once(): void
    {
        $token = LoginToken::issueFor($this->official, $this->admin);

        $this->get(route('judge.qr-login', $token))->assertRedirect(route('judge.index'));
        $this->assertAuthenticatedAs($this->official);

        auth()->logout();

        $this->get(route('judge.qr-login', $token))
            ->assertRedirect(route('judge.login'))
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_a_token_expires(): void
    {
        $token = LoginToken::issueFor($this->official, $this->admin);

        $this->travel(LoginToken::TTL_MINUTES + 1)->minutes();

        $this->get(route('judge.qr-login', $token))->assertRedirect(route('judge.login'));
        $this->assertGuest();
    }

    public function test_an_unknown_token_does_nothing(): void
    {
        LoginToken::issueFor($this->official, $this->admin);

        $this->get(route('judge.qr-login', str_repeat('a', 64)))->assertRedirect(route('judge.login'));
        $this->assertGuest();
    }

    public function test_a_new_token_replaces_the_old_one(): void
    {
        $old = LoginToken::issueFor($this->official, $this->admin);
        $new = LoginToken::issueFor($this->official, $this->admin);

        $this->get(route('judge.qr-login', $old))->assertRedirect(route('judge.login'));
        $this->assertGuest();

        $this->get(route('judge.qr-login', $new))->assertRedirect(route('judge.index'));
        $this->assertAuthenticatedAs($this->official);
    }

    public function test_only_a_hash_of_the_token_is_stored(): void
    {
        $token = LoginToken::issueFor($this->official, $this->admin);

        $this->assertDatabaseMissing('login_tokens', ['token_hash' => $token]);
        $this->assertDatabaseHas('login_tokens', ['token_hash' => hash('sha256', $token)]);
    }

    public function test_it_swaps_a_signed_in_device_over(): void
    {
        $token = LoginToken::issueFor($this->official, $this->admin);

        $this->actingAs($this->admin)
            ->get(route('judge.qr-login', $token))
            ->assertRedirect(route('judge.index'));

        $this->assertAuthenticatedAs($this->official);
    }
}
