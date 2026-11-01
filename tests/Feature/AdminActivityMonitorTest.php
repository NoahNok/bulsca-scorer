<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\User;
use App\Traits\RecordActivity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * The Admin Activity Monitor: the same ActivityFeed as a competition's Activity page, across every
 * competition
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

        $this->actingAs(User::factory()->create())
            ->get($this->appUrl('/admin/activity'))
            ->assertRedirect('/');

        $this->actingAs($siteAdmin)->get($this->appUrl("/admin/activity?by={$organiser->id}"))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Activity')
                ->where('url', route('manage.admin.activity'))
                ->has('entries', 1)
                ->where('entries.0.description', 'Places for A')
                ->where('entries.0.title', 'Places entered')
                // the competition is named, links to its page and filters by itself
                ->where('entries.0.related', fn ($related) => collect($related)->contains(fn ($item) => $item['kind'] === 'comp'
                    && $item['name'] === 'Our Competition'
                    && $item['link'] === route('comps.view', $comp)
                    && $item['filter']['value'] === "comp:{$comp->id}"))
                ->where('filters.by', [(string) $organiser->id])
                ->where('chipGroups.0.label', 'Actioned by')
                ->where('chipGroups.0.chips.0.label', 'Orla Organiser')
                // pickers name things in one competition, so there are none site-wide
                ->where('pickers', []));
    }
}
