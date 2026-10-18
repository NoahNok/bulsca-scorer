<?php

namespace Tests\Feature\DigitalJudge;

use App\Models\Competition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class JudgeLandingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $comp = new Competition();
        $comp->name = 'Test Competition';
        $comp->when = now();
        $comp->where = 'Test Pool';
        $comp->max_lanes = 8;
        $comp->anytimepin = true;
        $comp->seed_per_event = false;
        $comp->heats_per_event = false;
        $comp->use_seeds = true;
        $comp->digitalJudgeEnabled = true;
        $comp->digitalJudgePin = '123456';
        $comp->digitalJudgeHeadPin = '654321';
        $comp->save();
    }

    public function test_the_landing_page_points_officials_at_digital_judge(): void
    {
        $this->get(route('dj.landing'))
            ->assertOk()
            ->assertInertia(fn(AssertableInertia $page) => $page->component('Judge/Landing'));
    }

    public function test_an_old_pin_link_joins_with_digital_judge(): void
    {
        $this->get(route('dj.landing', ['pin' => '123456']))
            ->assertRedirect(route('judge.join-link', '123456'));
    }

    public function test_an_old_head_pin_link_goes_to_legacy_with_the_pin_filled_in(): void
    {
        $this->get(route('dj.landing', ['pin' => '654321']))
            ->assertRedirect(route('dj.index', ['pin' => '654321']));
    }

    public function test_legacy_shows_the_pin_form(): void
    {
        $this->get(route('dj.index'))
            ->assertOk()
            ->assertViewIs('digitaljudge.index');
    }

    public function test_signed_out_legacy_pages_send_officials_back_to_the_legacy_form(): void
    {
        $this->get(route('dj.home'))->assertRedirect(route('dj.index'));
    }

    public function test_a_legacy_session_skips_the_landing_page(): void
    {
        $this->post(route('dj.login'), ['pin' => '123456', 'judgeName' => 'JO']);

        $this->get(route('dj.landing'))->assertRedirect(route('dj.home'));
        $this->get(route('dj.index'))->assertRedirect(route('dj.home'));
    }

    public function test_logging_out_of_legacy_returns_to_the_landing_page(): void
    {
        $this->post(route('dj.login'), ['pin' => '123456', 'judgeName' => 'JO']);

        $this->get(route('dj.logout'))->assertRedirect(route('dj.landing'));
    }
}
