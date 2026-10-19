<?php

namespace Tests\Feature;

use App\Mail\CompetitionOfficialAddedMail;
use App\Models\AccountInvite;
use App\Models\Competition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/**
 * The Terms, Privacy and Cookie policies are public, linked from every page's footer, and pointed
 * out wherever someone signs in or gets an account.
 */
class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    private Competition $comp;

    protected function setUp(): void
    {
        parent::setUp();

        $this->comp = new Competition();
        $this->comp->name = 'Test Competition';
        $this->comp->when = now();
        $this->comp->where = 'Test Pool';
        $this->comp->max_lanes = 8;
        $this->comp->anytimepin = false;
        $this->comp->seed_per_event = false;
        $this->comp->heats_per_event = true;
        $this->comp->use_seeds = true;
        $this->comp->can_be_live = true;
        $this->comp->save();
    }

    private function assertLinksToPolicies($response, array $policies = ['terms', 'privacy', 'cookies']): void
    {
        // always the main domain: the subdomains have their own catch-all routes
        foreach ($policies as $policy) {
            $response->assertSee('href="' . config('app.url') . "/$policy" . '"', false);
        }
    }

    public function test_policies_are_public_and_name_the_operator(): void
    {
        foreach (['terms' => 'Terms of Service', 'privacy' => 'Privacy Policy', 'cookies' => 'Cookie Policy'] as $policy => $title) {
            $this->get(route("legal.$policy"))
                ->assertOk()
                ->assertSee($title)
                ->assertSee('Noah Hollowell')
                ->assertSee('support@scoring.events');
        }
    }

    public function test_policy_urls_arent_taken_by_the_organisation_pages(): void
    {
        $this->assertSame(url('/terms'), route('legal.terms'));
        $this->assertSame(url('/privacy'), route('legal.privacy'));
        $this->assertSame(url('/cookies'), route('legal.cookies'));
    }

    public function test_main_site_pages_have_the_footer(): void
    {
        $this->assertLinksToPolicies($this->get(route('landing.competition', $this->comp))->assertOk());
    }

    public function test_live_site_has_the_footer(): void
    {
        $this->assertLinksToPolicies($this->get(route('live') . '?comp=' . $this->comp->id)->assertOk());
    }

    public function test_results_site_has_the_footer(): void
    {
        $this->assertLinksToPolicies($this->get(route('public.results.unavailable', $this->comp))->assertOk());
    }

    public function test_legacy_digital_judge_has_the_footer(): void
    {
        $this->assertLinksToPolicies($this->get(route('dj.index'))->assertOk());
    }

    public function test_login_points_out_the_terms(): void
    {
        $this->assertLinksToPolicies($this->get(route('login'))->assertOk()->assertSee('By continuing you agree to'), ['terms', 'privacy']);
    }

    public function test_accepting_an_invite_points_out_the_terms(): void
    {
        $invite = new AccountInvite();
        $invite->email = 'new@example.com';
        $invite->to()->associate($this->comp);
        $invite->save();

        $this->get(route('invite.show', [$invite, 'new@example.com']))->assertOk()->assertSee('By continuing you agree to');

        $user = User::factory()->create(['email' => 'existing@example.com']);
        $existing = $invite->replicate();
        $existing->email = 'existing@example.com';
        $existing->save();

        $this->actingAs($user)->get(route('invite.show', [$existing, 'existing@example.com']))->assertOk()->assertSee('By continuing you agree to');
    }

    public function test_digital_judge_gets_the_policy_links(): void
    {
        $this->get(route('judge.login'))
            ->assertOk()
            ->assertInertia(fn(AssertableInertia $page) => $page
                ->where('legal.terms', config('app.url') . '/terms')
                ->where('legal.privacy', config('app.url') . '/privacy')
                ->where('legal.cookies', config('app.url') . '/cookies'));
    }

    public function test_official_added_email_links_to_the_policies(): void
    {
        $mail = (new CompetitionOfficialAddedMail($this->comp, 'official', newAccount: true))->render();

        $this->assertStringContainsString(route('legal.privacy'), $mail);
        $this->assertStringContainsString(route('legal.terms'), $mail);
        $this->assertStringContainsString('An organiser of Test Competition made this account', $mail);
    }
}
