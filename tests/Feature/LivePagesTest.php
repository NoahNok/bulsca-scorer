<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\DigitalJudge\JudgeDQSubmission;
use App\Models\DQCode;
use App\Models\Orders\Draw;
use App\Models\Orders\Heat;
use App\Models\SERC;
use App\Models\SERCJudge;
use App\Models\SERCMarkingPoint;
use App\Models\SERCResult;
use App\Models\User;
use App\Models\SpeedEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * The live hub shows competitors where they are in the SERC order, their speed heats and the DQs,
 * and the display page shows just the SERC order for a screen at the pool.
 */
class LivePagesTest extends TestCase
{
    use RefreshDatabase;

    private Competition $comp;
    private SERC $serc;
    private CompetitionSpeedEvent $speed;
    private CompetitionTeam $team;
    private Heat $heat;

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
        $this->comp->show_heats = true;
        $this->comp->save();

        $this->serc = new SERC();
        $this->serc->name = 'Dry SERC';
        $this->serc->competition = $this->comp->id;
        $this->serc->scorable_entity = 'team';
        $this->serc->save();

        $event = new SpeedEvent();
        $event->name = 'Swim & Tow';
        $event->save();

        $this->speed = new CompetitionSpeedEvent();
        $this->speed->event = $event->id;
        $this->speed->competition = $this->comp->id;
        $this->speed->record = 0;
        $this->speed->save();

        $this->team = new CompetitionTeam();
        $this->team->competition = $this->comp->id;
        $this->team->team = 'A';
        $this->team->save();

        $draw = new Draw();
        $draw->serc = $this->serc->id;
        $draw->entity()->associate($this->team);
        $draw->tank = 1;
        $draw->draw = 3;
        $draw->save();

