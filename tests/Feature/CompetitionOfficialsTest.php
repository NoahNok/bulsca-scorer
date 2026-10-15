<?php

namespace Tests\Feature;

use App\Mail\CompetitionOfficialAddedMail;
use App\Models\Competition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CompetitionOfficialsTest extends TestCase
{
    use RefreshDatabase;

    private Competition $comp;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

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

        $this->admin = User::factory()->create();
        $this->comp->addAccount($this->admin, 'admin');
    }

    public function test_admins_see_the_officials_page(): void
    {
        $official = User::factory()->create(['name' => 'Jo Official']);
        $this->comp->addOfficial($official, 'referee');

        $this->actingAs($this->admin)
            ->get(route('comps.officials', $this->comp))
            ->assertOk()
            ->assertSee('Jo Official')
            ->assertSee('REFEREE');
    }

    public function test_users_without_admin_access_cannot_manage_officials(): void
    {
        $viewer = User::factory()->create();
        $this->comp->addAccount($viewer, 'view');

        $this->actingAs($viewer)->get(route('comps.officials', $this->comp))->assertForbidden();
        $this->actingAs($viewer)->post(route('comps.officials.store', $this->comp), [
            'email' => 'someone@example.com',
            'name' => 'Someone',
            'role' => 'official',
        ])->assertForbidden();
    }

    public function test_adding_an_existing_user_attaches_them_and_emails_them_once(): void
    {
        $official = User::factory()->create();

        $this->actingAs($this->admin)->post(route('comps.officials.store', $this->comp), [
            'email' => $official->email,
            'role' => 'referee',
        ])->assertSessionHasNoErrors();

        $this->assertTrue($this->comp->officialHasRole($official, 'referee'));
        Mail::assertSent(CompetitionOfficialAddedMail::class, fn ($mail) => $mail->hasTo($official->email) && !$mail->newAccount);

        $this->actingAs($this->admin)->post(route('comps.officials.store', $this->comp), [
            'email' => $official->email,
            'role' => 'official',
        ])->assertSessionHasErrorsIn('addOfficial', 'email');

        // adding them again doesn't change their role or email them again
        $this->assertTrue($this->comp->officialHasRole($official, 'referee'));
        Mail::assertSent(CompetitionOfficialAddedMail::class, 1);
    }

    public function test_search_finds_people_by_name_or_email_without_revealing_emails(): void
    {
        $jo = User::factory()->create(['name' => 'Jo Bloggs', 'email' => 'jo.bloggs@example.com']);
        $this->comp->addOfficial(User::factory()->create(['name' => 'Jo Official']));

        $byName = $this->actingAs($this->admin)
            ->getJson(route('comps.officials.search', [$this->comp, 'q' => 'Jo']))
            ->assertOk()
            ->assertJsonCount(2, 'results')
            ->assertJsonPath('exactEmail', false)
            ->json('results');

        $this->assertSame('Jo Bloggs', $byName[0]['name']);
        $this->assertSame('j***@example.com', $byName[0]['email']);
        $this->assertFalse($byName[0]['official']);
        $this->assertTrue($byName[1]['official']);

        // typing the whole address shows it, since they already know it
        $this->actingAs($this->admin)
            ->getJson(route('comps.officials.search', [$this->comp, 'q' => 'jo.bloggs@example.com']))
            ->assertJsonPath('results.0.id', $jo->id)
            ->assertJsonPath('results.0.email', 'jo.bloggs@example.com')
            ->assertJsonPath('exactEmail', true);

        // LIKE wildcards are searched for literally
        $this->actingAs($this->admin)
            ->getJson(route('comps.officials.search', [$this->comp, 'q' => '%%']))
            ->assertJsonCount(0, 'results');
    }

    public function test_adding_someone_picked_from_the_search(): void
    {
        $official = User::factory()->create();

        $this->actingAs($this->admin)->post(route('comps.officials.store', $this->comp), [
            'user_id' => $official->id,
            'email' => 'whatever was left in the search box',
            'role' => 'official',
        ])->assertSessionHasNoErrors();

        $this->assertTrue($this->comp->officialHasRole($official, 'official'));
        Mail::assertSent(CompetitionOfficialAddedMail::class, fn ($mail) => $mail->hasTo($official->email));
    }

    public function test_adding_an_unknown_email_creates_a_judge_only_account(): void
    {
        $this->actingAs($this->admin)->post(route('comps.officials.store', $this->comp), [
            'email' => 'new.official@example.com',
            'name' => 'New Official',
            'role' => 'official',
        ])->assertSessionHasNoErrors();

        $official = User::where('email', 'new.official@example.com')->firstOrFail();

        $this->assertSame('New Official', $official->name);
        $this->assertTrue($official->isJudgeOnly());
        $this->assertFalse($official->hasVerifiedEmail());
        $this->assertTrue($this->comp->officialHasRole($official, 'official'));
        Mail::assertSent(CompetitionOfficialAddedMail::class, fn ($mail) => $mail->newAccount);
    }

    public function test_adding_an_unknown_email_needs_a_name(): void
    {
        $this->actingAs($this->admin)->post(route('comps.officials.store', $this->comp), [
            'email' => 'new.official@example.com',
            'role' => 'official',
        ])->assertSessionHasErrorsIn('addOfficial', 'name');

        $this->assertDatabaseMissing('users', ['email' => 'new.official@example.com']);
        Mail::assertNothingSent();
    }

    public function test_admins_can_change_an_officials_role(): void
    {
        $official = User::factory()->create();
        $this->comp->addOfficial($official);

        $this->actingAs($this->admin)
            ->post(route('comps.officials.update', [$this->comp, $official]), ['role' => 'referee'])
            ->assertSessionHasNoErrors();

        $this->assertTrue($this->comp->officialHasRole($official, 'referee'));

        $this->actingAs($this->admin)
            ->post(route('comps.officials.update', [$this->comp, $official]), ['role' => 'head-honcho'])
            ->assertSessionHasErrors('role');
    }

    public function test_removed_officials_lose_access_to_the_competition(): void
    {
        $official = User::factory()->create(['judge_only' => true]);
        $this->comp->addOfficial($official);

        $this->actingAs($official)
            ->get(route('judge.competition', ['competition' => $this->comp->getSlug()]))
            ->assertOk();

        $this->actingAs($this->admin)
            ->delete(route('comps.officials.destroy', [$this->comp, $official]))
            ->assertSessionHasNoErrors();

        $this->assertFalse($this->comp->officials()->whereKey($official->id)->exists());

        $this->actingAs($official)
            ->get(route('judge.competition', ['competition' => $this->comp->getSlug()]))
            ->assertForbidden();
    }

    public function test_cannot_change_or_remove_someone_who_isnt_an_official_here(): void
    {
        $stranger = User::factory()->create();

        $this->actingAs($this->admin)
            ->post(route('comps.officials.update', [$this->comp, $stranger]), ['role' => 'referee'])
            ->assertNotFound();

        $this->actingAs($this->admin)
            ->delete(route('comps.officials.destroy', [$this->comp, $stranger]))
            ->assertNotFound();
    }

    public function test_registering_claims_an_account_an_admin_made(): void
    {
        $this->actingAs($this->admin)->post(route('comps.officials.store', $this->comp), [
            'email' => 'new.official@example.com',
            'name' => 'Placeholder Name',
            'role' => 'official',
        ]);
        auth()->logout();

        $this->post(route('judge.register.post'), [
            'name' => 'Real Name',
            'email' => 'new.official@example.com',
            'password' => 'a-good-password-123',
            'password_confirmation' => 'a-good-password-123',
        ])->assertSessionHasNoErrors();

        $official = User::where('email', 'new.official@example.com')->sole();

        // nothing changes until they prove they own the email with the PIN
        $this->assertSame('Placeholder Name', $official->name);

        $this->post(route('judge.login.post'), [
            'email' => $official->email,
            'step' => 'pin',
            'pin' => $official->createOneTimePassword()->password,
        ]);

        $this->assertSame('Real Name', $official->refresh()->name);
        $this->assertTrue($this->comp->officials()->whereKey($official->id)->exists());
    }

    public function test_registering_with_a_verified_email_still_fails(): void
    {
        $existing = User::factory()->create(['judge_only' => true]);

        $this->post(route('judge.register.post'), [
            'name' => 'Someone Else',
            'email' => $existing->email,
            'password' => 'a-good-password-123',
            'password_confirmation' => 'a-good-password-123',
        ])->assertSessionHasErrors('email');
    }
}
