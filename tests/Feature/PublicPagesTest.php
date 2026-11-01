<?php

namespace Tests\Feature;

use App\Helpers\RouteHelpers;
use App\Models\Competition;
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
        $live = $this->competition(['name' => 'Live Classic', 'can_be_live' => true]);
        $closed = $this->competition(['name' => 'Closed Classic']);
        $tomorrow = $this->competition(['name' => 'Tomorrow Classic', 'when' => Carbon::tomorrow(), 'can_be_live' => true]);

        $this->get('/')
            ->assertOk()
            ->assertSee(route('live', ['comp' => $live->id]), false)
            ->assertDontSee(route('live', ['comp' => $closed->id]), false)
            ->assertDontSee(route('live', ['comp' => $tomorrow->id]), false);
    }
}
