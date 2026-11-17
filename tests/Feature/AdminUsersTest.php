<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\Organisation\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminUsersTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $organiser;
    private User $official;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['name' => 'Site Admin']);
        $this->admin->forceFill(['admin' => true])->save();

        $this->organiser = User::factory()->create(['name' => 'Olive Organiser', 'email' => 'olive@example.com']);

        $this->official = User::factory()->create(['name' => 'Jamie Judge', 'email' => 'jamie@judges.test']);
        $this->official->forceFill(['judge_only' => true])->save();
    }

    private function makeCompetition(string $name): Competition
    {
        $comp = new Competition();
        $comp->name = $name;
        $comp->when = now()->addWeek();
        $comp->where = 'Test Pool';
        $comp->max_lanes = 8;
        $comp->anytimepin = false;
        $comp->seed_per_event = false;
        $comp->heats_per_event = false;
        $comp->use_seeds = true;
        $comp->save();

        return $comp;
    }

    public function test_non_admins_cannot_see_users(): void
    {
        $this->actingAs($this->organiser)
            ->get($this->appUrl('/admin/users'))
            ->assertRedirect('/');

        $this->actingAs($this->organiser)
            ->get($this->appUrl("/admin/users/{$this->official->id}"))
            ->assertRedirect('/');
    }

    public function test_admins_see_every_user_and_their_account_type(): void
    {
        $this->actingAs($this->admin)
            ->get($this->appUrl('/admin/users'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Users')
                ->has('users', 3)
                // by name
                ->where('users.0.name', 'Jamie Judge')
                ->where('users.0.email', 'jamie@judges.test')
                ->where('users.0.officialOnly', true)
                ->where('users.0.admin', false)
                ->where('users.0.url', route('manage.admin.users.show', $this->official))
                ->where('users.1.name', 'Olive Organiser')
                ->where('users.1.officialOnly', false)
                ->where('users.2.name', 'Site Admin')
                ->where('users.2.admin', true)
                ->where('pagination.total', 3));
    }

    public function test_users_can_be_searched_by_name_or_email(): void
    {
        $this->actingAs($this->admin)
            ->get($this->appUrl('/admin/users?q=olive'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('q', 'olive')
                ->has('users', 1)
                ->where('users.0.name', 'Olive Organiser'));

        $this->actingAs($this->admin)
            ->get($this->appUrl('/admin/users?q=judges.test'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('users', 1)
                ->where('users.0.name', 'Jamie Judge'));

        $this->actingAs($this->admin)
            ->get($this->appUrl('/admin/users?q=nobody-here'))
            ->assertInertia(fn (Assert $page) => $page->has('users', 0));
    }

    public function test_the_user_page_shows_competitions_organisations_and_officiating(): void
    {
        $warwick = $this->makeCompetition('Warwick 2026');
        $warwick->addAccount($this->organiser, ['teams', 'results']);
        $bath = $this->makeCompetition('Bath 2026');
        $bath->officials()->attach($this->organiser, ['role' => 'referee']);

        $org = new Organisation();
        $org->name = 'BULSCA';
        $org->save();
        $org->addAccount($this->organiser, 'admin');

        $this->actingAs($this->admin)
            ->get($this->appUrl("/admin/users/{$this->organiser->id}"))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/User')
                ->where('user.name', 'Olive Organiser')
                ->where('user.email', 'olive@example.com')
                ->where('user.officialOnly', false)
                ->has('competitions', 1)
                ->where('competitions.0.name', 'Warwick 2026')
                ->where('competitions.0.url', route('manage.admin.competitions.show', $warwick))
                ->where('competitions.0.access', fn ($access) => collect($access)->contains('Teams/Competitors'))
                ->has('organisations', 1)
                ->where('organisations.0.name', 'BULSCA')
                ->where('organisations.0.url', route('manage.organisations.show', $org))
                ->has('officiating', 1)
                ->where('officiating.0.name', 'Bath 2026')
                ->where('officiating.0.url', route('manage.admin.competitions.show', $bath))
                ->where('officiating.0.roleName', 'Referee')
                ->where('submissions', null));
    }

    public function test_competitions_run_by_a_users_organisation_count_towards_their_competitions(): void
    {
        $org = new Organisation();
        $org->name = 'BULSCA';
        $org->save();
        $org->addAccount($this->official, 'view');

        $comp = $this->makeCompetition('Leeds 2026');
        $comp->organisation = $org->id;
        $comp->save();

        $this->actingAs($this->admin)
            ->get($this->appUrl('/admin/users?q=jamie'))
            ->assertInertia(fn (Assert $page) => $page->where('users.0.competitions', 1));

        $this->actingAs($this->admin)
            ->get($this->appUrl("/admin/users/{$this->official->id}"))
            ->assertInertia(fn (Assert $page) => $page
                ->where('competitions.0.name', 'Leeds 2026')
                ->where('competitions.0.via', ['Via BULSCA (Overview)']));
    }

    public function test_a_user_page_with_nothing_on_it_sends_empty_lists(): void
    {
        $this->official->forceFill(['email_verified_at' => null])->save();

        $this->actingAs($this->admin)
            ->get($this->appUrl("/admin/users/{$this->official->id}"))
            ->assertInertia(fn (Assert $page) => $page
                ->has('competitions', 0)
                ->has('organisations', 0)
                ->has('officiating', 0)
                // an Official made by an Admin hasn't signed in until they verify
                ->where('user.neverSignedIn', true));
    }

    public function test_users_are_paged_keeping_the_search(): void
    {
        foreach (range(1, 25) as $i) {
            User::factory()->create(['name' => sprintf('Paged %02d', $i)]);
        }

        $this->actingAs($this->admin)
            ->get($this->appUrl('/admin/users?q=paged'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('users', 25)
                ->where('pagination.last', 1));

        User::factory()->create(['name' => 'Paged 26']);

        $this->actingAs($this->admin)
            ->get($this->appUrl('/admin/users?q=paged'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('users', 25)
                ->where('pagination.total', 26)
                ->where('pagination.next', fn ($url) => str_contains($url, 'page=2') && str_contains($url, 'q=paged')));

        $this->actingAs($this->admin)
            ->get($this->appUrl('/admin/users?q=paged&page=2'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('users', 1)
                ->where('users.0.name', 'Paged 26')
                ->where('pagination.prev', fn ($url) => str_contains($url, 'q=paged')));
    }

    public function test_admins_can_make_an_official_only_account_a_full_one(): void
    {
        $this->actingAs($this->admin)
            ->from($this->appUrl("/admin/users/{$this->official->id}"))
            ->post($this->appUrl("/admin/users/{$this->official->id}/full-account"))
            ->assertRedirect($this->appUrl("/admin/users/{$this->official->id}"))
            ->assertSessionHas('success', 'Jamie Judge now has a full account');

        $this->assertFalse($this->official->fresh()->isJudgeOnly());
    }

    public function test_non_admins_cannot_make_full_accounts(): void
    {
        $this->actingAs($this->organiser)
            ->post($this->appUrl("/admin/users/{$this->official->id}/full-account"))
            ->assertRedirect('/');

        $this->assertTrue($this->official->fresh()->isJudgeOnly());
    }

    public function test_non_admins_cannot_create_users(): void
    {
        $this->actingAs($this->organiser)
            ->get($this->appUrl('/admin/users/create'))
            ->assertRedirect('/');

        $this->actingAs($this->organiser)
            ->post($this->appUrl('/admin/users'), ['name' => 'Pool tablet', 'email' => 'pool1@temp.local'])
            ->assertRedirect('/');

        $this->assertDatabaseMissing('users', ['email' => 'pool1@temp.local']);
    }

    public function test_admins_see_the_new_user_form(): void
    {
        $this->actingAs($this->admin)
            ->get($this->appUrl('/admin/users/create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Admin/NewUser'));
    }

    public function test_a_new_user_is_official_only_and_verified_without_any_email(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->admin)
            ->post($this->appUrl('/admin/users'), [
                'name' => 'Pool 1 tablet',
                'email' => 'pool1@temp.local',
                'password' => '',
                'full_account' => 0,
            ]);

        $user = User::where('email', 'pool1@temp.local')->firstOrFail();

        $response->assertRedirect($this->appUrl("/admin/users/{$user->id}"))
            ->assertSessionHas('success', 'Pool 1 tablet added');

        $this->assertSame('Pool 1 tablet', $user->name);
        $this->assertTrue($user->isJudgeOnly());
        $this->assertTrue($user->hasVerifiedEmail());
        $this->assertFalse((bool) $user->isAdmin());
        Mail::assertNothingSent();

        $this->actingAs($this->admin)
            ->get($this->appUrl("/admin/users/{$user->id}"))
            ->assertInertia(fn (Assert $page) => $page->where('user.neverSignedIn', false));
    }

    public function test_a_new_user_can_be_a_full_account_with_a_password(): void
    {
        $this->actingAs($this->admin)
            ->post($this->appUrl('/admin/users'), [
                'name' => 'Desk',
                'email' => 'desk@temp.local',
                'password' => 'a-long-password-123',
                'full_account' => 1,
            ])
            ->assertSessionHasNoErrors();

        $user = User::where('email', 'desk@temp.local')->firstOrFail();
        $this->assertFalse($user->isJudgeOnly());
        $this->assertTrue($user->hasVerifiedEmail());
        $this->assertTrue(Hash::check('a-long-password-123', $user->password));
    }

    public function test_a_new_user_needs_an_unused_email(): void
    {
        $this->actingAs($this->admin)
            ->from($this->appUrl('/admin/users/create'))
            ->post($this->appUrl('/admin/users'), ['name' => 'Copy', 'email' => 'olive@example.com'])
            ->assertSessionHasErrors('email');

        $this->assertSame(1, User::where('email', 'olive@example.com')->count());
    }
}
