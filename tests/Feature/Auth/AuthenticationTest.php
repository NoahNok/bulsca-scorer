<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get($this->appUrl('/login'));

        $response->assertStatus(200)
            ->assertViewIs('manage')
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Auth/SignIn'));
    }

    public function test_sign_in_shows_flashed_messages(): void
    {
        $this->withSession(['status' => 'Your password has been reset.', 'alert-error' => 'Invalid invite'])
            ->get($this->appUrl('/login'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('manage.flash.status', 'Your password has been reset.')
                ->where('manage.flash.error', 'Invalid invite'));
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post($this->appUrl('/login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_signing_in_from_the_svelte_page_is_a_full_visit(): void
    {
        $user = User::factory()->create();

        // the intended page may still be Blade, which an Inertia visit can't load
        $this->withHeaders(['X-Inertia' => 'true'])
            ->post($this->appUrl('/login'), [
                'email' => $user->email,
                'password' => 'password',
            ])
            ->assertStatus(409)
            ->assertHeader('X-Inertia-Location');

        $this->assertAuthenticated();
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post($this->appUrl('/login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('auth')->assertSessionDoesntHaveErrors('email');

        $this->assertGuest();
    }

    public function test_officials_only_accounts_are_pointed_to_the_digital_judge(): void
    {
        $official = User::factory()->create();
        $official->forceFill(['judge_only' => true])->save();

        $this->post($this->appUrl('/login'), [
            'email' => $official->email,
            'password' => 'password',
        ])->assertSessionHasErrors(['auth' => 'This account can only sign in to Digital Judge.']);

        $this->assertGuest();
    }

    public function test_repeated_failures_are_throttled(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 6) as $_) {
            $response = $this->post($this->appUrl('/login'), [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        $response->assertSessionHasErrors('auth');
        $this->assertStringContainsString('Too many', session('errors')->first('auth'));
    }
}
