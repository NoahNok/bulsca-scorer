<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\Competitor;
use App\Models\SERC;
use App\Models\SpeedEvent;
use App\Models\User;
use App\Traits\RecordActivity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogFiltersTest extends TestCase
{
    use RefreshDatabase, RecordActivity;

    private Competition $comp;
    private CompetitionSpeedEvent $speed;
    private SERC $serc;
    private CompetitionTeam $teamA;
    private CompetitionTeam $teamB;
    private CompetitionTeam $teamC;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->comp = $this->competition('Our Competition');

        $base = new SpeedEvent();
        $base->name = 'Swim & Tow';
        $base->save();

        $this->speed = new CompetitionSpeedEvent();
        $this->speed->event = $base->id;
        $this->speed->competition = $this->comp->id;
        $this->speed->record = 0;
        $this->speed->save();

        $this->serc = new SERC();
        $this->serc->name = 'Dry SERC';
        $this->serc->competition = $this->comp->id;
        $this->serc->scorable_entity = 'team';
        $this->serc->save();

        $this->teamA = $this->team($this->comp, 'A');
        $this->teamB = $this->team($this->comp, 'B');
        $this->teamC = $this->team($this->comp, 'C');

        $this->admin = User::factory()->create(['name' => 'Orla Organiser']);
        $this->comp->addAccount($this->admin, 'admin');
    }

    private function competition(string $name): Competition
    {
        $comp = new Competition();
        $comp->name = $name;
        $comp->when = now()->addWeek();
        $comp->where = 'Test Pool';
        $comp->max_lanes = 8;
        $comp->anytimepin = false;
        $comp->seed_per_event = false;
        $comp->heats_per_event = true;
        $comp->use_seeds = true;
        $comp->save();

        return $comp;
    }

    private function team(Competition $comp, string $letter): CompetitionTeam
    {
        $team = new CompetitionTeam();
        $team->competition = $comp->id;
        $team->team = $letter;
        $team->save();

        return $team;
    }

    private function log(array $query = [])
    {
        return $this->actingAs($this->admin)->get(route('comps.activityLog', [$this->comp, ...$query]));
    }

    public function test_two_teams_show_activity_for_either_team(): void
    {
        $this->recordActivity('SPEED_RESULT_MARKED', 'Marks for A', related: [$this->serc, $this->teamA, $this->comp]);
        $this->recordActivity('SPEED_RESULT_MARKED', 'Marks for B', related: [$this->serc, $this->teamB, $this->comp]);
        $this->recordActivity('SPEED_RESULT_MARKED', 'Marks for C', related: [$this->serc, $this->teamC, $this->comp]);

        $this->log(['related' => "team:{$this->teamA->id}|team:{$this->teamB->id}"])
            ->assertOk()
            ->assertSee('Marks for A')
            ->assertSee('Marks for B')
            ->assertDontSee('Marks for C');
    }

    public function test_teams_in_an_event_only_show_those_teams_in_that_event(): void
    {
        $this->recordActivity('SPEED_RESULT_MARKED', 'A in SERC', related: [$this->serc, $this->teamA, $this->comp]);
        $this->recordActivity('SPEED_RESULT_MARKED', 'B in SERC', related: [$this->serc, $this->teamB, $this->comp]);
        $this->recordActivity('SPEED_RESULT_MARKED', 'A in speed', related: [$this->speed, $this->teamA, $this->comp]);

        $this->log(['related' => "team:{$this->teamA->id}|serc:{$this->serc->id}|team:{$this->teamB->id}"])
            ->assertOk()
            ->assertSee('A in SERC')
            ->assertSee('B in SERC')
            ->assertDontSee('A in speed');
    }

    public function test_all_sercs_shows_activity_for_any_serc(): void
    {
        $this->recordActivity('SPEED_RESULT_MARKED', 'A in SERC', related: [$this->serc, $this->teamA, $this->comp]);
        $this->recordActivity('SPEED_RESULT_MARKED', 'A in speed', related: [$this->speed, $this->teamA, $this->comp]);

        $this->log(['related' => 'serc'])
            ->assertOk()
            ->assertSee('A in SERC')
            ->assertDontSee('A in speed');
    }

    public function test_actioned_by_shows_only_that_users_activity(): void
    {
        $judge = User::factory()->create(['name' => 'Jo Judge']);

        $this->recordActivity('SPEED_RESULT_MARKED', 'Done by judge', user: $judge, related: $this->comp);
        $this->recordActivity('SPEED_RESULT_MARKED', 'Done by organiser', user: $this->admin, related: $this->comp);

        $this->log(['by' => $judge->id])
            ->assertOk()
            ->assertSee('Done by judge')
            ->assertDontSee('Done by organiser');
    }

    public function test_actioned_by_several_people_or_system_shows_any_of_them(): void
    {
        $judge = User::factory()->create(['name' => 'Jo Judge']);
        $timer = User::factory()->create(['name' => 'Tam Timer']);

        $this->recordActivity('SPEED_RESULT_MARKED', 'Done by judge', user: $judge, related: $this->comp);
        $this->recordActivity('SPEED_RESULT_MARKED', 'Done by timer', user: $timer, related: $this->comp);
        $this->recordActivity('SPEED_RESULT_MARKED', 'Done by the system', related: $this->comp);

        $this->log(['by' => "{$judge->id}|system|junk"])
            ->assertOk()
            ->assertSee('Done by judge')
            ->assertSee('Done by the system')
            ->assertDontSee('Done by timer');
    }

    public function test_actioned_by_is_combined_with_related_filters(): void
    {
        $judge = User::factory()->create(['name' => 'Jo Judge']);

        $this->recordActivity('SPEED_RESULT_MARKED', 'Judge on A', user: $judge, related: [$this->teamA, $this->comp]);
        $this->recordActivity('SPEED_RESULT_MARKED', 'Judge on B', user: $judge, related: [$this->teamB, $this->comp]);

        $this->log(['by' => $judge->id, 'related' => "team:{$this->teamA->id}"])
            ->assertOk()
            ->assertSee('Judge on A')
            ->assertDontSee('Judge on B');
    }

    public function test_pickers_offer_this_competitions_events_teams_and_people(): void
    {
        $judge = User::factory()->create(['name' => 'Jo Judge']);
        $this->recordActivity('SPEED_RESULT_MARKED', 'Done by judge', user: $judge, related: $this->comp);

        $other = $this->competition('Their Competition');
        $theirTeam = $this->team($other, 'Z');
        $outsider = User::factory()->create(['name' => 'Olly Outsider']);
        $this->recordActivity('SPEED_RESULT_MARKED', 'Elsewhere', user: $outsider, related: [$theirTeam, $other]);

        $this->log()
            ->assertOk()
            ->assertSee('value="serc"', false)
            ->assertSee('value="speed"', false)
            ->assertSee('value="serc:' . $this->serc->id . '"', false)
            ->assertSee('value="speed:' . $this->speed->id . '"', false)
            ->assertSee('value="team:' . $this->teamA->id . '"', false)
            ->assertDontSee('value="team:' . $theirTeam->id . '"', false)
            ->assertSee('value="' . $judge->id . '"', false)
            ->assertSee('value="system"', false)
            ->assertDontSee('Olly Outsider')
            ->assertDontSee('Add competitor');
    }

    public function test_a_serc_and_a_speed_event_show_activity_for_either_event(): void
    {
        $this->recordActivity('SPEED_RESULT_MARKED', 'A in SERC', related: [$this->serc, $this->teamA, $this->comp]);
        $this->recordActivity('SPEED_RESULT_MARKED', 'A in speed', related: [$this->speed, $this->teamA, $this->comp]);

        $this->log(['related' => "serc:{$this->serc->id}|speed:{$this->speed->id}"])
            ->assertOk()
            ->assertSee('A in SERC')
            ->assertSee('A in speed');
    }

    public function test_chips_are_grouped_by_kind_and_each_removes_only_its_value(): void
    {
        $judge = User::factory()->create(['name' => 'Jo Judge']);
        $this->recordActivity('SPEED_RESULT_MARKED', 'Done by judge', user: $judge, related: $this->comp);
        $teamA = "team:{$this->teamA->id}";
        $teamB = "team:{$this->teamB->id}";

        $response = $this->log([
            'related' => "{$teamA}|{$teamB}|serc",
            'by' => $judge->id,
            'activity' => 'HEATS_RESET',
            'page' => 2,
        ])->assertOk();

        $response->assertSeeInOrder(['Type', 'HEATS RESET', 'Event', 'All SERCs', 'Team', $this->teamA->getName($this->comp), 'or', $this->teamB->getName($this->comp), 'Actioned by', 'Jo Judge']);

        // Removing team A keeps team B and the other filters, and goes back to page 1
        $removeA = e(route('comps.activityLog', [
            $this->comp,
            'related' => "{$teamB}|serc",
            'by' => $judge->id,
            'activity' => 'HEATS_RESET',
        ]));
        $response->assertSee($removeA, false);

        // Clear drops every filter
        $response->assertSee('href="' . route('comps.activityLog', $this->comp) . '"', false);
    }

    public function test_activity_type_toggles_apply_straight_away(): void
    {
        $this->recordActivity('HEATS_RESET', 'Heats reset', related: $this->comp);
        $this->recordActivity('HEATS_GENERATED', 'Heats generated', related: $this->comp);

        $this->log(['related' => 'serc', 'activity' => 'HEATS_RESET', 'page' => 2])
            ->assertOk()
            ->assertDontSee('Apply Filters')
            // an off type adds itself, an on type removes itself, both back on page 1
            ->assertSee('href="' . e(route('comps.activityLog', [$this->comp, 'related' => 'serc', 'activity' => 'HEATS_RESET|HEATS_GENERATED'])) . '"', false)
            ->assertSee('href="' . e(route('comps.activityLog', [$this->comp, 'related' => 'serc'])) . '"', false);
    }

    public function test_footer_items_filter_the_log_by_themselves(): void
    {
        $this->recordActivity('SPEED_RESULT_MARKED', 'A on speed', related: [$this->speed, $this->teamA, $this->comp]);

        $response = $this->log(['related' => "team:{$this->teamA->id}"])->assertOk();

        // an inactive item adds itself
        $response->assertSee('href="' . e(route('comps.activityLog', [$this->comp, 'related' => "team:{$this->teamA->id}|speed:{$this->speed->id}"])) . '"', false);

        // an active item removes itself and shows as pressed
        $response->assertSee('href="' . e(route('comps.activityLog', $this->comp)) . '" aria-pressed="true"', false);

        // the event keeps a link to its own page
        $response->assertSee('href="' . route('comps.events.speeds.view', [$this->comp, $this->speed->id]) . '"', false);

        // the competition is already the page's filter, so it isn't clickable and has no page link
        $response->assertDontSee('comp%3A' . $this->comp->id, false);
        $response->assertDontSee('href="' . route('comps.view', $this->comp) . '" class="text-gray-500 hover:text-se" title="Open page"', false);
    }

    public function test_deleted_footer_items_arent_clickable(): void
    {
        $this->recordActivity('SPEED_RESULT_MARKED', 'A on speed', related: [$this->speed, $this->comp]);
        $speedId = $this->speed->id;
        $this->speed->delete();

        $this->log()
            ->assertOk()
            ->assertSee('Speed Event Deleted')
            ->assertDontSee('speed%3A' . $speedId, false);
    }

    public function test_the_admin_monitor_has_click_through_but_no_pickers(): void
    {
        $siteAdmin = User::factory()->create();
        $siteAdmin->forceFill(['admin' => true])->save();

        $this->recordActivity('SPEED_RESULT_MARKED', 'A on speed', user: $this->admin, related: [$this->speed, $this->teamA, $this->comp]);

        $this->actingAs($siteAdmin)->get(route('admin.activity', ['by' => $this->admin->id]))
            ->assertOk()
            ->assertSeeInOrder(['Actioned by', 'Orla Organiser', 'Clear'])
            ->assertSee('href="' . route('comps.view', $this->comp) . '" class="text-gray-500 hover:text-se" title="Open page"', false)
            ->assertSee('href="' . e(route('admin.activity', ['by' => $this->admin->id, 'related' => "comp:{$this->comp->id}"])) . '"', false)
            ->assertDontSee('Add team');
    }

    public function test_actioned_by_chips_dont_name_people_from_other_competitions(): void
    {
        $outsider = User::factory()->create(['name' => 'Olly Outsider']);

        $this->log(['by' => $outsider->id])
            ->assertOk()
            ->assertSee("#{$outsider->id}")
            ->assertDontSee('Olly Outsider');
    }

    public function test_the_competitor_picker_lists_this_competitions_competitors(): void
    {
        $competitor = new Competitor();
        $competitor->name = 'Casey Competitor';
        $competitor->team = $this->teamA->id;
        $competitor->competition = $this->comp->id;
        $competitor->save();

        $this->log()
            ->assertOk()
            ->assertSee('Add competitor')
            ->assertSee('value="competitor:' . $competitor->id . '"', false);
    }

    public function test_unknown_aliases_are_ignored(): void
    {
        $this->recordActivity('SPEED_RESULT_MARKED', 'Marks for A', related: [$this->teamA, $this->comp]);
        $this->recordActivity('SPEED_RESULT_MARKED', 'Marks for B', related: [$this->teamB, $this->comp]);

        $this->log(['related' => "nonsense:1|team:{$this->teamA->id}|:"])
            ->assertOk()
            ->assertSee('Marks for A')
            ->assertDontSee('Marks for B');
    }

    public function test_oring_in_another_competitions_team_doesnt_show_its_activity(): void
    {
        $other = $this->competition('Their Competition');
        $theirTeam = $this->team($other, 'Z');

        $this->recordActivity('SPEED_RESULT_MARKED', 'Marks for A', related: [$this->teamA, $this->comp]);
        $this->recordActivity('SPEED_RESULT_MARKED', 'Their marks', related: [$theirTeam, $other]);

        $this->log(['related' => "team:{$this->teamA->id}|team:{$theirTeam->id}"])
            ->assertOk()
            ->assertSee('Marks for A')
            ->assertDontSee('Their marks');
    }
}
