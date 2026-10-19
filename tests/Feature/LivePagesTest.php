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
use App\Models\SpeedEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->assertSee('3. ' . $this->team->getName($this->comp))
            ->assertDontSee('Heat 2')
            ->assertDontSee(route('live.dqs.event', ['se:' . $this->serc->id]), false)
            ->assertSee(route('live.dqs'), false)
            ->assertSee(route('live.display'), false);
    }

    public function test_the_dqs_tab_lists_each_event(): void
    {
        $this->get($this->url('live.dqs'))
            ->assertOk()
            ->assertSee(route('live.dqs.event', ['se:' . $this->serc->id]), false)
            ->assertSee(route('live.dqs.event', ['sp:' . $this->speed->id]), false);
    }

    public function test_the_hub_links_to_the_competition_on_the_main_site(): void
    {
        $this->get($this->url('live'))
            ->assertSee('href="' . config('app.url') . '/competition/' . $this->comp->getSlug() . '"', false);
    }

    public function test_the_display_shows_only_the_serc_order(): void
    {
        $this->get($this->url('live.display'))
            ->assertOk()
            ->assertSee('3. ' . $this->team->getName($this->comp))
            ->assertSee('<svg', false)
            ->assertDontSee('Heat 2')
            ->assertDontSee('<footer', false);
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
            ->assertSee('Did not touch the wall')
            ->assertSee('Judge Person');
    }

    public function test_an_event_with_no_dqs_says_so(): void
    {
        $this->get($this->url('live.dqs.event', ['se:' . $this->serc->id]))
            ->assertOk()
            ->assertSee('No DQs or penalties for this event');
    }
}
