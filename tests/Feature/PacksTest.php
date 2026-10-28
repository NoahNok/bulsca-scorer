<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\Orders\Draw;
use App\Models\Orders\Heat;
use App\Models\Pool;
use App\Models\SERC;
use App\Models\SERCJudge;
use App\Models\SERCMarkingPoint;
use App\Models\SpeedEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;
use Tests\TestCase;

/**
 * Packs are rendered to PDF server-side. These tests check which view each Pack renders and
 * what it shows; the PDF engine itself is faked, so page layout is checked by eye.
 */
class PacksTest extends TestCase
{
    use RefreshDatabase;

    private Competition $comp;
    private CompetitionSpeedEvent $event;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Pdf::fake();

        $this->comp = new Competition();
        $this->comp->name = 'Pack Competition';
        $this->comp->when = now()->addWeek();
        $this->comp->where = 'Test Pool';
        $this->comp->max_lanes = 4;
        $this->comp->anytimepin = false;
        $this->comp->seed_per_event = false;
        $this->comp->heats_per_event = false;
        $this->comp->use_seeds = false;
        $this->comp->use_pools = false;
        $this->comp->team_format = ':N';
        $this->comp->save();

        $base = new SpeedEvent();
        $base->name = 'Swim & Tow';
        $base->save();

        $this->event = new CompetitionSpeedEvent();
        $this->event->event = $base->id;
        $this->event->competition = $this->comp->id;
        $this->event->record = 0;
        $this->event->save();

