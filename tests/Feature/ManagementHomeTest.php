<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\Organisation\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * The Management home (/ on app.) lists the organisations and competitions an Organiser can manage,
 * and the competitions they opened most recently.
 */
class ManagementHomeTest extends TestCase
{
    use RefreshDatabase;

    private User $organiser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organiser = User::factory()->create();
    }

    private function makeOrg(string $name): Organisation
    {
        $org = new Organisation();
        $org->name = $name;
        $org->save();

        return $org;
    }

    private function makeComp(string $name, $when, ?Organisation $org = null): Competition
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

    private function home()
    {
        return $this->actingAs($this->organiser)->get(route('manage.home'))->assertOk();
    }

    public function test_organisations_list_their_competitions(): void
    {
        $org = $this->makeOrg('Test Org');
        $org->addAccount($this->organiser, 'admin');
        $this->makeComp('Spring Champs', now()->addMonth(), $org);
        $this->makeOrg('Not Ours');

        $this->home()->assertInertia(fn (Assert $page) => $page
            ->component('Manage/Home')
            ->has('organisations', 1)
            ->where('organisations.0.name', 'Test Org')
            ->where('organisations.0.competitions.0.name', 'Spring Champs'));
    }

    public function test_organisations_show_upcoming_soonest_first_then_the_three_latest_past(): void
    {
        $org = $this->makeOrg('Test Org');
        $org->addAccount($this->organiser, 'admin');
        $this->makeComp('Later', now()->addMonths(2), $org);
        $this->makeComp('Sooner', now()->addWeek(), $org);
        $this->makeComp('Today', now(), $org);
        foreach ([1, 2, 3, 4] as $weeks) {
            $this->makeComp("$weeks weeks ago", now()->subWeeks($weeks), $org);
        }

        $this->home()->assertInertia(fn (Assert $page) => $page
            ->where('organisations.0.competitions', fn ($comps) => collect($comps)->pluck('name')->all() === [
                'Today', 'Sooner', 'Later', '1 weeks ago', '2 weeks ago', '3 weeks ago',
            ])
            ->where('organisations.0.more', true));
    }

    public function test_competitions_with_direct_access_are_listed_once_unless_an_organisation_covers_them(): void
    {
        $ours = $this->makeOrg('Our Org');
        $ours->addAccount($this->organiser, 'admin');
        $covered = $this->makeComp('Covered', now()->addWeek(), $ours);
        $covered->addAccount($this->organiser, 'admin');

        $personal = $this->makeComp('Personal', now()->addWeek());
        $personal->addAccount($this->organiser, ['owner', 'teams']);

        $elsewhere = $this->makeComp('Elsewhere', now()->subWeek(), $this->makeOrg('Their Org'));
        $elsewhere->addAccount($this->organiser, 'results');

        $this->makeComp('Not Ours', now()->addWeek());

        $this->home()->assertInertia(fn (Assert $page) => $page
            ->where('competitions', fn ($comps) => collect($comps)->pluck('name')->all() === ['Personal', 'Elsewhere'])
            ->where('competitions.0.limited', false)
            ->where('competitions.1.limited', true)
            ->where('competitions.1.past', true));
    }

    public function test_competitions_today_are_flagged_and_live_when_the_live_site_is_on(): void
    {
        $live = $this->makeComp('Live', now()->startOfDay());
        $live->can_be_live = true;
        $live->save();
        $live->addAccount($this->organiser, 'admin');
        $hidden = $this->makeComp('Hidden', now()->addWeek());
        $hidden->show_competition = false;
        $hidden->save();
        $hidden->addAccount($this->organiser, 'admin');

        $this->home()->assertInertia(fn (Assert $page) => $page
            ->where('competitions.0.name', 'Live')
            ->where('competitions.0.today', true)
            ->where('competitions.0.live', true)
            ->where('competitions.0.private', false)
            ->where('competitions.0.url', route('comps.view', $live))
            ->where('competitions.1.today', false)
            ->where('competitions.1.days', 7)
            ->where('competitions.1.live', false)
            ->where('competitions.1.private', true));
    }

    public function test_opening_a_competition_puts_it_first_in_recent(): void
    {
        $first = $this->makeComp('First', now()->addWeek());
        $second = $this->makeComp('Second', now()->addWeek());
        $first->addAccount($this->organiser, 'admin');
        $second->addAccount($this->organiser, 'admin');

        $this->actingAs($this->organiser);
        $this->get(route('comps.view', $first))->assertOk();
        $this->travel(1)->minutes();
        $this->get(route('comps.view', $second))->assertOk();
        $this->travel(1)->minutes();
        $this->get(route('comps.view', $first))->assertOk();

        $this->home()->assertInertia(fn (Assert $page) => $page
            ->where('recent', fn ($comps) => collect($comps)->pluck('name')->all() === ['First', 'Second']));
    }

    public function test_fetches_and_downloads_are_not_visits(): void
    {
        $comp = $this->makeComp('Fetched', now()->addWeek());
        $comp->addAccount($this->organiser, 'admin');

        $this->actingAs($this->organiser);
        $this->getJson(route('comps.view', $comp));
        $this->get(route('comps.view', $comp), ['Purpose' => 'prefetch']);

        $this->home()->assertInertia(fn (Assert $page) => $page->has('recent', 0));
    }

    public function test_recent_leaves_out_competitions_you_can_no_longer_access(): void
    {
        $org = $this->makeOrg('Test Org');
        $org->addAccount($this->organiser, 'admin');
        $viaOrg = $this->makeComp('Via Org', now()->addWeek(), $org);
        $lost = $this->makeComp('Lost', now()->addWeek());
        $lost->addAccount($this->organiser, 'admin');

        $this->actingAs($this->organiser);
        $this->get(route('comps.view', $viaOrg))->assertOk();
        $this->get(route('comps.view', $lost))->assertOk();
        $lost->addAccount($this->organiser, 'none');

        $this->home()->assertInertia(fn (Assert $page) => $page
            ->where('recent', fn ($comps) => collect($comps)->pluck('name')->all() === ['Via Org']));
    }

    public function test_admins_keep_competitions_they_opened_in_recent(): void
    {
        $this->organiser->forceFill(['admin' => true])->save();
        $theirs = $this->makeComp('Someone Elses', now()->addWeek());

        $this->actingAs($this->organiser)->get(route('comps.view', $theirs))->assertOk();

        $this->home()->assertInertia(fn (Assert $page) => $page
            ->where('recent.0.name', 'Someone Elses')
            ->has('competitions', 0));
    }
}
