<?php

namespace Tests\Feature;

use App\Http\Controllers\AccountInviteController;
use App\Mail\AccountInviteMail;
use App\Models\AccountInvite;
use App\Models\Organisation\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * An organisation's Accounts (Svelte): who can manage it, inviting someone, changing their access,
 * removing them, and cancelling an invite.
 */
class OrganisationAccountsTest extends TestCase
{
    use RefreshDatabase;

    private Organisation $org;

    private User $owner;

    private User $organiser;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        $this->org = new Organisation();
        $this->org->name = 'Cardiff ULSC';
        $this->org->save();

        $this->owner = User::factory()->create(['name' => 'Olly Owner']);
        $this->org->addAccount($this->owner, 'owner');

        $this->organiser = User::factory()->create(['name' => 'Ash Admin']);
        $this->org->addAccount($this->organiser, 'admin');
    }

    private function url(string $path = ''): string
    {
        return $this->appUrl("/organisation/{$this->org->id}{$path}");
    }

    public function test_the_page_lists_accounts_and_pending_invites(): void
    {
        $results = User::factory()->create(['name' => 'Rae Results']);
        $this->org->addAccount($results, ['view', 'results']);
        AccountInviteController::invite('new@example.com', $this->org, ['access' => ['view']]);

        $this->actingAs($this->organiser)->get($this->url('/accounts'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Organisation/Accounts')
                ->where('organisation.tabs.2.current', true)
                ->where('accounts', fn ($accounts) => collect($accounts)->map(fn ($a) => [$a['name'], $a['access'], $a['owner'], $a['you']])->all() === [
                    ['Ash Admin', ['admin'], false, true],
                    ['Olly Owner', [], true, false],
                    ['Rae Results', ['view', 'results'], false, false],
                ])
                ->where('invites', fn ($invites) => collect($invites)->pluck('email')->all() === ['new@example.com'])
                ->where('accessTypes.Organisation', ['admin' => 'Admin'])
                ->has('accessTypes.Competition.results'));
    }

    public function test_inviting_someone_emails_them_once(): void
    {
        $invite = fn (string $email) => $this->actingAs($this->organiser)
            ->post($this->url('/accounts'), ['email' => $email, 'access' => ['view', 'results']]);

        $invite('new@example.com')->assertSessionHasNoErrors()->assertSessionHas('success', 'Invite sent');
        Mail::assertSent(AccountInviteMail::class, fn ($mail) => $mail->hasTo('new@example.com'));
        $this->assertSame(['view', 'results'], AccountInvite::sole()->details['access']);

        $invite('new@example.com')->assertSessionHasErrors(['email' => "They've already been invited."]);
        $invite($this->owner->email)->assertSessionHasErrors(['email' => 'They can already manage this organisation.']);
        $this->actingAs($this->organiser)->post($this->url('/accounts'), ['email' => 'x@example.com', 'access' => []])
            ->assertSessionHasErrors('access');
        $this->assertSame(1, AccountInvite::count());
    }

    public function test_an_accounts_access_can_be_changed(): void
    {
        $member = User::factory()->create();
        $this->org->addAccount($member, 'view');

        $this->actingAs($this->organiser)
            ->post($this->url("/accounts/{$member->id}"), ['access' => ['view', 'serc']])
            ->assertSessionHasNoErrors();
        $this->assertTrue($this->org->canUser($member, 'serc'));
        $this->assertFalse($this->org->canUser($member, 'admin'));

        $outsider = User::factory()->create();
        $this->actingAs($this->organiser)
            ->post($this->url("/accounts/{$outsider->id}"), ['access' => ['view']])
            ->assertSessionHasErrors('access');
        $this->assertFalse($this->org->userBelongsToOrganisation($outsider));
    }

    public function test_an_account_can_be_removed_but_not_the_owner_or_you(): void
    {
        $member = User::factory()->create();
        $this->org->addAccount($member, 'view');

        $this->actingAs($this->organiser)->delete($this->url("/accounts/{$member->id}"))
            ->assertSessionHasNoErrors()->assertSessionHas('success', 'Account removed');
        $this->assertFalse($this->org->userBelongsToOrganisation($member));

        $this->actingAs($this->organiser)->delete($this->url("/accounts/{$this->owner->id}"))->assertSessionHasErrors('account');
        $this->actingAs($this->organiser)->delete($this->url("/accounts/{$this->organiser->id}"))->assertSessionHasErrors('account');
        $this->assertTrue($this->org->userBelongsToOrganisation($this->owner));
        $this->assertTrue($this->org->userBelongsToOrganisation($this->organiser));
    }

    public function test_an_invite_can_be_cancelled_but_only_this_organisations(): void
    {
        AccountInviteController::invite('new@example.com', $this->org, ['access' => ['view']]);
        $ours = AccountInvite::sole();

        $other = new Organisation();
        $other->name = 'Swansea';
        $other->save();
        AccountInviteController::invite('theirs@example.com', $other, ['access' => ['view']]);
        $theirs = AccountInvite::where('email', 'theirs@example.com')->sole();

        $this->actingAs($this->organiser)->delete($this->url("/invites/{$theirs->id}"))->assertNotFound();
        $this->actingAs($this->organiser)->delete($this->url("/invites/{$ours->id}"))
            ->assertSessionHas('success', 'Invite cancelled');

        $this->assertSame(['theirs@example.com'], AccountInvite::pluck('email')->all());
    }

    public function test_members_without_admin_access_cannot_change_accounts(): void
    {
        $member = User::factory()->create();
        $this->org->addAccount($member, 'view');

        $this->actingAs($member)->post($this->url('/accounts'), ['email' => 'new@example.com', 'access' => ['view']])->assertForbidden();
        $this->actingAs($member)->delete($this->url("/accounts/{$this->organiser->id}"))->assertForbidden();
        $this->assertSame(0, AccountInvite::count());
    }
}
