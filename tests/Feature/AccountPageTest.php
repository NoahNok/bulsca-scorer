<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * Your account (Svelte, in ManageLayout): Details (your name) and Security (password and devices).
 */
class AccountPageTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['name' => 'Sam Swimmer', 'password' => Hash::make('old-password')]);
    }

    public function test_it_shows_your_details_and_devices(): void
    {
        $this->actingAs($this->user)->get($this->appUrl('/account'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Account')
                ->where('user.name', 'Sam Swimmer')
                ->where('user.email', $this->user->email)
                ->has('devices'));
    }

    public function test_guests_are_sent_to_sign_in(): void
    {
        $this->get($this->appUrl('/account'))->assertRedirect();
    }

    public function test_it_saves_your_name(): void
    {
        $this->actingAs($this->user)->post($this->appUrl('/account'), ['name' => 'Sam Lifesaver'])
            ->assertRedirect()
            ->assertSessionHas('success', 'Details saved');

        $this->assertSame('Sam Lifesaver', $this->user->fresh()->name);
    }

    public function test_a_name_is_required(): void
    {
        $this->actingAs($this->user)->post($this->appUrl('/account'), ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    public function test_it_changes_your_password(): void
    {
        $this->actingAs($this->user)->put($this->appUrl('/account/password'), [
            'current_password'      => 'old-password',
            'password'              => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertRedirect()->assertSessionHas('success', 'Password changed');

        $this->assertTrue(Hash::check('new-password-123', $this->user->fresh()->password));
    }

    public function test_it_needs_your_current_password(): void
    {
        $this->actingAs($this->user)->put($this->appUrl('/account/password'), [
            'current_password'      => 'wrong-password',
            'password'              => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('old-password', $this->user->fresh()->password));
    }

    public function test_the_new_password_must_be_confirmed(): void
    {
        $this->actingAs($this->user)->put($this->appUrl('/account/password'), [
            'current_password'      => 'old-password',
            'password'              => 'new-password-123',
            'password_confirmation' => 'something-else',
        ])->assertSessionHasErrors('password');
    }

    public function test_it_signs_out_other_devices(): void
    {
        $this->actingAs($this->user)->delete($this->appUrl('/account/devices'))
            ->assertRedirect()
            ->assertSessionHas('success', 'Other devices signed out');
    }
}
