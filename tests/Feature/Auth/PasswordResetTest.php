<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get($this->appUrl('/forgot-password'));

        $response->assertStatus(200)
            ->assertViewIs('manage')
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Auth/ForgotPassword'));
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->from($this->appUrl('/forgot-password'))
            ->post($this->appUrl('/forgot-password'), ['email' => $user->email])
            ->assertRedirect($this->appUrl('/forgot-password'))
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post($this->appUrl('/forgot-password'), ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->get($this->appUrl('/reset-password/'.$notification->token.'?email='.urlencode($user->email)));

            $response->assertStatus(200)
                ->assertViewIs('manage')
                ->assertInertia(fn (Assert $page) => $page
                    ->component('Manage/Auth/ResetPassword')
                    ->where('token', $notification->token)
                    ->where('email', $user->email));

            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post($this->appUrl('/forgot-password'), ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post($this->appUrl('/reset-password'), [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response->assertSessionHasNoErrors()
                ->assertRedirect(route('login'))
                ->assertSessionHas('status');

            return true;
        });
    }

    public function test_password_cannot_be_reset_with_invalid_token(): void
    {
        $user = User::factory()->create();

        $this->post($this->appUrl('/reset-password'), [
            'token' => 'not-a-token',
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertSessionHasErrors('email');
    }
}