        $this->heat = new Heat();
        $this->heat->speed_event = $this->speed->id;
        $this->heat->entity()->associate($this->team);
        $this->heat->heat = 2;
        $this->heat->lane = 5;
        $this->heat->save();
    }

    private function url(string $route, array $params = []): string
    {
        return route($route, $params) . '?comp=' . $this->comp->id;
    }

    public function test_the_hub_shows_the_serc_order(): void
    {
        $this->get($this->url('live'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Live/Index')
                ->where('competition.name', 'Test Competition')
                ->where('competition.links.dqs', route('live.dqs', ['comp' => $this->comp->id]))
                ->where('competition.links.display', route('live.display', ['comp' => $this->comp->id]))
                ->where('competition.dataUrl', route('live.data', $this->comp->id))
                ->where('sercOrder.tanks.0.teams.0', ['id' => $this->team->id, 'draw' => 3, 'name' => $this->team->getName($this->comp)])
                ->where('sercOrder.sercCount', 1)
                ->missing('auth.user.email'));
    }

    public function test_the_hub_links_to_the_heats_and_draws_on_the_main_site(): void
    {
        $this->get($this->url('live'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('competition.links.heatsDraws', config('app.url') . '/competition/' . $this->comp->getSlug() . '/heats-and-draws')
                ->where('live.site.explore', config('app.url') . '/explore'));
    }

    public function test_the_dqs_tab_lists_each_event(): void
    {
        $this->get($this->url('live.dqs'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Live/Dqs')
                ->where('groups.0.title', 'SERCs')
                ->where('groups.0.events.0.url', route('live.dqs.event', ['se:' . $this->serc->id, 'comp' => $this->comp->id]))
                ->where('groups.1.title', 'Speeds')
                ->where('groups.1.events.0.url', route('live.dqs.event', ['sp:' . $this->speed->id, 'comp' => $this->comp->id])));
    }

    public function test_the_display_shows_only_the_serc_order(): void
    {
        $this->get($this->url('live.display'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Live/Display')
                ->where('sercOrder.tanks.0.teams.0.draw', 3)
                ->where('qr', fn ($qr) => str_contains($qr, '<svg')));
    }

    public function test_an_event_lists_its_resolved_dqs_with_the_code_description(): void
    {
        $code = new DQCode();
        $code->code = 100;
        $code->description = 'Did not touch the wall';
        $code->save();

        JudgeDQSubmission::create([
            'competition' => $this->comp->id,
            'event_type' => CompetitionSpeedEvent::class,
            'event_id' => $this->speed->id,
            'heat_lane' => $this->heat->id,
            'code' => 'DQ' . $code->id,
            'name' => 'Judge Person',
            'position' => 'Turn judge',
            'resolved' => true,
        ]);

        $this->get($this->url('live.dqs.event', ['sp:' . $this->speed->id]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Live/EventDqs')
                ->where('dqs.0.description', 'Did not touch the wall')
                ->where('dqs.0.reporter', ['Judge Person', 'Turn judge'])
                ->where('dqs.0.placement', ['Heat' => 2, 'Lane' => 5]));
    }

    public function test_an_event_with_no_dqs_says_so(): void
    {
        $this->get($this->url('live.dqs.event', ['se:' . $this->serc->id]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Live/EventDqs')->where('dqs', []));
    }

    public function test_without_a_live_competition_the_past_ones_are_listed(): void
    {
        $this->comp->can_be_live = false;
        $this->comp->public_results = true;
        $this->comp->when = now()->subWeek();
        $this->comp->save();

        $this->get(route('live'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Live/Unavailable')
                ->where('competitions.0.url', route('live', ['comp' => $this->comp->id]))
                ->where('pagination.current', 1));
    }

    public function test_the_live_data_estimates_when_each_team_starts(): void
    {
        $this->comp->serc_start_time = now()->addHour();
        $this->comp->save();

        $this->get(route('live.data', ['comp' => $this->comp]))
            ->assertOk()
            ->assertJsonPath("teams.{$this->team->id}.finished", 0)
            ->assertJsonPath("teams.{$this->team->id}.next", true)
            ->assertJsonPath("teams.{$this->team->id}.start", $this->comp->serc_start_time->timestamp);
    }

    public function test_the_status_message_shows_on_the_live_pages_and_comes_with_each_poll(): void
    {
        $this->get(route('live.data', ['comp' => $this->comp]))->assertOk()->assertJsonPath('statusMessage', null);
        $this->get($this->url('live'))->assertInertia(fn (Assert $page) => $page->where('competition.statusMessage', null));

        $this->comp->status_message = '<p>Warm-up moved to 9:30</p>';
        $this->comp->save();

        $this->get(route('live.data', ['comp' => $this->comp]))->assertJsonPath('statusMessage', '<p>Warm-up moved to 9:30</p>');
        $this->get($this->url('live.display'))->assertInertia(fn (Assert $page) => $page->where('competition.statusMessage', '<p>Warm-up moved to 9:30</p>'));
    }

    private function mark(SERC $serc, CompetitionTeam $team): void
    {
        $judge = new SERCJudge();
        $judge->name = 'Casualty';
        $judge->description = '';
        $judge->serc = $serc->id;
        $judge->save();

        $point = new SERCMarkingPoint();
        $point->name = 'Approach';
        $point->weight = 1;
        $point->judge = $judge->id;
        $point->serc = $serc->id;
        $point->save();

        $mark = new SERCResult();
        $mark->marking_point = $point->id;
        $mark->entity()->associate($team);
        $mark->result = 5;
        $mark->save();
    }

    public function test_only_dry_serc_marks_start_a_team_in_the_serc_order(): void
    {
        $wet = new SERC();
        $wet->name = 'Wet SERC';
        $wet->type = 'WET';
        $wet->competition = $this->comp->id;
        $wet->scorable_entity = 'team';
        $wet->save();

        $later = new CompetitionTeam();
        $later->competition = $this->comp->id;
        $later->team = 'B';
        $later->save();

        $draw = new Draw();
        $draw->serc = $this->serc->id;
        $draw->entity()->associate($later);
        $draw->tank = 1;
        $draw->draw = 4;
        $draw->save();
        $this->comp->clearDrawCache();

        // The later Team has done the wet SERC, run in another order: it's still to start the dry one, and
        // the Team ahead of it isn't skipped
        $this->mark($wet, $later);

        $this->get(route('live.data', ['comp' => $this->comp]))
            ->assertOk()
            ->assertJsonPath("teams.{$this->team->id}.next", true)
            ->assertJsonPath("teams.{$this->team->id}.start", fn ($start) => $start !== null)
            ->assertJsonPath("teams.{$later->id}.finished", 1)
            ->assertJsonPath("teams.{$later->id}.start", fn ($start) => $start !== null);
    }

    public function test_the_live_pages_share_nothing_about_who_is_signed_in(): void
    {
        $this->actingAs(User::factory()->create(['email' => 'organiser@example.com']))
            ->get($this->url('live.display'))
            ->assertOk()
            ->assertDontSee('organiser@example.com')
            ->assertInertia(fn (Assert $page) => $page->missing('auth')->has('legal')->has('live'));
    }

    public function test_the_past_competitions_pages_keep_the_comp_in_the_url(): void
    {
        $this->comp->can_be_live = false;
        $this->comp->public_results = true;
        $this->comp->when = now()->subWeek();
        $this->comp->save();

        foreach (range(1, 10) as $i) {
            $this->comp->replicate()->save();
        }

        $this->get(route('live', ['comp' => 999999]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Live/Unavailable')
                ->where('pagination.next', fn ($next) => str_contains($next, 'comp=999999') && str_contains($next, 'page=2')));
    }

    public function test_several_devices_on_one_ip_can_poll_the_live_data(): void
    {
        // A few phones on the venue wifi polling every 5s for a minute
        foreach (range(1, 60) as $i) {
            $this->get(route('live.data', ['comp' => $this->comp]))->assertOk();
        }
    }
}
