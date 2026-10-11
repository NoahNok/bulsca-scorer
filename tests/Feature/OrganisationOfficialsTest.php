<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\Organisation\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganisationOfficialsTest extends TestCase
{
    use RefreshDatabase;

    private Organisation $org;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->org = new Organisation();
        $this->org->name = 'Test Org';
        $this->org->save();

        $this->admin = User::factory()->create();
        $this->org->addAccount($this->admin, 'admin');
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

    public function test_admins_see_officials_from_every_competition_with_their_roles(): void
    {
        $older = $this->makeComp('Older Comp', $this->org, now()->subMonth());
        $newer = $this->makeComp('Newer Comp', $this->org, now()->subWeek());

        $both = User::factory()->create(['name' => 'Jo Both']);
        $older->addOfficial($both, 'official');
        $newer->addOfficial($both, 'referee');

        $once = User::factory()->create(['name' => 'Sam Once']);
        $older->addOfficial($once, 'official');

        $officials = $this->org->officials()->keyBy('name');

        $this->assertCount(2, $officials);
        $this->assertEquals(['official', 'referee'], $officials['Jo Both']['roles']->all());
        $this->assertSame(2, $officials['Jo Both']['count']);
        $this->assertSame('Newer Comp', $officials['Jo Both']['last']['name']);
        $this->assertEquals(['official'], $officials['Sam Once']['roles']->all());

        $this->actingAs($this->admin)
            ->get(route('orgs.officials', $this->org))
            ->assertOk()
            ->assertSee('Jo Both')
            ->assertSee('Sam Once')
            ->assertSee('REFEREE')
            ->assertSee('2 competitions');
    }

    public function test_officials_of_other_organisations_are_not_listed(): void
    {
        $other = new Organisation();
        $other->name = 'Other Org';
        $other->save();

        $theirs = $this->makeComp('Their Comp', $other, now());
        $theirs->addOfficial(User::factory()->create(['name' => 'Not Ours']), 'referee');

        $unowned = $this->makeComp('Unowned Comp', null, now());
        $unowned->addOfficial(User::factory()->create(['name' => 'Also Not Ours']), 'official');

        $this->actingAs($this->admin)
            ->get(route('orgs.officials', $this->org))
            ->assertOk()
            ->assertDontSee('Not Ours')
            ->assertSee('No one has officiated');
    }

    public function test_users_without_admin_access_cannot_see_officials(): void
    {
        $member = User::factory()->create();
        $this->org->addAccount($member, 'view');

        $this->actingAs($member)->get(route('orgs.officials', $this->org))->assertForbidden();
        $this->actingAs(User::factory()->create())->get(route('orgs.officials', $this->org))->assertForbidden();
    }
}
