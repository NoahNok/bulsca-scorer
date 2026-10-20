<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * Open sign-up is off: accounts come from an Invite (AccountInviteTest).
 */
class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_explains_accounts_come_by_invite(): void
    {
        $this->get($this->appUrl('/register'))
            ->assertOk()
            ->assertViewIs('manage')
            ->assertInertia(fn (Assert $page) => $page->component('Manage/Auth/Register'));
    }

    public function test_open_sign_up_is_closed(): void
    {
        $this->post($this->appUrl('/register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertForbidden();

        $this->assertGuest();
    }
}