        $this->admin = User::factory()->create();
        $this->comp->addAccount($this->admin, 'admin');
    }

    private function pool(string $name, int $lanes): Pool
    {
        $this->comp->use_pools = true;
        $this->comp->save();

        $pool = new Pool();
        $pool->competition = $this->comp->id;
        $pool->name = $name;
        $pool->lanes = $lanes;
        $pool->save();

        return $pool;
    }

    private function entity(string $name): CompetitionTeam
    {
        $team = new CompetitionTeam();
        $team->competition = $this->comp->id;
        $team->team = $name;
        $team->save();

        return $team;
    }

    private function team(string $name, int $heat, int $lane, ?Pool $pool = null): CompetitionTeam
    {
        $team = $this->entity($name);

        $row = new Heat();
        $row->speed_event = $this->event->id;
        $row->entity()->associate($team);
        $row->heat = $heat;
        $row->lane = $lane;
        $row->pool = $pool?->id;
        $row->save();

        return $team;
    }

    private function serc(string $name): SERC
    {
        $serc = new SERC();
        $serc->name = $name;
        $serc->competition = $this->comp->id;
        $serc->save();

        return $serc;
    }

    private function draw(SERC $serc, CompetitionTeam $team, int $tank, int $draw): void
    {
        $row = new Draw();
        $row->serc = $serc->id;
        $row->entity()->associate($team);
        $row->tank = $tank;
        $row->draw = $draw;
        $row->save();
    }

    private function useTanks(): void
    {
        $settings = $this->comp->getScoringSettings;
        $settings->use_tanks = true;
        $settings->save();
    }

    public function test_the_chief_timekeeper_pack_is_an_inline_pdf_with_a_block_per_heat(): void
    {
        $this->team('Alpha', 1, 1);
        $this->team('Bravo', 2, 3);

        $this->actingAs($this->admin)
            ->get(route('comps.printables.chief-timekeeper-pack', $this->comp))
            ->assertOk();

        Pdf::assertRespondedWithPdf(function (PdfBuilder $pdf) {
            return $pdf->viewName === 'pdfs.heats.chief-timekeeper'
                && $pdf->isInline()
                && $pdf->downloadName === 'pack-competition-chief-timekeeper-pack.pdf'
                && $pdf->contains(['Swim &amp; Tow · Heat 1', 'Alpha', 'Swim &amp; Tow · Heat 2', 'Bravo']);
        });
    }

    public function test_the_chief_timekeeper_pack_gives_each_pool_its_own_section(): void
    {
        $main = $this->pool('Main Pool', 8);
        $diving = $this->pool('Diving Pool', 4);
        $this->team('Main Team', 1, 4, $main);
        $this->team('Diving Team', 1, 2, $diving);

        $this->actingAs($this->admin)
            ->get(route('comps.printables.chief-timekeeper-pack', $this->comp))
            ->assertOk();

        Pdf::assertRespondedWithPdf(function (PdfBuilder $pdf) {
            $sections = $pdf->viewData['sections'];

            return count($sections) === 2
                && $sections[0]['pool']->name === 'Main Pool'
                && $sections[1]['pool']->name === 'Diving Pool'
                && $pdf->contains(['1x Swim &amp; Tow Main Pool Heats', '1x Swim &amp; Tow Diving Pool Heats'])
                && $pdf->contains(['Swim &amp; Tow · Main Pool · Heat 1', 'Main Team', 'Swim &amp; Tow · Diving Pool · Heat 1', 'Diving Team']);
        });
    }

    public function test_the_chief_timekeeper_pack_is_one_heat_per_page_unless_packed(): void
    {
        $this->team('Alpha', 1, 1);

        $this->actingAs($this->admin)->get(route('comps.printables.chief-timekeeper-pack', $this->comp));
        $this->actingAs($this->admin)->get(route('comps.printables.chief-timekeeper-pack', [$this->comp, 'layout' => 'packed']));

        Pdf::assertRespondedWithPdf(fn(PdfBuilder $pdf) => $pdf->viewData['onePerPage'] === true);
        Pdf::assertRespondedWithPdf(fn(PdfBuilder $pdf) => $pdf->viewData['onePerPage'] === false);
    }

    public function test_the_speed_marshalling_pack_lists_each_heats_lanes_without_pool_end_names(): void
    {
        $this->team('Alpha', 1, 1);
        $this->team('Bravo', 1, 2);
        $this->team('Charlie', 2, 1);

        $this->actingAs($this->admin)
            ->get(route('comps.printables.marshalling', $this->comp))
            ->assertOk();

        Pdf::assertRespondedWithPdf(function (PdfBuilder $pdf) {
            return $pdf->viewName === 'pdfs.marshalling'
                && $pdf->downloadName === 'pack-competition-speed-marshalling-pack.pdf'
                && $pdf->contains(['Speeds · Heat 1', 'Lane 1: Alpha', 'Lane 2: Bravo', 'Speeds · Heat 2', 'Lane 1: Charlie'])
                && !$pdf->contains('Diving Pit End')
                && !$pdf->contains('Scoreboard End');
        });
    }

    public function test_the_speed_marshalling_pack_gives_each_pool_its_own_section(): void
    {
        $main = $this->pool('Main Pool', 8);
        $diving = $this->pool('Diving Pool', 4);
        $this->team('Main Team', 1, 4, $main);
        $this->team('Diving Team', 1, 2, $diving);

        $this->actingAs($this->admin)
            ->get(route('comps.printables.marshalling', $this->comp))
            ->assertOk();

        Pdf::assertRespondedWithPdf(function (PdfBuilder $pdf) {
            $sections = $pdf->viewData['sections'];

            return count($sections) === 2
                && $sections[0]['title'] === 'Speeds · Main Pool'
                && $sections[1]['title'] === 'Speeds · Diving Pool'
                && $pdf->contains(['Speeds · Main Pool · Heat 1', 'Lane 4: Main Team', 'Speeds · Diving Pool · Heat 1', 'Lane 2: Diving Team']);
        });
    }

    public function test_the_serc_marshalling_pack_lists_each_tanks_draws_per_serc(): void
    {
        $serc = $this->serc('Dry SERC');
        $this->draw($serc, $this->entity('Alpha'), 1, 1);
        $this->draw($serc, $this->entity('Bravo'), 1, 2);
        $this->draw($serc, $this->entity('Charlie'), 2, 1);

        $this->actingAs($this->admin)
            ->get(route('comps.printables.marshalling', [$this->comp, 'type' => 'serc']))
            ->assertOk();

        Pdf::assertRespondedWithPdf(function (PdfBuilder $pdf) {
            return $pdf->downloadName === 'pack-competition-serc-marshalling-pack.pdf'
                && $pdf->viewData['sections'][0]['title'] === 'Dry SERC'
                && $pdf->contains(['Tank 1', '1: Alpha', '2: Bravo', 'Tank 2', '1: Charlie']);
        });
    }

    public function test_the_serc_marshalling_pack_with_tanks_lists_each_draw_across_tanks(): void
    {
        $this->useTanks();
        $serc = $this->serc('Dry SERC');
        $this->draw($serc, $this->entity('Alpha'), 1, 1);
        $this->draw($serc, $this->entity('Bravo'), 2, 1);
        $this->draw($serc, $this->entity('Charlie'), 1, 2);

        $this->actingAs($this->admin)
            ->get(route('comps.printables.marshalling', [$this->comp, 'type' => 'serc']))
            ->assertOk();

        Pdf::assertRespondedWithPdf(function (PdfBuilder $pdf) {
            return $pdf->contains(['Draw 1', 'Tank 1: Alpha', 'Tank 2: Bravo', 'Draw 2', 'Tank 1: Charlie', 'Tank 2: -']);
        });
    }

    public function test_the_serc_marking_pack_has_a_sheet_per_draw_for_every_serc(): void
    {
        $dry = $this->serc('Dry SERC');
        $wet = $this->serc('Wet SERC');
        $alpha = $this->entity('Alpha');
        $bravo = $this->entity('Bravo');
        // Draws are shared by every SERC
        foreach ([$dry, $wet] as $serc) {
            $this->draw($serc, $alpha, 1, 1);
            $this->draw($serc, $bravo, 2, 1);
        }

        $this->actingAs($this->admin)
            ->get(route('comps.printables.serc-marking-pack', $this->comp))
            ->assertOk();

        Pdf::assertRespondedWithPdf(function (PdfBuilder $pdf) {
            return $pdf->viewName === 'pdfs.sercs.serc-marking'
                && $pdf->downloadName === 'pack-competition-serc-marking-pack.pdf'
                && $pdf->contains(['Dry SERC', 'Tank 1 | Draw 1', 'Alpha', 'Tank 2 | Draw 1', 'Bravo', 'Wet SERC', 'Alpha', 'Bravo'])
                && !$pdf->contains('Background graphics');
        });
    }

    public function test_the_rough_mark_sheets_have_a_table_of_teams_per_objective(): void
    {
        $serc = $this->serc('Dry SERC');
        foreach (['Casualty 1', 'Casualty 2'] as $name) {
            $judge = new SERCJudge();
            $judge->name = $name;
            $judge->description = '';
            $judge->serc = $serc->id;
            $judge->save();

            $point = new SERCMarkingPoint();
            $point->name = "$name approach";
            $point->weight = 1;
            $point->judge = $judge->id;
            $point->serc = $serc->id;
            $point->save();
        }
        $this->entity('Alpha');

        $this->actingAs($this->admin)
            ->get(route('comps.printables.serc-sheets', [$this->comp, $serc]))
            ->assertOk();

        Pdf::assertRespondedWithPdf(function (PdfBuilder $pdf) {
            return $pdf->viewName === 'pdfs.sercs.raw-marking'
                && $pdf->downloadName === 'pack-competition-dry-serc-rough-mark-sheets.pdf'
                && $pdf->contains(['Casualty 1 | Dry SERC', 'Casualty 1 approach', 'Alpha', 'Casualty 2 | Dry SERC', 'Casualty 2 approach', 'Alpha']);
        });
    }

    public function test_pooled_packs_put_every_event_for_a_pool_before_the_next_pool(): void
    {
        $this->comp->heats_per_event = true;
        $this->comp->save();
        $main = $this->pool('Main Pool', 8);
        $diving = $this->pool('Diving Pool', 4);
        $this->team('Main Team', 1, 4, $main);
        $this->team('Diving Team', 1, 2, $diving);

        $base = new SpeedEvent();
        $base->name = 'Rope Throw';
        $base->save();
        $this->event = new CompetitionSpeedEvent();
        $this->event->event = $base->id;
        $this->event->competition = $this->comp->id;
        $this->event->record = 0;
        $this->event->save();
        $this->team('Main Thrower', 1, 1, $main);
        $this->team('Diving Thrower', 1, 1, $diving);

        $this->actingAs($this->admin)->get(route('comps.printables.chief-timekeeper-pack', $this->comp));
        $this->actingAs($this->admin)->get(route('comps.printables.marshalling', $this->comp));

        Pdf::assertRespondedWithPdf(fn(PdfBuilder $pdf) => $pdf->contains([
            'Swim &amp; Tow · Main Pool · Heat 1', 'Rope Throw · Main Pool · Heat 1',
            'Swim &amp; Tow · Diving Pool · Heat 1', 'Rope Throw · Diving Pool · Heat 1',
        ]) && $pdf->viewName === 'pdfs.heats.chief-timekeeper');
        Pdf::assertRespondedWithPdf(fn(PdfBuilder $pdf) => array_column($pdf->viewData['sections'], 'title') === [
            'Swim & Tow · Main Pool', 'Rope Throw · Main Pool', 'Swim & Tow · Diving Pool', 'Rope Throw · Diving Pool',
        ]);
    }

    public function test_packs_render_with_javascript_off(): void
    {
        // Chrome may run without its sandbox, and Objective descriptions are organiser-entered HTML
        $this->team('Alpha', 1, 1);

        $this->actingAs($this->admin)->get(route('comps.printables.chief-timekeeper-pack', $this->comp));

        Pdf::assertBrowsershot(fn(Browsershot $browsershot) => ($browsershot->createPdfCommand()['options']['disableJavascript'] ?? false) === true);
    }
}
