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

    public function test_a_competitions_pages_share_its_heading_and_tabs(): void
    {
        $org = $this->organisation();
        $championship = $org->championships()->create(['name' => 'Winter Series', 'start_date' => '2026-10-01', 'end_date' => '2027-03-31']);
        $comp = $this->competition([
            'organisation' => $org->id,
            'championship_id' => $championship->id,
            'type' => 'QUALIFIER',
            'status_message' => '<p>Lunch until <strong>1pm</strong></p>',
            'can_be_live' => true,
        ]);

        foreach ([
            'landing.competition' => 'Info',
            'landing.competition.heats-draws' => 'Heats &amp; Draws',
            'landing.competition.results' => 'Results',
        ] as $route => $tab) {
            $this->get(route($route, $comp->getSlug()))
                ->assertOk()
                ->assertSee('Test Competition')
                ->assertSee(Carbon::today()->format('l jS F'))
                ->assertSee('href="' . route('landing.championship', 'Winter-Series') . '"', false)
                ->assertSee('Qualifier')
                ->assertSee('href="' . route('landing.organisation', 'Test Club') . '"', false)
                ->assertSee('aria-current="page">' . $tab . '</a>', false)
                ->assertSee('Lunch until <strong>1pm</strong>', false)
                ->assertSee('href="' . route('live', ['comp' => $comp->id]) . '"', false);
        }
    }

    public function test_a_competition_that_isnt_live_today_has_no_follow_live(): void
    {
        $comp = $this->competition(['when' => Carbon::tomorrow(), 'can_be_live' => true]);

        $this->get(route('landing.competition', $comp->getSlug()))
            ->assertOk()
            ->assertDontSee('Follow live');
    }

    public function test_info_lists_the_events_without_registration(): void
    {
        $comp = $this->competition();
        $serc = new \App\Models\SERC();
        $serc->forceFill(['name' => 'Car Crash', 'competition' => $comp->id, 'scorable_entity' => 'team'])->save();

        $this->get(route('landing.competition', $comp->getSlug()))
            ->assertOk()
            ->assertSee('Car Crash')
            ->assertSee('Test Pool')
            ->assertSee('google.com/maps', false)
            ->assertDontSee('Register')
            ->assertDontSee('se-btn');
    }

    private function sercWithDraw(Competition $comp): array
    {
        $serc = new \App\Models\SERC();
        $serc->forceFill(['name' => 'Dry SERC', 'type' => 'DRY', 'scorable_entity' => 'team', 'competition' => $comp->id])->save();

        $teams = [];
        foreach ([['A', 0, 1], ['B', 1, 1]] as [$letter, $tank, $draw]) {
            $team = \App\Models\CompetitionTeam::create(['competition' => $comp->id, 'team' => $letter]);
            $serc->draw()->create(['entity_id' => $team->id, 'entity_type' => $team->getMorphClass(), 'tank' => $tank, 'draw' => $draw]);
            $teams[] = $team;
        }

        return $teams;
    }

    public function test_the_draw_shows_only_once_released(): void
    {
        $comp = $this->competition(['show_draws' => false, 'team_format' => 'Team :N']);
        $this->sercWithDraw($comp);

        $this->get(route('landing.competition.heats-draws', $comp->getSlug()))
            ->assertOk()
            ->assertSee('The Draw appears here once the Organisers release it.')
            ->assertDontSee('Team A')
            // Nothing's released, so there's nothing to search
            ->assertDontSee('Find a Team or competitor');
    }

    public function test_the_draw_is_split_into_tanks_when_the_competition_uses_them(): void
    {
        $comp = $this->competition(['show_draws' => true, 'team_format' => 'Team :N']);
        $this->sercWithDraw($comp);
        $settings = $comp->getScoringSettings;
        $settings->use_tanks = true;
        $settings->save();

        $this->get(route('landing.competition.heats-draws', $comp->getSlug()))
            ->assertOk()
            ->assertSeeInOrder(['Tank 1', 'Team A', 'Tank 2', 'Team B'])
            ->assertSee('Find a Team or competitor')
            ->assertDontSee('se-card');
    }

    public function test_heats_show_only_once_released(): void
    {
        $comp = $this->competition(['show_heats' => false]);
        $base = new \App\Models\SpeedEvent();
        $base->name = 'Swim & Tow';
        $base->save();
        $event = new \App\Models\CompetitionSpeedEvent();
        $event->forceFill(['competition' => $comp->id, 'event' => $base->id, 'record' => 0])->save();

        $this->get(route('landing.competition.heats-draws', $comp->getSlug()))
            ->assertOk()
            ->assertSee('Heats appear here once the Organisers release them.');
    }

    private function publishedSerc(Competition $comp, string $name = 'Dry SERC', bool $viewable = true): \App\Models\SERC
    {
        $serc = new \App\Models\SERC();
        $serc->forceFill(['name' => $name, 'type' => 'DRY', 'scorable_entity' => 'team', 'competition' => $comp->id, 'viewable' => $viewable])->save();

        return $serc;
    }

    private function publishedSheet(Competition $comp, string $name, bool $viewable = true): \App\Models\ResultSchema
    {
        $sheet = new \App\Models\ResultSchema();
        $sheet->forceFill([
            'name' => $name,
            'competition' => $comp->id,
            'viewable' => $viewable,
            'schema' => ['equation' => 'item.position', 'global_variables' => [], 'league' => null, 'ignore_dq' => false, 'group_on' => 'team', 'rank_higher' => false, 'break_ties' => []],
        ])->save();

        return $sheet;
    }

    public function test_results_say_when_theyll_appear_until_theyre_published(): void
    {
        $comp = $this->competition(['public_results' => false]);
        $this->publishedSerc($comp);

        $this->get(route('landing.competition.results', $comp->getSlug()))
            ->assertOk()
            ->assertSee('Results appear here once an Organiser publishes them.')
            ->assertDontSee('role="tablist"', false)
            ->assertDontSee('Dry SERC');
    }

    public function test_results_show_only_the_tabs_with_something_published(): void
    {
        $comp = $this->competition(['public_results' => true]);
        $this->publishedSerc($comp);
        $this->publishedSerc($comp, 'Wet SERC', viewable: false);

        // Only Events has anything, so there's no tab bar to choose from
        $this->get(route('landing.competition.results', $comp->getSlug()))
            ->assertOk()
            ->assertSee('Dry SERC')
            ->assertDontSee('Wet SERC')
            ->assertDontSee('role="tablist"', false)
            ->assertDontSee('Results appear here once');

        $this->publishedSheet($comp, 'Overall Standings');
        $this->publishedSheet($comp, 'Working Sheet', viewable: false);

        // Results sheets comes before Events, so it opens first; Overall has no Master sheets, so it's left out
        $this->get(route('landing.competition.results', $comp->getSlug()))
            ->assertOk()
            ->assertSee('role="tablist"', false)
            ->assertSeeInOrder(['aria-selected="true">Results sheets', 'aria-selected="false">Events'], false)
            ->assertDontSee('>Overall<', false)
            ->assertSee('Overall Standings')
            ->assertDontSee('Working Sheet')
            ->assertDontSee('se-card');
    }

    public function test_results_ask_for_a_league_first_when_the_competition_says_to(): void
    {
        $comp = $this->competition(['public_results' => true, 'select_league_first' => true]);
        $this->publishedSerc($comp);

        $this->get(route('landing.competition.results', $comp->getSlug()))
            ->assertOk()
            ->assertSee('Choose a League to see the results.');

        $comp->forceFill(['select_league_first' => false])->save();

        $this->get(route('landing.competition.results', $comp->getSlug()))
            ->assertOk()
            ->assertDontSee('Choose a League to see the results.');
    }

    public function test_provisional_results_are_flagged_once(): void
    {
        $comp = $this->competition(['public_results' => true, 'results_provisional' => true]);
        $this->publishedSerc($comp);
        $this->publishedSheet($comp, 'Overall Standings');

        $response = $this->get(route('landing.competition.results', $comp->getSlug()))->assertOk();
        $this->assertSame(1, substr_count($response->getContent(), 'These results are provisional'));
    }

    public function test_the_serc_breakdown_sits_under_results_and_leads_back_to_its_league(): void
    {
        $comp = $this->competition(['public_results' => true]);
        $serc = $this->publishedSerc($comp);

        $this->get(route('landing.competition.results.breakdown.serc', [$comp->getSlug(), $serc, 'league' => 7]))
            ->assertOk()
            ->assertSee('aria-current="page">Results</a>', false)
            ->assertSee('Dry SERC breakdown')
            ->assertSee('href="' . e(route('landing.competition.results', [$comp->getSlug(), 'tab' => 'events', 'event' => 'serc-' . $serc->id, 'league' => 7])) . '"', false)
            ->assertDontSee('se-table');
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
