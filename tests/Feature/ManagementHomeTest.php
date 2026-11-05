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

    public function test_competitions_from_your_organisations_and_direct_access_are_listed_once(): void
    {
        $ours = $this->makeOrg('Our Org');
        $ours->addAccount($this->organiser, 'admin');
        $this->makeOrg('Not Our Org');
        $covered = $this->makeComp('Covered', now()->addWeeks(2), $ours);
        $covered->addAccount($this->organiser, 'admin');

        $personal = $this->makeComp('Personal', now()->addWeek());
        $personal->addAccount($this->organiser, ['owner', 'teams']);

        $elsewhere = $this->makeComp('Elsewhere', now()->subWeek(), $this->makeOrg('Their Org'));
        $elsewhere->addAccount($this->organiser, 'results');

        $this->makeComp('Not Ours', now()->addWeek());

        $this->home()->assertInertia(fn (Assert $page) => $page
            ->component('Manage/Home')
            ->where('competitions', fn ($comps) => collect($comps)->pluck('name')->all() === ['Personal', 'Covered', 'Elsewhere'])
            ->where('competitions.0.organisation', null)
            ->where('competitions.1.organisation', $ours->id)
            ->where('competitions.2.organisation', $elsewhere->organisation)
            // Yours first, then ones you were only invited into, which have no page you can open
            ->where('organisations', fn ($orgs) => collect($orgs)->map(fn ($org) => [$org['name'], $org['url'] !== null])->all() === [
                ['Our Org', true], ['Their Org', false],
            ])
            ->where('competitions.0.limited', false)
            ->where('competitions.2.limited', true)
            ->where('competitions.2.past', true));
    }

    public function test_competitions_are_upcoming_soonest_first_then_past_latest_first(): void
    {
        $org = $this->makeOrg('Test Org');
        $org->addAccount($this->organiser, 'admin');
        $this->makeComp('Later', now()->addMonths(2), $org);
        $this->makeComp('Sooner', now()->addWeek(), $org);
        $this->makeComp('Today', now(), $org);
        $this->makeComp('Last week', now()->subWeek(), $org);
        $this->makeComp('Last month', now()->subMonth(), $org);

        $this->home()->assertInertia(fn (Assert $page) => $page
            ->where('competitions', fn ($comps) => collect($comps)->pluck('name')->all() === [
                'Today', 'Sooner', 'Later', 'Last week', 'Last month',
            ]));
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
