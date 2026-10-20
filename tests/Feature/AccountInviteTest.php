<?php

namespace Tests\Feature;

use App\Helpers\RouteHelpers;
use App\Models\AccountInvite;
use App\Models\Competition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * Someone without an account accepts an Invite to become an Organiser, which creates their account.
 */
class AccountInviteTest extends TestCase
{
    use RefreshDatabase;

    private Competition $comp;
    private AccountInvite $invite;

    protected function setUp(): void
    {
        parent::setUp();

        $this->comp = new Competition();
        $this->comp->name = 'Cardiff Classic';
        $this->comp->when = now();
        $this->comp->where = 'Test Pool';
        $this->comp->max_lanes = 8;
        $this->comp->anytimepin = false;
        $this->comp->seed_per_event = false;
        $this->comp->heats_per_event = true;
        $this->comp->use_seeds = true;
        $this->comp->can_be_live = true;
        $this->comp->save();

        $this->invite = new AccountInvite();
        $this->invite->email = 'new@example.com';
        $this->invite->details = ['access' => 'admin'];
        $this->invite->to()->associate($this->comp);
        $this->invite->save();
    }

    public function test_the_invite_page_names_what_they_are_joining(): void
    {
        $this->get(route('invite.show', [$this->invite, 'new@example.com']))
            ->assertOk()
            ->assertViewIs('manage')
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Auth/InviteNewAccount')
                ->where('invite.id', $this->invite->id)
                ->where('invite.email', 'new@example.com')
                ->where('invite.name', 'Cardiff Classic')
                ->where('invite.to', 'competition'));
    }

    public function test_accepting_creates_the_account_and_makes_them_an_organiser(): void
    {
        $this->post(route('manage.invite.accept-new-account', [$this->invite, 'new@example.com']), [
            'name' => 'Sam Organiser',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertRedirect(route('comps.view', $this->comp));

        $user = User::where('email', 'new@example.com')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertSame('Sam Organiser', $user->name);
        $this->assertModelMissing($this->invite);
    }

    public function test_accepting_needs_matching_passwords(): void
    {
        $this->post(route('manage.invite.accept-new-account', [$this->invite, 'new@example.com']), [
            'name' => 'Sam Organiser',
            'password' => 'secret123',
            'password_confirmation' => 'different',
        ])->assertSessionHasErrors(['password' => 'Your passwords do not match.']);

        $this->assertGuest();
        $this->assertModelExists($this->invite);
    }

    public function test_declining_deletes_the_invite_without_an_account(): void
    {
        $this->post(route('manage.invite.decline-new-account', [$this->invite, 'new@example.com']))
            ->assertRedirect(RouteHelpers::mainRoute('home'));

        $this->assertModelMissing($this->invite);
        $this->assertDatabaseMissing('users', ['email' => 'new@example.com']);
        $this->assertGuest();
    }

    public function test_an_invite_cant_be_used_with_another_email(): void
    {
        $this->post(route('manage.invite.decline-new-account', [$this->invite, 'someone@example.com']))
            ->assertRedirect();

        $this->assertModelExists($this->invite);
    }
}
