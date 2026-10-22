<?php

namespace Tests\Feature;

use App\Mail\CompetitionOfficialAddedMail;
use App\Models\Competition;
use App\Models\Organisation\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
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

    public function test_competitions_and_users_with_officials_can_be_deleted(): void
    {
        $official = User::factory()->create();
        $this->comp->addOfficial($official, 'official');

        $other = User::factory()->create();
        $this->comp->addOfficial($other, 'referee');

        $official->delete();
        $this->assertFalse($this->comp->officials()->whereKey($official->id)->exists());

        $this->comp->delete();
        $this->assertNull(Competition::find($this->comp->id));
        $this->assertNotNull($other->fresh());
    }

    private function makeComp(string $name, ?Organisation $org, $when): Competition
    {
        $comp = new Competition();
        $comp->name = $name;
        $comp->when = $when;
        $comp->where = 'Test Pool';
        $comp->max_lanes = 8;
        $comp->anytimepin = false;
        $comp->seed_per_event = false;
        $comp->heats_per_event = false;
        $comp->use_seeds = true;
        $comp->organisation = $org?->id;
        $comp->save();

        return $comp;
    }

    private function makeOrg(): Organisation
    {
        $org = new Organisation();
        $org->name = 'Test Org';
        $org->save();

        $this->comp->organisation = $org->id;
        $this->comp->save();

        return $org;
    }

    public function test_opening_the_competition_on_digital_judge_shows_the_official_online(): void
    {
        $official = User::factory()->create(['judge_only' => true]);
        $this->comp->addOfficial($official);

        $this->actingAs($this->admin)->get(route('comps.officials', $this->comp))
            ->assertSee("Hasn't opened it on Digital Judge yet")
            ->assertSee('Resend email')
            ->assertDontSee('ONLINE');

        $this->actingAs($official)
            ->get(route('judge.competition', ['competition' => $this->comp->getSlug()]))
            ->assertOk();

        $this->actingAs($this->admin)->get(route('comps.officials', $this->comp))
            ->assertSee('ONLINE')
            ->assertDontSee('Resend email');

        $this->travel(2)->hours();

        $this->actingAs($this->admin)->get(route('comps.officials', $this->comp))
            ->assertDontSee('ONLINE')
            ->assertSee('Last seen 2 hours ago');
    }

    public function test_last_seen_is_only_written_once_a_minute(): void
    {
        $official = User::factory()->create();
        $this->comp->addOfficial($official);

        $this->freezeSecond();
        $this->comp->markOfficialSeen($official);
        $first = $this->comp->officials()->sole()->pivot->last_seen_at;

        $this->travel(30)->seconds();
        $this->comp->markOfficialSeen($official);
        $this->assertTrue($this->comp->officials()->sole()->pivot->last_seen_at->eq($first));

        $this->travel(1)->minutes();
        $this->comp->markOfficialSeen($official);
        $this->assertTrue($this->comp->officials()->sole()->pivot->last_seen_at->gt($first));
    }

    public function test_resending_the_email_is_rate_limited(): void
    {
        $official = User::factory()->create(['judge_only' => true, 'email_verified_at' => null]);
        $this->comp->addOfficial($official, 'referee');

        $this->actingAs($this->admin)
            ->post(route('comps.officials.resend', [$this->comp, $official]))
            ->assertSessionHas('success');

        Mail::assertSent(CompetitionOfficialAddedMail::class, fn ($mail) => $mail->hasTo($official->email) && $mail->newAccount && $mail->role === 'referee');

        $this->actingAs($this->admin)
            ->post(route('comps.officials.resend', [$this->comp, $official]))
            ->assertSessionHas('alert-error');

        Mail::assertSent(CompetitionOfficialAddedMail::class, 1);

        $this->actingAs($this->admin)
            ->post(route('comps.officials.resend', [$this->comp, User::factory()->create()]))
            ->assertNotFound();
    }

    public function test_warns_when_there_is_no_referee(): void
    {
        $official = User::factory()->create();
        $this->comp->addOfficial($official);

        $this->actingAs($this->admin)->get(route('comps.officials', $this->comp))
            ->assertSee('No referee yet');

        $this->comp->setOfficialRole($official, 'referee');

        $this->actingAs($this->admin)->get(route('comps.officials', $this->comp))
            ->assertDontSee('No referee yet')
            ->assertViewHas('officials', fn ($officials) => $officials[0]['onlyReferee']
                && str_contains($officials[0]['removeMessage'], "They're the only referee"));

        $this->comp->addOfficial(User::factory()->create(), 'referee');

        $this->actingAs($this->admin)->get(route('comps.officials', $this->comp))
            ->assertViewHas('officials', fn ($officials) => $officials->every(fn ($official) => !$official['onlyReferee']));
    }

    public function test_the_printable_qr_code_links_to_the_join_link(): void
    {
        $this->actingAs($this->admin)->get(route('dj.qrs', $this->comp))->assertNotFound();

        $this->comp->digitalJudgePin = 123456;
        $this->comp->digitalJudgeEnabled = true;
        $this->comp->save();

        $this->actingAs($this->admin)->get(route('comps.officials', $this->comp))
            ->assertSee(route('dj.qrs', $this->comp));

        $this->actingAs($this->admin)->get(route('dj.qrs', $this->comp))
            ->assertOk()
            ->assertSee(route('judge.join-link', 123456))
            ->assertSee('<svg', false);
    }

    public function test_the_overview_pin_points_officials_at_the_new_judge_app(): void
    {
        $this->comp->digitalJudgePin = 123456;
        $this->comp->digitalJudgeHeadPin = 654321;
        $this->comp->digitalJudgeEnabled = true;
        $this->comp->save();

        $this->actingAs($this->admin)->get(route('comps.view', $this->comp))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('digitalJudge.pin', '123456')
                ->where('digitalJudge.judgeUrl', route('judge.index'))
                ->where('digitalJudge.qrUrl', route('dj.qrs', $this->comp)))
            // referees get the Referee role instead of a PIN
            ->assertDontSee('654321');
    }

    public function test_the_join_link_adds_the_official(): void
    {
        $this->comp->digitalJudgePin = 123456;
        $this->comp->digitalJudgeEnabled = true;
        $this->comp->save();

        $official = User::factory()->create(['judge_only' => true]);

        // signed out, they come back to the link after signing in
        $this->get(route('judge.join-link', 123456))->assertRedirect(route('judge.login'));
        $this->assertSame(route('judge.join-link', 123456), session('url.intended'));

        $this->actingAs($official)->get(route('judge.join-link', 123456))
            ->assertRedirect(route('judge.competition', ['competition' => $this->comp->getSlug()]));
        $this->assertTrue($this->comp->officialHasRole($official, 'official'));

        $this->actingAs($official)->get(route('judge.join-link', 654321))
            ->assertRedirect(route('judge.index'))
            ->assertSessionHasErrors('pin');
    }

    public function test_joining_by_pin_adds_the_official(): void
    {
        $this->comp->digitalJudgePin = 123456;
        $this->comp->digitalJudgeEnabled = true;
        $this->comp->save();

        $official = User::factory()->create(['judge_only' => true]);

        $this->actingAs($official)->post(route('judge.join-competition'), ['pin' => 123456])
            ->assertRedirect(route('judge.competition', ['competition' => $this->comp->getSlug()]));
        $this->assertTrue($this->comp->officialHasRole($official, 'official'));
    }

    public function test_too_many_wrong_pins_locks_joining_for_a_minute(): void
    {
        $this->comp->digitalJudgePin = 123456;
        $this->comp->digitalJudgeEnabled = true;
        $this->comp->save();

        $official = User::factory()->create(['judge_only' => true]);

        foreach (range(1, 5) as $guess) {
            $this->actingAs($official)->from(route('judge.index'))
                ->post(route('judge.join-competition'), ['pin' => 100000 + $guess])
                ->assertSessionHasErrors(['pin' => 'No competition found.']);
        }

        // even the right PIN, so a guess that lands after the limit doesn't get in
        $this->actingAs($official)->from(route('judge.index'))
            ->post(route('judge.join-competition'), ['pin' => 123456])
            ->assertSessionHasErrors(['pin' => 'Too many attempts, try again in a minute.']);

        $this->actingAs($official)->get(route('judge.join-link', 123456))
            ->assertRedirect(route('judge.index'))
            ->assertSessionHasErrors(['pin' => 'Too many attempts, try again in a minute.']);

        $this->assertFalse($this->comp->officials()->whereKey($official->id)->exists());

        $this->travel(61)->seconds();

        $this->actingAs($official)->post(route('judge.join-competition'), ['pin' => 123456])
            ->assertRedirect(route('judge.competition', ['competition' => $this->comp->getSlug()]));
    }

    public function test_right_pins_dont_count_towards_the_limit(): void
    {
        $this->comp->digitalJudgePin = 123456;
        $this->comp->digitalJudgeEnabled = true;
        $this->comp->save();

        $official = User::factory()->create(['judge_only' => true]);

        foreach (range(1, 6) as $attempt) {
            $this->actingAs($official)->post(route('judge.join-competition'), ['pin' => 123456])
                ->assertRedirect(route('judge.competition', ['competition' => $this->comp->getSlug()]));
        }
    }

    public function test_officials_cannot_join_by_pin_or_link_while_digital_judge_is_off(): void
    {
        $this->comp->digitalJudgePin = 123456;
        $this->comp->digitalJudgeEnabled = false;
        $this->comp->save();

        $official = User::factory()->create(['judge_only' => true]);

        $this->actingAs($official)->from(route('judge.index'))
            ->post(route('judge.join-competition'), ['pin' => 123456])
            ->assertRedirect(route('judge.index'))
            ->assertSessionHasErrors(['pin' => 'No competition found.']);

        $this->actingAs($official)->get(route('judge.join-link', 123456))
            ->assertRedirect(route('judge.index'))
            ->assertSessionHasErrors(['pin' => 'No competition found.']);

        $this->assertFalse($this->comp->officials()->whereKey($official->id)->exists());
    }

    public function test_the_add_modal_suggests_officials_from_the_organisations_other_competitions(): void
    {
        $org = $this->makeOrg();
        $previous = $this->makeComp('Previous Comp', $org, now()->subMonth());
        $elsewhere = $this->makeComp('Other Org Comp', null, now()->subMonth());

        $past = User::factory()->create(['name' => 'Pat Past', 'email' => 'pat.past@example.com']);
        $previous->addOfficial($past);

        $already = User::factory()->create(['name' => 'Alex Already']);
        $previous->addOfficial($already);
        $this->comp->addOfficial($already);

        $stranger = User::factory()->create(['name' => 'Sam Stranger']);
        $elsewhere->addOfficial($stranger);

        $this->actingAs($this->admin)->get(route('comps.officials', $this->comp))
            ->assertViewHas('suggestions', fn ($suggestions) => $suggestions->pluck('name')->all() === ['Pat Past']
                && $suggestions[0]['email'] === 'p***@example.com'
                && $suggestions[0]['last'] === 'Previous Comp')
            ->assertViewHas('previousCompetitions', fn ($comps) => $comps->pluck('id')->all() === [$previous->id]);
    }

    public function test_copying_officials_from_another_competition_keeps_their_roles(): void
    {
        $org = $this->makeOrg();
        $previous = $this->makeComp('Previous Comp', $org, now()->subMonth());

        $referee = User::factory()->create();
        $previous->addOfficial($referee, 'referee');
        $official = User::factory()->create();
        $previous->addOfficial($official, 'official');

        $already = User::factory()->create();
        $previous->addOfficial($already, 'referee');
        $this->comp->addOfficial($already, 'official');

        $this->actingAs($this->admin)
            ->post(route('comps.officials.copy', $this->comp), ['from' => $previous->id])
            ->assertSessionHas('success', 'Added 2 officials from Previous Comp');

        $this->assertTrue($this->comp->officialHasRole($referee, 'referee'));
        $this->assertTrue($this->comp->officialHasRole($official, 'official'));
        // already here, so their role is left alone and they aren't emailed
        $this->assertTrue($this->comp->officialHasRole($already, 'official'));
        Mail::assertSent(CompetitionOfficialAddedMail::class, 2);
        Mail::assertNotSent(CompetitionOfficialAddedMail::class, fn ($mail) => $mail->hasTo($already->email));
    }

    public function test_cannot_copy_officials_from_another_organisations_competition(): void
    {
        $this->makeOrg();
        $elsewhere = $this->makeComp('Other Org Comp', null, now()->subMonth());
        $elsewhere->addOfficial(User::factory()->create());

        $this->actingAs($this->admin)
            ->post(route('comps.officials.copy', $this->comp), ['from' => $elsewhere->id])
            ->assertSessionHasErrorsIn('copyOfficials', 'from');

        $this->actingAs($this->admin)
            ->post(route('comps.officials.copy', $this->comp), ['from' => $this->comp->id])
            ->assertSessionHasErrorsIn('copyOfficials', 'from');

        $this->assertSame(0, $this->comp->officials()->count());
        Mail::assertNothingSent();
    }

    public function test_the_remove_confirmation_says_what_they_leave_behind(): void
    {
        $official = User::factory()->create();
        $this->comp->addOfficial($official);
        $this->comp->addOfficial(User::factory()->create(), 'referee');

        foreach (['SUBMITTED', 'APPEALED', 'SUBMITTED'] as $status) {
            DB::table('violation_submissions')->insert([
                'id' => Str::uuid(),
                'competition_id' => $this->comp->id,
                'entity_type' => 'team', 'entity_id' => 1,
                'event_type' => 'serc', 'event_id' => 1,
                'submitted_type' => 'dq', 'submitted_id' => 1,
                'status' => $status,
                'details' => 'Saw it',
                'submitter_id' => $official->id,
                'submitter_position' => 'Judge',
            ]);
        }

        $this->actingAs($this->admin)->get(route('comps.officials', $this->comp))
            ->assertViewHas('officials', fn ($officials) => str_ends_with(
                $officials->firstWhere('id', $official->id)['removeMessage'],
                'Their 3 DQ/penalty submissions (2 waiting for review) will stay.'
            ));
    }
}
