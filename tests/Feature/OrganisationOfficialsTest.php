<?php

namespace Tests\Feature;

use App\Mail\CompetitionOfficialAddedMail;
use App\Models\Competition;
use App\Models\Organisation\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * An organisation's Officials (Svelte): everyone who has officiated at its competitions, the CSV export,
 * and adding one of them to an upcoming competition.
 */
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
            ->get(route('manage.organisations.officials', $this->org))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Organisation/Officials')
                ->where('organisation.tabs.3.current', true)
                ->where('officials.0.name', 'Jo Both')
                ->where('officials.0.roles', ['official', 'referee'])
                ->where('officials.0.count', 2)
                ->where('officials.0.last.name', 'Newer Comp')
                ->where('officials.0.last.when', $newer->when->toDateString())
                ->where('officials.1.name', 'Sam Once')
                ->where('roles', ['official' => 'Official', 'referee' => 'Referee']));
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
            ->get(route('manage.organisations.officials', $this->org))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('officials', []));
    }

    public function test_users_without_admin_access_cannot_see_officials(): void
    {
        $member = User::factory()->create();
        $this->org->addAccount($member, 'view');

        $this->actingAs($member)->get(route('manage.organisations.officials', $this->org))->assertForbidden();
        $this->actingAs(User::factory()->create())->get(route('manage.organisations.officials', $this->org))->assertForbidden();
    }

    public function test_the_last_competition_links_to_its_officials_tab(): void
    {
        $comp = $this->makeComp('Linked Comp', $this->org, now()->subWeek());
        $comp->addOfficial(User::factory()->create(), 'official');

        $this->actingAs($this->admin)
            ->get(route('manage.organisations.officials', $this->org))
            ->assertInertia(fn (Assert $page) => $page->where('officials.0.last.url', route('manage.competitions.officials', $comp->id)));
    }

    public function test_the_csv_export_lists_the_filtered_officials(): void
    {
        $comp = $this->makeComp('Export Comp', $this->org, now()->setDate(2026, 9, 1));
        $comp->addOfficial(User::factory()->create(['name' => 'Jo Referee', 'email' => 'jo@example.com']), 'referee');
        $comp->addOfficial(User::factory()->create(['name' => 'Sam Official']), 'official');

        $response = $this->actingAs($this->admin)
            ->get(route('manage.organisations.officials.export', [$this->org, 'role' => 'referee']))
            ->assertOk()
            ->assertDownload('test-org-officials.csv');

        $csv = $response->streamedContent();
        $this->assertStringContainsString('Name,Email,Roles,Competitions,"Last competition","Last competition date"', $csv);
        $this->assertStringContainsString('"Jo Referee",jo@example.com,Referee,1,"Export Comp",2026-09-01', $csv);
        $this->assertStringNotContainsString('Sam Official', $csv);

        $member = User::factory()->create();
        $this->org->addAccount($member, 'view');
        $this->actingAs($member)->get(route('manage.organisations.officials.export', $this->org))->assertForbidden();
    }

    public function test_admins_can_add_a_past_official_to_an_upcoming_competition(): void
    {
        Mail::fake();

        $past = $this->makeComp('Past Comp', $this->org, now()->subMonth());
        $upcoming = $this->makeComp('Upcoming Comp', $this->org, now()->addWeek());
        $official = User::factory()->create(['name' => 'Jo Past']);
        $past->addOfficial($official, 'official');

        $this->actingAs($this->admin)
            ->get(route('manage.organisations.officials', $this->org))
            ->assertInertia(fn (Assert $page) => $page->where('upcomingCompetitions', fn ($comps) => collect($comps)->pluck('id')->all() === [$upcoming->id]));

        $this->actingAs($this->admin)
            ->from(route('manage.organisations.officials', $this->org))
            ->post(route('manage.organisations.officials.add', $this->org), [
                'user_id' => $official->id,
                'competition_id' => $upcoming->id,
                'role' => 'referee',
            ])
            ->assertRedirect(route('manage.organisations.officials', $this->org))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Jo Past added to Upcoming Comp as a referee');

        $this->assertTrue($upcoming->officialHasRole($official, 'referee'));
        Mail::assertSent(CompetitionOfficialAddedMail::class, fn ($mail) => $mail->hasTo($official->email));

        // adding them again doesn't send another email
        $this->actingAs($this->admin)
            ->post(route('manage.organisations.officials.add', $this->org), [
                'user_id' => $official->id,
                'competition_id' => $upcoming->id,
                'role' => 'official',
            ])
            ->assertSessionHasErrors('competition_id');
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

        $post = fn (array $data) => $this->actingAs($this->admin)->post(route('manage.organisations.officials.add', $this->org), $data + ['role' => 'official']);

        $post(['user_id' => User::factory()->create()->id, 'competition_id' => $upcoming->id])
            ->assertSessionHasErrors('user_id');
        $post(['user_id' => $official->id, 'competition_id' => $past->id])
            ->assertSessionHasErrors('competition_id');
        $post(['user_id' => $official->id, 'competition_id' => $theirs->id])
            ->assertSessionHasErrors('competition_id');

        $this->assertFalse($theirs->officials()->exists());
        $this->assertFalse($upcoming->officials()->exists());
        Mail::assertNothingSent();

        $member = User::factory()->create();
        $this->org->addAccount($member, 'view');
        $this->actingAs($member)->post(route('manage.organisations.officials.add', $this->org), [
            'user_id' => $official->id,
            'competition_id' => $upcoming->id,
            'role' => 'official',
        ])->assertForbidden();
    }
}
