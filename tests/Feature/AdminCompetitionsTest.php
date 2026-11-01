<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\Organisation\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminCompetitionsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->forceFill(['admin' => true])->save();
    }

    private function competition(string $name, string $when, ?Organisation $org = null): Competition
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

    public function test_non_admins_cannot_see_admin(): void
    {
        $this->actingAs(User::factory()->create())
            ->get($this->appUrl('/admin'))
            ->assertRedirect('/');
    }

    public function test_admins_see_every_competition_newest_first_with_its_organisation(): void
    {
        $org = new Organisation();
        $org->name = 'BULSCA';
        $org->save();

        $old = $this->competition('Bath 2024', '2024-02-01 09:00', $org);
        $new = $this->competition('Warwick 2026', '2026-02-01 09:00');

        $this->actingAs($this->admin)
            ->get($this->appUrl('/admin'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Competitions')
                ->where('q', '')
                ->has('competitions', 2)
                ->where('competitions.0.name', 'Warwick 2026')
                ->where('competitions.0.url', route('manage.admin.competitions.show', $new))
                ->where('competitions.0.organisationName', null)
                ->where('competitions.1.name', 'Bath 2024')
                ->where('competitions.1.organisationName', 'BULSCA')
                ->where('pagination.total', 2)
                ->where('pagination.next', null));
    }

    public function test_competitions_can_be_searched_by_name(): void
    {
        $this->competition('Bath 2024', '2024-02-01 09:00');
        $this->competition('Warwick 2026', '2026-02-01 09:00');

        $this->actingAs($this->admin)
            ->get($this->appUrl('/admin?q=warw'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('q', 'warw')
                ->has('competitions', 1)
                ->where('competitions.0.name', 'Warwick 2026'));
    }

    public function test_competitions_are_paged_keeping_the_search(): void
    {
        foreach (range(1, 26) as $i) {
            $this->competition("Comp {$i}", now()->subDays($i)->toDateTimeString());
        }

        $this->actingAs($this->admin)
            ->get($this->appUrl('/admin?q=comp'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('competitions', 25)
                ->where('pagination.current', 1)
                ->where('pagination.last', 2)
                ->where('pagination.total', 26)
                ->where('pagination.prev', null)
                ->where('pagination.next', fn ($url) => str_contains($url, 'page=2') && str_contains($url, 'q=comp')));
    }
}
