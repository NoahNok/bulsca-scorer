<?php

namespace Tests\Feature;

use App\Helpers\RouteHelpers;
use App\Models\Competition;
use App\Models\Organisation\Organisation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * The main domain's public pages, on the v2 layout (docs/v2-ui-style-guide.md).
 */
class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    private function competition(array $attributes = []): Competition
    {
        $comp = new Competition();
        $comp->forceFill(array_merge([
            'name' => 'Test Competition',
            'when' => Carbon::today(),
            'where' => 'Test Pool',
            'max_lanes' => 8,
            'anytimepin' => false,
            'seed_per_event' => false,
            'heats_per_event' => true,
            'use_seeds' => true,
            'show_competition' => true,
            'can_be_live' => false,
            'public_results' => false,
        ], $attributes))->save();

        return $comp;
    }

    private function organisation(string $name = 'Test Club'): Organisation
    {
        $org = new Organisation();
        $org->name = $name;
        $org->save();

        return $org;
    }

    public function test_explore_lists_today_upcoming_and_past_competitions(): void
    {
        $this->competition(['name' => 'Today Classic', 'can_be_live' => true]);
        $this->competition(['name' => 'Next Week Open', 'when' => Carbon::today()->addWeek()]);
        $this->competition(['name' => 'Next Year Open', 'when' => Carbon::today()->addYear()]);
        $this->competition(['name' => 'Last Month Cup', 'when' => Carbon::today()->subMonth(), 'public_results' => true]);
        $this->competition(['name' => 'Secret Cup', 'when' => Carbon::today()->subMonth(), 'show_competition' => false]);

        $this->get(route('explore'))
            ->assertOk()
            ->assertViewIs('landing.explore')
            ->assertSeeInOrder(['Today Classic', 'Follow live', 'Next Week Open', 'Next Year Open', 'Last Month Cup'])
            ->assertDontSee('Secret Cup')
            ->assertDontSee('se-card');
    }

    public function test_explore_pages_past_competitions_twenty_at_a_time(): void
    {
        for ($i = 1; $i <= 21; $i++) {
            $this->competition(['name' => "Past Comp $i", 'when' => Carbon::today()->subDays($i)]);
        }

        $this->get(route('explore'))
            ->assertOk()
            ->assertSee('Past Comp 20')
            ->assertDontSee('Past Comp 21<', false)
            ->assertSee('Older');

        $this->get(route('explore', ['page' => 2]))
            ->assertOk()
            ->assertSee('Past Comp 21')
            ->assertDontSee('Past Comp 20<', false)
            ->assertSee('Newer');
    }

    public function test_explore_lists_every_organisation(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            $this->organisation(sprintf('Club %02d', $i));
        }

        $this->get(route('explore'))
            ->assertOk()
            ->assertSee('Club 01')
            ->assertSee('Club 15')
            ->assertSee('href="' . route('landing.organisation', 'Club 15') . '"', false);
    }

    public function test_an_organisations_page_lists_its_championships_and_competitions(): void
    {
        $org = $this->organisation();
        $org->championships()->create(['name' => 'Old Series', 'start_date' => '2024-10-01', 'end_date' => '2025-03-31']);
        $org->championships()->create([
            'name' => 'Current Series',
            'start_date' => Carbon::today()->subMonth(),
            'end_date' => Carbon::today()->addMonth(),
        ]);
        $this->competition(['name' => 'Their Next Cup', 'organisation' => $org->id, 'when' => Carbon::tomorrow()]);
        for ($i = 1; $i <= 25; $i++) {
            $this->competition(['name' => "Their Comp $i", 'organisation' => $org->id, 'when' => Carbon::today()->subDays($i)]);
        }
        $this->competition(['name' => 'Someone Elses Cup']);

        $this->get(route('landing.organisation', $org->name))
            ->assertOk()
            ->assertSee('Test Club')
            ->assertSeeInOrder(['Current Series', 'Old Series', 'Their Next Cup', 'Their Comp 1', 'Their Comp 25'])
            ->assertSee('href="' . route('landing.championship', 'Current-Series') . '"', false)
            ->assertDontSee('Someone Elses Cup')
            ->assertDontSee('se-card');
    }

    public function test_a_championships_page_lists_its_competitions_with_their_type(): void
    {
        $org = $this->organisation();
        $championship = $org->championships()->create(['name' => 'Winter Series', 'start_date' => '2026-10-01', 'end_date' => '2027-03-31']);
        $this->competition(['name' => 'North Regional', 'championship_id' => $championship->id, 'type' => 'REGIONAL', 'when' => Carbon::today()->subWeek()]);
        $this->competition(['name' => 'Grand Final', 'championship_id' => $championship->id, 'type' => 'FINAL', 'when' => Carbon::today()->addWeek()]);

        $this->get(route('landing.championship', $championship->slug()))
            ->assertOk()
            ->assertSee('Winter Series')
            ->assertSee('href="' . route('landing.organisation', 'Test Club') . '"', false)
            ->assertSeeInOrder(['North Regional', 'Regional', 'Grand Final', 'Final'])
            ->assertSee('1 October 2026')
            ->assertDontSee('se-card');
    }

    public function test_the_home_page_is_the_v2_home(): void
    {
        $this->get('/')->assertOk()->assertViewIs('landing.v2.home');
    }

    public function test_the_old_preview_address_redirects_home(): void
    {
        $this->get('/v2')->assertStatus(301)->assertRedirect('/');
    }

    public function test_the_component_catalogue_is_kept_out_of_search(): void
    {
        $this->get('/v2/kit')->assertOk()->assertSee('<meta name="robots" content="noindex">', false);
        $this->get('/')->assertOk()->assertDontSee('noindex');
        $this->get(route('legal.terms'))->assertOk()->assertDontSee('noindex');
    }

    public function test_the_header_links_to_the_other_apps(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('href="' . route('manage.home') . '">Sign in</a>', false)
            ->assertSee('href="' . route('dj.home') . '"', false)
            ->assertSee('href="' . route('explore') . '"', false);

        $this->assertStringStartsWith('http://' . RouteHelpers::domainRemap('app.'), route('manage.home'));
        $this->assertStringStartsWith('http://' . RouteHelpers::domainRemap('judge.'), route('dj.home'));
    }

    public function test_follow_live_shows_only_for_competitions_the_live_pages_will_open_today(): void
    {
        $org = $this->organisation();
        $championship = $org->championships()->create(['name' => 'Winter Series', 'start_date' => Carbon::today()->subMonth(), 'end_date' => Carbon::today()->addMonth()]);
        $in = ['organisation' => $org->id, 'championship_id' => $championship->id, 'type' => 'REGIONAL'];

        $live = $this->competition(['name' => 'Live Classic', 'can_be_live' => true, ...$in]);
        $closed = $this->competition(['name' => 'Closed Classic', ...$in]);
        $tomorrow = $this->competition(['name' => 'Tomorrow Classic', 'when' => Carbon::tomorrow(), 'can_be_live' => true, ...$in]);

        foreach (['/', route('explore'), route('landing.organisation', $org->name), route('landing.championship', $championship->slug())] as $page) {
            $this->get($page)
                ->assertOk()
                ->assertSee('href="' . route('live', ['comp' => $live->id]) . '"', false)
                ->assertDontSee('href="' . route('live', ['comp' => $closed->id]) . '"', false)
                ->assertDontSee('href="' . route('live', ['comp' => $tomorrow->id]) . '"', false);
        }
    }
}
