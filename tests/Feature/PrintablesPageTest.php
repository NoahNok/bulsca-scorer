<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\Orders\Draw;
use App\Models\Orders\Heat;
use App\Models\SERC;
use App\Models\SpeedEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * Printables (Svelte, in CompetitionLayout): a row per Pack linking to its PDF, or saying why there's
 * nothing to print yet and where to fix it.
 */
class PrintablesPageTest extends TestCase
{
    use RefreshDatabase;

    private User $organiser;

    private Competition $comp;

    private CompetitionSpeedEvent $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organiser = User::factory()->create();

        $this->comp = new Competition();
        $this->comp->name = 'Print Champs';
        $this->comp->when = now()->addWeek();
        $this->comp->where = 'Test Pool';
        $this->comp->max_lanes = 4;
        $this->comp->anytimepin = false;
        $this->comp->seed_per_event = false;
        $this->comp->heats_per_event = false;
        $this->comp->use_seeds = false;
        $this->comp->save();
        $this->comp->addAccount($this->organiser, ['owner']);

        $base = new SpeedEvent();
        $base->name = 'Swim & Tow';
        $base->save();

        $this->event = new CompetitionSpeedEvent();
        $this->event->event = $base->id;
        $this->event->competition = $this->comp->id;
        $this->event->record = 0;
        $this->event->save();
    }

    private function url(): string
    {
        return $this->appUrl('/comps/' . $this->comp->id . '/printables');
    }

    private function team(string $letter): CompetitionTeam
    {
        return CompetitionTeam::create(['competition' => $this->comp->id, 'team' => $letter]);
    }

    private function heat(CompetitionTeam $team): void
    {
        $row = new Heat();
        $row->speed_event = $this->event->id;
        $row->entity()->associate($team);
        $row->heat = 1;
        $row->lane = 1;
        $row->save();
    }

    private function serc(string $name): SERC
    {
        $serc = new SERC();
        $serc->name = $name;
        $serc->type = 'DRY';
        $serc->scorable_entity = 'team';
        $serc->competition = $this->comp->id;
        $serc->save();

        return $serc;
    }

    private function draw(SERC $serc, CompetitionTeam $team): void
    {
        $row = new Draw();
        $row->serc = $serc->id;
        $row->entity()->associate($team);
        $row->tank = 1;
        $row->draw = 1;
        $row->save();
    }

    public function test_with_heats_it_offers_both_chief_timekeeper_layouts_and_speed_marshalling(): void
    {
        $this->heat($this->team('A'));

        $this->actingAs($this->organiser)->get($this->url())
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Competition/Printables')
                ->where('packs.chiefTimekeeper.url', route('comps.printables.chief-timekeeper-pack', $this->comp))
                ->where('packs.chiefTimekeeper.packedUrl', route('comps.printables.chief-timekeeper-pack', [$this->comp, 'layout' => 'packed']))
                ->where('packs.chiefTimekeeper.unavailable', null)
                ->where('packs.speedMarshalling.url', route('comps.printables.marshalling', $this->comp))
                ->where('packs.speedMarshalling.unavailable', null));
    }

    public function test_without_heats_the_speed_packs_say_so_and_link_to_heats_and_draws(): void
    {
        $noHeats = ['reason' => 'No heats yet', 'url' => route('manage.competitions.heats_and_draws', $this->comp)];

        $this->actingAs($this->organiser)->get($this->url())
            ->assertInertia(fn (Assert $page) => $page
                ->where('packs.chiefTimekeeper.url', null)
                ->where('packs.chiefTimekeeper.packedUrl', null)
                ->where('packs.chiefTimekeeper.unavailable', $noHeats)
                ->where('packs.speedMarshalling.url', null)
                ->where('packs.speedMarshalling.unavailable', $noHeats));
    }

    public function test_without_sercs_the_serc_packs_say_so_and_link_to_events(): void
    {
        $noSercs = ['reason' => 'No SERCs yet', 'url' => route('manage.competitions.events', $this->comp)];

        $this->actingAs($this->organiser)->get($this->url())
            ->assertInertia(fn (Assert $page) => $page
                ->where('packs.sercMarking.url', null)
                ->where('packs.sercMarking.unavailable', $noSercs)
                ->where('packs.sercMarshalling.url', null)
                ->where('packs.sercMarshalling.unavailable', $noSercs)
                ->where('roughMarkSheets.sercs', [])
                ->where('roughMarkSheets.unavailable', $noSercs));
    }

    public function test_with_sercs_but_no_draw_the_draw_packs_say_so_and_link_to_heats_and_draws(): void
    {
        $serc = $this->serc('Dry SERC');
        $noDraw = ['reason' => 'No draw yet', 'url' => route('manage.competitions.heats_and_draws', $this->comp)];

        $this->actingAs($this->organiser)->get($this->url())
            ->assertInertia(fn (Assert $page) => $page
                ->where('packs.sercMarking.url', null)
                ->where('packs.sercMarking.unavailable', $noDraw)
                ->where('packs.sercMarshalling.url', null)
                ->where('packs.sercMarshalling.unavailable', $noDraw)
                ->where('roughMarkSheets.sercs', [
                    ['id' => $serc->id, 'name' => 'Dry SERC', 'url' => route('comps.printables.serc-sheets', [$this->comp, $serc])],
                ])
                ->where('roughMarkSheets.unavailable', null));
    }

    public function test_with_a_draw_every_serc_pack_can_be_printed(): void
    {
        $first = $this->serc('Dry SERC');
        $second = $this->serc('Wet SERC');
        $this->draw($first, $this->team('A'));

        $this->actingAs($this->organiser)->get($this->url())
            ->assertInertia(fn (Assert $page) => $page
                ->where('packs.sercMarking.url', route('comps.printables.serc-marking-pack', $this->comp))
                ->where('packs.sercMarking.unavailable', null)
                ->where('packs.sercMarshalling.url', route('comps.printables.marshalling', [$this->comp, 'type' => 'serc']))
                ->where('packs.sercMarshalling.unavailable', null)
                ->where('roughMarkSheets.sercs.0.id', $first->id)
                ->where('roughMarkSheets.sercs.1.id', $second->id));
    }

    public function test_reasons_link_only_to_pages_the_user_can_open(): void
    {
        $printer = User::factory()->create();
        $this->comp->addAccount($printer, ['printables']);

        $this->actingAs($printer)->get($this->url())
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('packs.chiefTimekeeper.unavailable', ['reason' => 'No heats yet', 'url' => null])
                ->where('roughMarkSheets.unavailable', ['reason' => 'No SERCs yet', 'url' => null]));
    }

    public function test_it_needs_printables_access(): void
    {
        $viewer = User::factory()->create();
        $this->comp->addAccount($viewer, ['speed']);

        $this->actingAs($viewer)->get($this->url())->assertForbidden();
    }
}
