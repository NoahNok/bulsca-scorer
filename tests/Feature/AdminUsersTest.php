<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\Organisation\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->get(route('admin.users.index'))
            ->assertRedirect('/');

        $this->actingAs($this->organiser)
            ->get(route('admin.users.show', $this->official))
            ->assertRedirect('/');
    }

    public function test_admins_see_every_user_and_their_account_type(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Olive Organiser')
            ->assertSee('Jamie Judge')
            ->assertSee('olive@example.com')
            ->assertSee('OFFICIAL ONLY')
            ->assertSee('FULL ACCOUNT');
    }

    public function test_users_can_be_searched_by_name_or_email(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['q' => 'olive']))
            ->assertOk()
            ->assertSee('Olive Organiser')
            ->assertDontSee('Jamie Judge');

        $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['q' => 'judges.test']))
            ->assertOk()
            ->assertSee('Jamie Judge')
            ->assertDontSee('Olive Organiser');

        $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['q' => 'nobody-here']))
            ->assertOk()
            ->assertSee('No users match');
    }

    public function test_the_user_page_shows_competitions_organisations_and_officiating(): void
    {
        $this->makeCompetition('Warwick 2026')->addAccount($this->organiser, ['teams', 'results']);
        $this->makeCompetition('Bath 2026')->officials()->attach($this->organiser, ['role' => 'referee']);

        $org = new Organisation();
        $org->name = 'BULSCA';
        $org->save();
        $org->addAccount($this->organiser, 'admin');

        $this->actingAs($this->admin)
            ->get(route('admin.users.show', $this->organiser))
            ->assertOk()
            ->assertSee('Warwick 2026')
            ->assertSee('Teams/Competitors')
            ->assertSee('BULSCA')
            ->assertSee('Bath 2026')
            ->assertSee('REFEREE');
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
            ->get(route('admin.users.index', ['q' => 'jamie']))
            ->assertOk()
            ->assertViewHas('users', fn ($users) => $users->first()->competitions_count === 1);

        $this->actingAs($this->admin)
            ->get(route('admin.users.show', $this->official))
            ->assertOk()
            ->assertSee('Leeds 2026')
            ->assertSee('Via BULSCA (Overview)');
    }

    public function test_the_user_page_has_empty_states(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.users.show', $this->official))
            ->assertOk()
            ->assertSee('No access to any competitions.')
            ->assertSee('Not a member of any organisations.')
            ->assertSee('Not an official at any competitions.');
    }
}
