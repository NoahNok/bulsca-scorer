<?php

namespace Tests\Feature;

use App\Models\Activity\Activity;
use App\Models\Activity\ActivityRelation;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\Competitor;
use App\Models\DigitalJudge\JudgeDQSubmission;
use App\Models\Orders\Heat;
use App\Models\SERC;
use App\Models\SpeedEvent;
use App\Models\User;
use App\Traits\RecordActivity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CompetitionActivityTest extends TestCase
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

        // Creating them recorded Activity of its own
        ActivityRelation::query()->delete();
        Activity::query()->delete();
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
        $query = count($query) ? '?' . http_build_query($query) : '';

        return $this->actingAs($this->admin)->get($this->appUrl("/comps/{$this->comp->id}/activity-log{$query}"));
    }

    /**
     * The descriptions of the entries shown
     */
    private function shown(array $query = []): array
    {
        $descriptions = [];

        $this->log($query)->assertOk()->assertInertia(function (Assert $page) use (&$descriptions) {
            $page->component('Manage/Competition/Activity');
            $descriptions = collect($page->toArray()['props']['entries'])->pluck('description')->all();
        });

        return $descriptions;
    }

    public function test_organisers_see_this_competitions_activity_newest_first(): void
    {
        $this->recordActivity('HEATS_GENERATED', 'First', related: $this->comp);
        $this->travel(1)->minutes();
        $this->recordActivity('HEATS_RESET', 'Second', related: $this->comp);

        $this->assertSame(['Second', 'First'], $this->shown());
    }

    /**
     * Asserts on the page's props
     */
    private function page(array $query, callable $assert): void
    {
        $this->log($query)->assertOk()->assertInertia(fn (Assert $page) => $assert($page->component('Manage/Competition/Activity')));
    }

    private function props(array $query = []): array
    {
        $props = [];
        $this->page($query, function (Assert $page) use (&$props) {
            $props = $page->toArray()['props'];
        });

        return $props;
    }

    public function test_people_without_organiser_access_are_refused(): void
    {
        $viewer = User::factory()->create();
        $this->comp->addAccount($viewer, 'view');

        $this->actingAs($viewer)->get($this->appUrl("/comps/{$this->comp->id}/activity-log"))->assertForbidden();
    }

    public function test_another_competitions_activity_is_never_shown(): void
    {
        $other = $this->competition('Their Competition');
        $theirTeam = $this->team($other, 'Z');
        $this->recordActivity('SERC_MARKED', 'Their marks', related: [$theirTeam, $other]);
        $this->recordActivity('SERC_MARKED', 'Our marks', related: [$this->teamA, $this->comp]);

        $this->assertSame(['Our marks'], $this->shown());
        $this->assertSame([], $this->shown(['related' => "team:{$theirTeam->id}"]));
        $this->assertSame(['Our marks'], $this->shown(['related' => "team:{$this->teamA->id}|team:{$theirTeam->id}"]));
    }

    public function test_two_teams_show_activity_for_either_team(): void
    {
        $this->recordActivity('SPEED_RESULT_MARKED', 'Marks for A', related: [$this->serc, $this->teamA, $this->comp]);
        $this->recordActivity('SPEED_RESULT_MARKED', 'Marks for B', related: [$this->serc, $this->teamB, $this->comp]);
        $this->recordActivity('SPEED_RESULT_MARKED', 'Marks for C', related: [$this->serc, $this->teamC, $this->comp]);

        $this->assertEqualsCanonicalizing(['Marks for A', 'Marks for B'], $this->shown(['related' => "team:{$this->teamA->id}|team:{$this->teamB->id}"]));
    }

    public function test_teams_in_an_event_only_show_those_teams_in_that_event(): void
    {
        $this->recordActivity('SPEED_RESULT_MARKED', 'A in SERC', related: [$this->serc, $this->teamA, $this->comp]);
        $this->recordActivity('SPEED_RESULT_MARKED', 'B in SERC', related: [$this->serc, $this->teamB, $this->comp]);
        $this->recordActivity('SPEED_RESULT_MARKED', 'A in speed', related: [$this->speed, $this->teamA, $this->comp]);

        $this->assertEqualsCanonicalizing(['A in SERC', 'B in SERC'], $this->shown(['related' => "team:{$this->teamA->id}|serc:{$this->serc->id}|team:{$this->teamB->id}"]));
    }

    public function test_all_sercs_shows_activity_for_any_serc(): void
    {
        $this->recordActivity('SPEED_RESULT_MARKED', 'A in SERC', related: [$this->serc, $this->teamA, $this->comp]);
        $this->recordActivity('SPEED_RESULT_MARKED', 'A in speed', related: [$this->speed, $this->teamA, $this->comp]);

        $this->assertSame(['A in SERC'], $this->shown(['related' => 'serc']));
        $this->assertEqualsCanonicalizing(['A in SERC', 'A in speed'], $this->shown(['related' => "serc:{$this->serc->id}|speed:{$this->speed->id}"]));
    }

    public function test_actioned_by_several_people_or_system_shows_any_of_them(): void
    {
        $judge = User::factory()->create(['name' => 'Jo Judge']);
        $timer = User::factory()->create(['name' => 'Tam Timer']);

        $this->recordActivity('SPEED_RESULT_MARKED', 'Done by judge', user: $judge, related: [$this->teamA, $this->comp]);
        $this->recordActivity('SPEED_RESULT_MARKED', 'Done by timer', user: $timer, related: $this->comp);
        $this->recordActivity('SPEED_RESULT_MARKED', 'Done by the system', related: $this->comp);

        $this->assertSame(['Done by judge'], $this->shown(['by' => $judge->id]));
        $this->assertEqualsCanonicalizing(['Done by judge', 'Done by the system'], $this->shown(['by' => "{$judge->id}|system|junk"]));
        $this->assertSame([], $this->shown(['by' => $judge->id, 'related' => "team:{$this->teamB->id}"]));
    }

    public function test_activity_types_filter_and_are_scoped_to_the_competition(): void
    {
        $this->recordActivity('HEATS_RESET', 'Heats reset', related: $this->comp);
        $this->recordActivity('HEATS_GENERATED', 'Heats generated', related: $this->comp);
        $this->recordActivity('DRAW_SWAP', 'Elsewhere', related: $this->competition('Their Competition'));

        $this->assertSame(['Heats reset'], $this->shown(['activity' => 'HEATS_RESET']));

        $this->page(['activity' => 'HEATS_RESET'], fn (Assert $page) => $page
            // the picked type is left out, and types only seen on other competitions aren't offered
            ->where('pickers.3.label', 'Type')
            ->where('pickers.3.param', 'activity')
            ->where('pickers.3.groups.0.options', [['value' => 'HEATS_GENERATED', 'label' => 'Heats generated']]));
    }

    public function test_entries_have_a_readable_title_who_did_them_and_when(): void
    {
        $this->recordActivity('OOF_MARKED', 'Places', user: $this->admin, related: $this->comp);
        $this->travel(1)->seconds();
        $this->recordActivity('SOMETHING_NEW_FOR_SERCS', 'Unmapped', related: $this->comp);

        $props = $this->props();

        $this->assertSame('Something new for SERCs', $props['entries'][0]['title']);
        $this->assertNull($props['entries'][0]['by']);
        $this->assertSame('Places entered', $props['entries'][1]['title']);
        $this->assertSame('OOF_MARKED', $props['entries'][1]['type']);
        $this->assertSame('Orla Organiser', $props['entries'][1]['by']);
        $this->assertNotFalse(\DateTime::createFromFormat(\DateTime::ATOM, $props['entries'][1]['at']));
    }

    public function test_marks_and_places_come_with_a_table_of_changes(): void
    {
        $this->recordActivity('SERC_RESULT_UPDATED', 'Marks', context: ['changes' => [['name' => 'Judge 1 - Airway', 'old' => 4, 'new' => 6], ['name' => 'Judge 1 - Rescue', 'old' => null, 'new' => 3]]], related: $this->comp);
        $this->recordActivity('SPEED_RESULT_UPDATED', 'Time', context: ['changes' => ['result' => ['old' => '01:00.000', 'new' => 'DNF']]], related: $this->comp);
        $this->travel(1)->minutes();
        $this->recordActivity('OOF_MARKED', 'Places', context: ['heat' => 1, 'changes' => [['entity' => $this->teamA->id, 'name' => 'Team A', 'old' => '—', 'new' => 2]]], related: $this->comp);

        $entries = collect($this->props()['entries'])->keyBy('description');

        $this->assertSame([
            'columns' => ['label' => 'Team', 'old' => 'Old place', 'new' => 'New place'],
            'rows' => [['label' => 'Team A', 'old' => '—', 'new' => '2']],
        ], $entries['Places']['changes']);
        $this->assertSame([
            'columns' => ['label' => 'Marking point', 'old' => 'Old mark', 'new' => 'New mark'],
            'rows' => [['label' => 'Judge 1 - Airway', 'old' => '4', 'new' => '6'], ['label' => 'Judge 1 - Rescue', 'old' => null, 'new' => '3']],
        ], $entries['Marks']['changes']);
        $this->assertNull($entries['Time']['changes']);
    }

    public function test_related_items_filter_by_themselves_and_link_to_their_page(): void
    {
        $this->recordActivity('SPEED_RESULT_MARKED', 'A on speed', related: [$this->speed, $this->teamA, $this->comp]);

        $related = collect($this->props(['related' => "team:{$this->teamA->id}"])['entries'][0]['related'])->keyBy('kind');

        $this->assertSame([
            'kind' => 'speed',
            'name' => 'Swim & Tow',
            'detail' => null,
            'state' => 'present',
            'link' => route('manage.competitions.events.speeds.show', [$this->comp, $this->speed->id]),
            'filter' => ['value' => "speed:{$this->speed->id}", 'active' => false],
        ], $related['speed']);
        $this->assertSame(['value' => "team:{$this->teamA->id}", 'active' => true], $related['team']['filter']);
        $this->assertNull($related['team']['link']);

        // the competition is what the page is already forced to
        $this->assertSame('Our Competition', $related['comp']['name']);
        $this->assertNull($related['comp']['filter']);
        $this->assertNull($related['comp']['link']);
    }

    public function test_deleted_related_items_say_so_and_do_nothing(): void
    {
        $this->recordActivity('SPEED_RESULT_MARKED', 'A on speed', related: [$this->speed, $this->comp]);
        $this->speed->delete();

        $speed = collect($this->props()['entries'][0]['related'])->firstWhere('kind', 'speed');

        $this->assertSame(['kind' => 'speed', 'name' => 'Speed event deleted', 'detail' => null, 'state' => 'deleted', 'link' => null, 'filter' => null], $speed);
    }

    public function test_chips_are_grouped_by_kind_and_only_name_this_competitions_things(): void
    {
        $judge = User::factory()->create(['name' => 'Jo Judge']);
        $outsider = User::factory()->create(['name' => 'Olly Outsider']);
        $theirTeam = $this->team($this->competition('Their Competition'), 'Z');
        $this->recordActivity('SPEED_RESULT_MARKED', 'Done by judge', user: $judge, related: $this->comp);

        $this->page([
            'related' => "team:{$this->teamA->id}|team:{$theirTeam->id}|serc|serc:{$this->serc->id}",
            'by' => "{$judge->id}|{$outsider->id}|system",
            'activity' => 'HEATS_RESET',
        ], fn (Assert $page) => $page->where('chipGroups', [
            ['label' => 'Type', 'chips' => [['label' => 'Heats reset', 'param' => 'activity', 'value' => 'HEATS_RESET']]],
            ['label' => 'Event', 'chips' => [
                ['label' => 'All SERCs', 'param' => 'related', 'value' => 'serc'],
                ['label' => 'Dry SERC', 'param' => 'related', 'value' => "serc:{$this->serc->id}"],
            ]],
            ['label' => 'Team', 'chips' => [
                ['label' => $this->teamA->getName($this->comp), 'param' => 'related', 'value' => "team:{$this->teamA->id}"],
                ['label' => "#{$theirTeam->id}", 'param' => 'related', 'value' => "team:{$theirTeam->id}"],
            ]],
            ['label' => 'Actioned by', 'chips' => [
                ['label' => 'Jo Judge', 'param' => 'by', 'value' => (string) $judge->id],
                ['label' => "#{$outsider->id}", 'param' => 'by', 'value' => (string) $outsider->id],
                ['label' => 'System', 'param' => 'by', 'value' => 'system'],
            ]],
        ])->where('filters.related', ['team:' . $this->teamA->id, "team:{$theirTeam->id}", 'serc', "serc:{$this->serc->id}"]));
    }

    public function test_pickers_offer_this_competitions_events_teams_and_people_leaving_out_picked_ones(): void
    {
        $judge = User::factory()->create(['name' => 'Jo Judge']);
        $this->recordActivity('SPEED_RESULT_MARKED', 'Done by judge', user: $judge, related: $this->comp);

        $other = $this->competition('Their Competition');
        $outsider = User::factory()->create(['name' => 'Olly Outsider']);
        $this->recordActivity('SPEED_RESULT_MARKED', 'Elsewhere', user: $outsider, related: [$this->team($other, 'Z'), $other]);

        $pickers = collect($this->props(['related' => "serc|team:{$this->teamB->id}"])['pickers'])->keyBy('label');

        $this->assertSame(['Event', 'Team', 'Actioned by', 'Type'], $pickers->keys()->all());
        $this->assertSame([
            ['label' => null, 'options' => [['value' => 'speed', 'label' => 'All speed events']]],
            ['label' => 'SERCs', 'options' => [['value' => "serc:{$this->serc->id}", 'label' => 'Dry SERC']]],
            ['label' => 'Speed events', 'options' => [['value' => "speed:{$this->speed->id}", 'label' => 'Swim & Tow']]],
        ], $pickers['Event']['groups']);
        $this->assertSame(["team:{$this->teamA->id}", "team:{$this->teamC->id}"], collect($pickers['Team']['groups'][0]['options'])->pluck('value')->sort()->values()->all());
        $this->assertSame([['value' => (string) $judge->id, 'label' => 'Jo Judge'], ['value' => 'system', 'label' => 'System']], $pickers['Actioned by']['groups'][0]['options']);
    }

    public function test_the_competitor_picker_lists_this_competitions_competitors(): void
    {
        $competitor = new Competitor();
        $competitor->name = 'Casey Competitor';
        $competitor->team = $this->teamA->id;
        $competitor->competition = $this->comp->id;
        $competitor->save();

        $this->page([], fn (Assert $page) => $page
            ->where('pickers.2.label', 'Competitor')
            ->where('pickers.2.groups.0.options.0.value', "competitor:{$competitor->id}"));
    }

    public function test_the_history_shortcut_is_marks_times_and_places(): void
    {
        $history = ['SERC_MARKED', 'SERC_RESULT_UPDATED', 'SPEED_RESULT_MARKED', 'SPEED_RESULT_UPDATED', 'OOF_MARKED'];

        $this->page([], fn (Assert $page) => $page->where('history', ['value' => $history, 'active' => false]));
        $this->page(['activity' => 'OOF_MARKED|SPEED_RESULT_UPDATED|SPEED_RESULT_MARKED|SERC_RESULT_UPDATED|SERC_MARKED'], fn (Assert $page) => $page->where('history.active', true));
        $this->page(['activity' => 'OOF_MARKED|SERC_MARKED'], fn (Assert $page) => $page->where('history.active', false));
    }

    public function test_activity_comes_twenty_at_a_time(): void
    {
        for ($i = 1; $i <= 25; $i++) {
            $this->recordActivity('HEATS_RESET', "Reset {$i}", related: $this->comp);
            $this->travel(1)->seconds();
        }

        $this->page([], fn (Assert $page) => $page->has('entries', 20)->where('entries.0.description', 'Reset 25')->where('olderPage', 2));
        $this->page(['page' => 2], fn (Assert $page) => $page->has('entries', 5)->where('entries.0.description', 'Reset 5')->where('olderPage', null));
    }

    public function test_unknown_aliases_are_ignored(): void
    {
        $this->recordActivity('SPEED_RESULT_MARKED', 'Marks for A', related: [$this->teamA, $this->comp]);
        $this->recordActivity('SPEED_RESULT_MARKED', 'Marks for B', related: [$this->teamB, $this->comp]);

        $this->assertSame(['Marks for A'], $this->shown(['related' => "nonsense:1|team:{$this->teamA->id}|:"]));
    }

    public function test_an_appealed_submission_is_marked_as_appealed(): void
    {
        $heat = new Heat();
        $heat->speed_event = $this->speed->id;
        $heat->entity()->associate($this->teamA);
        $heat->heat = 1;
        $heat->lane = 1;
        $heat->save();

        $submission = new JudgeDQSubmission();
        $submission->competition = $this->comp->id;
        $submission->event_id = $this->speed->id;
        $submission->event_type = CompetitionSpeedEvent::class;
        $submission->heat_lane = $heat->id;
        $submission->code = 'DQ012';
        $submission->name = 'Reporting Judge';
        $submission->position = 'Turn judge';
        $submission->appealed = true;
        $submission->save();

        $this->recordActivity('DISQUALIFICATION_SUBMISSION', 'Submitted', related: [$submission, $this->comp]);

        $item = collect($this->props()['entries'][0]['related'])->firstWhere('kind', 'submission');

        $this->assertSame('Reporting Judge (Turn judge)', $item['name']);
        $this->assertSame('appealed', $item['state']);
        $this->assertSame(['value' => "submission:{$submission->id}", 'active' => false], $item['filter']);
    }

    public function test_a_value_repeated_in_the_url_counts_once(): void
    {
        $this->page(['related' => "team:{$this->teamA->id}|team:{$this->teamA->id}", 'by' => 'system|system'], fn (Assert $page) => $page
            ->where('filters.related', ["team:{$this->teamA->id}"])
            ->has('chipGroups.0.chips', 1)
            ->has('chipGroups.1.chips', 1));
    }
}
