<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\User;
use App\Traits\RecordActivity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The Admin Activity Monitor stays Blade until Admin's redesign, on the same ActivityFeed as a
 * competition's Activity page
 */
class AdminActivityMonitorTest extends TestCase
{
    use RefreshDatabase, RecordActivity;

    public function test_it_shows_every_competitions_activity_with_filter_chips_and_click_through(): void
    {
        $comp = new Competition();
        $comp->name = 'Our Competition';
        $comp->when = now()->addWeek();
        $comp->where = 'Test Pool';
        $comp->max_lanes = 8;
        $comp->anytimepin = false;
        $comp->seed_per_event = false;
        $comp->heats_per_event = true;
        $comp->use_seeds = true;
        $comp->save();

        $team = new CompetitionTeam();
        $team->competition = $comp->id;
        $team->team = 'A';
        $team->save();

        $organiser = User::factory()->create(['name' => 'Orla Organiser']);
        $siteAdmin = User::factory()->create();
        $siteAdmin->forceFill(['admin' => true])->save();

        $this->recordActivity('OOF_MARKED', 'Places for A', user: $organiser, related: [$team, $comp]);
        $this->recordActivity('HEATS_RESET', 'Reset by someone else', user: $siteAdmin, related: $comp);

        $this->actingAs($siteAdmin)->get(route('admin.activity', ['by' => $organiser->id]))
            ->assertOk()
            ->assertSee('Places for A')
            ->assertDontSee('Reset by someone else')
            ->assertSee('Places entered')
            ->assertSeeInOrder(['Actioned by', 'Orla Organiser', 'Clear'])
            // a related item filters by itself, and the competition opens its page
            ->assertSee('href="' . e(route('admin.activity', ['by' => $organiser->id, 'related' => "comp:{$comp->id}"])) . '"', false)
            ->assertSee('href="' . route('comps.view', $comp) . '"', false)
            // removing the chip clears the filter
            ->assertSee('href="' . route('admin.activity') . '"', false)
            ->assertDontSee('Add team');
    }
}
