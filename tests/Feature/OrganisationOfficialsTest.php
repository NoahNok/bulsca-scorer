<?php

namespace Tests\Feature;

use App\Mail\CompetitionOfficialAddedMail;
use App\Models\Competition;
use App\Models\Organisation\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
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

    public function test_officials_can_be_searched_and_filtered_by_role(): void
    {
        $comp = $this->makeComp('Comp', $this->org, now()->subWeek());
        $comp->addOfficial(User::factory()->create(['name' => 'Jo Referee', 'email' => 'jo@example.com']), 'referee');
        $comp->addOfficial(User::factory()->create(['name' => 'Sam Official', 'email' => 'sam@club.org']), 'official');

        $this->actingAs($this->admin)
            ->get(route('orgs.officials', [$this->org, 'q' => 'CLUB.ORG']))
            ->assertOk()
            ->assertSee('Sam Official')
            ->assertDontSee('Jo Referee')
            ->assertSee('Showing 1 of 2');

        $this->actingAs($this->admin)
            ->get(route('orgs.officials', [$this->org, 'role' => 'referee']))
            ->assertSee('Jo Referee')
            ->assertDontSee('Sam Official');

        $this->actingAs($this->admin)
            ->get(route('orgs.officials', [$this->org, 'q' => 'nobody']))
            ->assertSee('No officials match');
    }

    public function test_the_last_competition_links_to_its_officials_tab(): void
    {
        $comp = $this->makeComp('Linked Comp', $this->org, now()->subWeek());
        $comp->addOfficial(User::factory()->create(), 'official');

        $this->actingAs($this->admin)
            ->get(route('orgs.officials', $this->org))
            ->assertSee(route('comps.officials', $comp->id));
    }

    public function test_the_csv_export_lists_the_filtered_officials(): void
    {
        $comp = $this->makeComp('Export Comp', $this->org, now()->setDate(2026, 9, 1));
        $comp->addOfficial(User::factory()->create(['name' => 'Jo Referee', 'email' => 'jo@example.com']), 'referee');
        $comp->addOfficial(User::factory()->create(['name' => 'Sam Official']), 'official');

        $response = $this->actingAs($this->admin)
            ->get(route('orgs.officials.export', [$this->org, 'role' => 'referee']))
            ->assertOk()
            ->assertDownload('test-org-officials.csv');

        $csv = $response->streamedContent();
        $this->assertStringContainsString('Name,Email,Roles,Competitions,"Last competition","Last competition date"', $csv);
        $this->assertStringContainsString('"Jo Referee",jo@example.com,Referee,1,"Export Comp",2026-09-01', $csv);
        $this->assertStringNotContainsString('Sam Official', $csv);

        $member = User::factory()->create();
        $this->org->addAccount($member, 'view');
        $this->actingAs($member)->get(route('orgs.officials.export', $this->org))->assertForbidden();
    }

    public function test_admins_can_add_a_past_official_to_an_upcoming_competition(): void
    {
        Mail::fake();

        $past = $this->makeComp('Past Comp', $this->org, now()->subMonth());
        $upcoming = $this->makeComp('Upcoming Comp', $this->org, now()->addWeek());
        $official = User::factory()->create(['name' => 'Jo Past']);
        $past->addOfficial($official, 'official');

        $this->actingAs($this->admin)
            ->get(route('orgs.officials', $this->org))
            ->assertSee('Add to a competition')
            ->assertViewHas('upcomingCompetitions', fn ($comps) => $comps->pluck('id')->all() === [$upcoming->id]);

        $this->actingAs($this->admin)
            ->from(route('orgs.officials', $this->org))
            ->post(route('orgs.officials.add', $this->org), [
                'user_id' => $official->id,
                'competition_id' => $upcoming->id,
                'role' => 'referee',
            ])
            ->assertRedirect(route('orgs.officials', $this->org))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Jo Past added to Upcoming Comp as a referee');

        $this->assertTrue($upcoming->officialHasRole($official, 'referee'));
        Mail::assertSent(CompetitionOfficialAddedMail::class, fn ($mail) => $mail->hasTo($official->email));

        // adding them again doesn't send another email
        $this->actingAs($this->admin)
            ->post(route('orgs.officials.add', $this->org), [
                'user_id' => $official->id,
                'competition_id' => $upcoming->id,
                'role' => 'official',
            ])
            ->assertSessionHasErrorsIn('addToCompetition', 'competition_id');
        Mail::assertSentCount(1);
    }

    public function test_only_past_officials_and_upcoming_competitions_of_the_organisation_can_be_picked(): void
    {
        Mail::fake();

        $past = $this->makeComp('Past Comp', $this->org, now()->subMonth());
        $upcoming = $this->makeComp('Upcoming Comp', $this->org, now()->addWeek());
        $official = User::factory()->create();
        $past->addOfficial($official, 'official');

        $other = new Organisation();
        $other->name = 'Other Org';
        $other->save();
        $theirs = $this->makeComp('Their Comp', $other, now()->addWeek());

        $post = fn (array $data) => $this->actingAs($this->admin)->post(route('orgs.officials.add', $this->org), $data + ['role' => 'official']);

        $post(['user_id' => User::factory()->create()->id, 'competition_id' => $upcoming->id])
            ->assertSessionHasErrorsIn('addToCompetition', 'user_id');
        $post(['user_id' => $official->id, 'competition_id' => $past->id])
            ->assertSessionHasErrorsIn('addToCompetition', 'competition_id');
        $post(['user_id' => $official->id, 'competition_id' => $theirs->id])
            ->assertSessionHasErrorsIn('addToCompetition', 'competition_id');

        $this->assertFalse($theirs->officials()->exists());
        $this->assertFalse($upcoming->officials()->exists());
        Mail::assertNothingSent();

        $member = User::factory()->create();
        $this->org->addAccount($member, 'view');
        $this->actingAs($member)->post(route('orgs.officials.add', $this->org), [
            'user_id' => $official->id,
            'competition_id' => $upcoming->id,
            'role' => 'official',
        ])->assertForbidden();
    }
}
