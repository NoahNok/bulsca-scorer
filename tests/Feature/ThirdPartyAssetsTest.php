<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\SERC;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Public pages serve their fonts and scripts from Scoring.Events, so visiting one doesn't send
 * the visitor's IP address to Google, jsDelivr or Giphy (as the privacy policy promises).
 */
class ThirdPartyAssetsTest extends TestCase
{
    use RefreshDatabase;

    private const THIRD_PARTY_HOSTS = ['fonts.googleapis.com', 'fonts.gstatic.com', 'cdn.jsdelivr.net', 'google.com/maps/embed', 'giphy.com'];

    private Competition $comp;
    private SERC $serc;

    protected function setUp(): void
    {
        parent::setUp();

        $this->comp = new Competition();
        $this->comp->name = 'Test Competition';
        $this->comp->when = now();
        $this->comp->where = 'Test Pool, Leeds';
        $this->comp->max_lanes = 8;
        $this->comp->anytimepin = false;
        $this->comp->seed_per_event = false;
        $this->comp->heats_per_event = true;
        $this->comp->use_seeds = true;
        $this->comp->can_be_live = true;
        $this->comp->public_results = true;
        $this->comp->save();

        $this->serc = new SERC();
        $this->serc->name = 'Dry SERC';
        $this->serc->competition = $this->comp->id;
        $this->serc->scorable_entity = 'team';
        $this->serc->save();
    }

    private function assertNoThirdPartyAssets(string $url): void
    {
        $response = $this->get($url)->assertOk();

        foreach (self::THIRD_PARTY_HOSTS as $host) {
            $response->assertDontSee($host, false);
        }
    }

    public function test_competition_overview_has_no_third_party_assets(): void
    {
        $this->assertNoThirdPartyAssets(route('landing.competition', $this->comp));
    }

    public function test_competition_overview_links_to_the_venue_on_google_maps(): void
    {
        $this->get(route('landing.competition', $this->comp))
            ->assertSee('https://www.google.com/maps/search/?api=1&amp;query=Test+Pool%2C+Leeds', false);
    }

    public function test_serc_breakdown_has_no_third_party_assets(): void
    {
        $this->assertNoThirdPartyAssets(route('landing.competition.results.breakdown.serc', ['comp' => $this->comp, 'serc' => $this->serc]));
    }

    public function test_live_site_has_no_third_party_assets(): void
    {
        $this->assertNoThirdPartyAssets(route('live') . '?comp=' . $this->comp->id);
    }
}
