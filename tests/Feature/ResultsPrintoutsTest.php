<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\Event\ScoringSchema;
use App\Models\League;
use App\Models\ResultSchema;
use App\Models\ResultSchemaEvent;
use App\Models\SERC;
use App\Models\SERC\MarkingPointTemplate;
use App\Models\SERCJudge;
use App\Models\SERCMarkingPoint;
use App\Models\SERCResult;
use App\Models\SpeedEvent;
use App\Models\SpeedResult;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;
use Tests\TestCase;

/**
 * Results printouts: PDFs of the rankings for an Event or a Results sheet. The PDF engine is faked,
 * so these check what each printout is given and shows; page layout is checked by eye.
 *
 * Team A (in A League) wins the speed event and team B (in no League) wins the SERC.
 */
class ResultsPrintoutsTest extends TestCase
{
    use RefreshDatabase;

    private User $organiser;

    private Competition $comp;

    private CompetitionSpeedEvent $speed;

    private SERC $serc;

    /** @var CompetitionTeam[] */
    private array $teams = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Pdf::fake();

        $this->organiser = User::factory()->create();

        $this->comp = new Competition();
        $this->comp->name = 'Results Champs';
        $this->comp->when = now()->addWeek();
        $this->comp->where = 'Test Pool';
        $this->comp->max_lanes = 8;
        $this->comp->anytimepin = false;
        $this->comp->seed_per_event = false;
        $this->comp->heats_per_event = false;
        $this->comp->save();
        $this->comp->addAccount($this->organiser, ['owner']);

        $club = Club::create(['competition' => $this->comp->id, 'name' => 'Cardiff']);
        foreach (['A', 'B'] as $name) {
            $this->teams[] = CompetitionTeam::create(['competition' => $this->comp->id, 'club' => $club->id, 'team' => $name]);
        }

        // A League has team A; B League has nobody, so its section is left out
        $this->teams[0]->leagues()->attach($this->league('A League')->id);
        $this->league('B League');

        // Lowest time wins
        $this->speed = $this->speedEvent('Swim & Tow', [62000, 65000]);

        // Highest mark wins
        $this->serc = new SERC();
        $this->serc->name = 'Dry SERC';
        $this->serc->type = 'DRY';
        $this->serc->scorable_entity = 'team';
        $this->serc->competition = $this->comp->id;
        $this->serc->scoring_schema = $this->scoringSchema(true)->id;
        $this->serc->save();

        $objective = new SERCJudge();
        $objective->name = 'Casualty 1';
        $objective->description = '';
        $objective->serc = $this->serc->id;
        $objective->save();

        $mp = new SERCMarkingPoint();
        $mp->name = 'Checks for danger';
        $mp->weight = 1;
        $mp->judge = $objective->id;
        $mp->serc = $this->serc->id;
        $mp->marking_point_template_id = MarkingPointTemplate::create(['name' => 'Default', 'settings' => []])->id;
        $mp->save();

        foreach ([4, 8] as $index => $mark) {
            $result = new SERCResult();
            $result->marking_point = $mp->id;
            $result->entity()->associate($this->teams[$index]);
            $result->result = $mark;
            $result->save();
        }
    }

    private function scoringSchema(bool $rankHigher, array $settings = []): ScoringSchema
    {
        $schema = new ScoringSchema();
        $schema->name = 'Raw';
        $schema->schema = ['equation' => 'item.result', 'global_variables' => [], 'rank_higher' => $rankHigher, ...$settings];
        $schema->save();

        return $schema;
    }

    /**
     * A speed event scoring teams, with a time for each team in order
     */
    private function speedEvent(string $name, array $times): CompetitionSpeedEvent
    {
        $base = new SpeedEvent();
        $base->name = $name;
        $base->save();

        $event = new CompetitionSpeedEvent();
        $event->event = $base->id;
        $event->competition = $this->comp->id;
        $event->scorable_entity = 'team';
        $event->record = 0;
        $event->scoring_schema = $this->scoringSchema(false)->id;
        $event->save();

        foreach ($times as $index => $time) {
            $result = new SpeedResult();
            $result->event = $event->id;
            $result->entity()->associate($this->teams[$index]);
            $result->result = $time;
            $result->save();
        }

        return $event;
    }

    private function league(string $name): League
    {
        $league = new League();
        $league->name = $name;
        $league->competition = $this->comp->id;
        $league->save();

        return $league;
    }

    /**
     * A Results sheet summing positions, lowest wins, over the speed event and the SERC unless given its events
     */
    private function sheet(string $name, bool $viewable = true, ?array $events = null): ResultSchema
    {
        $sheet = new ResultSchema();
        $sheet->name = $name;
        $sheet->competition = $this->comp->id;
        $sheet->viewable = $viewable;
        $sheet->schema = [
            'equation' => 'item.position',
            'global_variables' => [],
            'league' => null,
            'ignore_dq' => false,
            'group_on' => 'team',
            'rank_higher' => false,
            'break_ties' => [],
        ];
        $sheet->save();

        foreach ($events ?? [$this->speed, $this->serc] as $event) {
            $row = new ResultSchemaEvent();
            $row->schema = $sheet->id;
            $row->event_id = $event->id;
            $row->event_type = $event instanceof SERC ? '\App\Models\SERC' : '\App\Models\CompetitionSpeedEvent';
            $row->weight = 1;
            $row->save();
        }

        return $sheet;
    }

    private function url(string $path): string
    {
        return $this->appUrl('/comps/' . $this->comp->id . $path);
    }

    private function sectionTitles(PdfBuilder $pdf): array
    {
        return array_column($pdf->viewData['sections'], 'title');
    }

    public function test_a_speed_events_results_are_a_portrait_pdf_with_overall_then_each_league(): void
    {
        $this->actingAs($this->organiser)->get($this->url('/events/speeds/' . $this->speed->id . '/print-results'))->assertOk();

        Pdf::assertRespondedWithPdf(fn (PdfBuilder $pdf) => $pdf->viewName === 'pdfs.results.speed'
            && $this->sectionTitles($pdf) === ['Overall', 'A League']
            && $pdf->orientation === 'Portrait'
            && $pdf->headerData['title'] === 'Swim & Tow Results'
            && $pdf->downloadName === 'results-champs-swim-tow-results.pdf'
            && $pdf->contains(['01:02.000']));
    }

    public function test_a_disqualified_speed_result_shows_dq_unless_its_scoring_lets_it_rank(): void
    {
        $result = SpeedResult::where('event', $this->speed->id)->whereMorphedTo('entity', $this->teams[0])->first();
        $this->actingAs($this->organiser)->post($this->url('/events/speeds/' . $this->speed->id . '/results'), [
            'rows' => [$result->id => ['result' => '01:02.000', 'disqualification' => 'dq101']],
        ])->assertSessionHasNoErrors();
        $url = $this->url('/events/speeds/' . $this->speed->id . '/print-results');

        $this->actingAs($this->organiser)->get($url);
        Pdf::assertRespondedWithPdf(fn (PdfBuilder $pdf) => $pdf->contains('>DQ</td>'));

        Pdf::fake();
        $this->speed->scoring_schema = $this->scoringSchema(false, ['allow_disqualified_to_rank' => true])->id;
        $this->speed->save();

        $this->actingAs($this->organiser)->get($url);
        Pdf::assertRespondedWithPdf(fn (PdfBuilder $pdf) => !$pdf->contains('>DQ</td>'));
    }

    public function test_a_sercs_results_are_a_portrait_pdf_with_the_raw_mark_points_and_position(): void
    {
        $this->actingAs($this->organiser)->get($this->url('/events/sercs/' . $this->serc->id . '/print-results'))->assertOk();

        Pdf::assertRespondedWithPdf(fn (PdfBuilder $pdf) => $pdf->viewName === 'pdfs.results.serc'
            && $this->sectionTitles($pdf) === ['Overall', 'A League']
            && $pdf->orientation === 'Portrait'
            && $pdf->headerData['title'] === 'Dry SERC Results'
            && $pdf->contains('Raw mark'));
    }

    public function test_a_sheets_standings_are_a_portrait_pdf_of_positions_and_points(): void
    {
        $sheet = $this->sheet('Senior');

        $this->actingAs($this->organiser)->get($this->url('/results/' . $sheet->id . '/print/standings'))->assertOk();

        Pdf::assertRespondedWithPdf(fn (PdfBuilder $pdf) => $pdf->viewName === 'pdfs.results.standings'
            && $pdf->orientation === 'Portrait'
            && $pdf->headerData['title'] === 'Senior Standings'
            && count($pdf->viewData['results']) === 2
            && !$pdf->contains('Swim &amp; Tow'));
    }

    public function test_a_sheets_breakdown_is_a_landscape_pdf_with_every_events_points(): void
    {
        $sheet = $this->sheet('Senior');

        $this->actingAs($this->organiser)->get($this->url('/results/' . $sheet->id . '/print/breakdown'))->assertOk();

        Pdf::assertRespondedWithPdf(fn (PdfBuilder $pdf) => $pdf->viewName === 'pdfs.results.breakdown'
            && $pdf->orientation === 'Landscape'
            && $pdf->headerData['title'] === 'Senior Breakdown'
            && $this->sectionTitles($pdf) === ['Senior']
            && $pdf->contains('Swim &amp; Tow')
            && $pdf->contains('Dry SERC')
            && $pdf->contains('rounded for presentation'));
    }

    public function test_printing_all_sheets_is_one_landscape_pdf_with_every_sheet_even_hidden_ones(): void
    {
        $this->sheet('Senior');
        $this->sheet('Working', viewable: false);

        $this->actingAs($this->organiser)->get($this->url('/results/print-all'))->assertOk();

        Pdf::assertRespondedWithPdf(fn (PdfBuilder $pdf) => $pdf->viewName === 'pdfs.results.breakdown'
            && $pdf->orientation === 'Landscape'
            && $pdf->headerData['title'] === 'All Results sheets'
            && $this->sectionTitles($pdf) === ['Senior', 'Working']);
    }

    public function test_every_printouts_header_says_provisional_only_while_results_are(): void
    {
        $sheet = $this->sheet('Senior');
        $paths = [
            '/events/speeds/' . $this->speed->id . '/print-results',
            '/events/sercs/' . $this->serc->id . '/print-results',
            '/results/' . $sheet->id . '/print/standings',
            '/results/' . $sheet->id . '/print/breakdown',
            '/results/print-all',
        ];

        foreach ([true, false] as $provisional) {
            $this->comp->results_provisional = $provisional;
            $this->comp->save();

            foreach ($paths as $path) {
                Pdf::fake();
                $this->actingAs($this->organiser)->get($this->url($path))->assertOk();
                Pdf::assertRespondedWithPdf(fn (PdfBuilder $pdf) => $pdf->headerData['provisional'] === $provisional
                    && $pdf->contains('Provisional') === $provisional);
            }
        }
    }

    public function test_a_printout_with_nothing_to_rank_says_no_results_yet(): void
    {
        $unscored = $this->speedEvent('Rope Throw', []);
        $empty = $this->sheet('Empty', events: []);

        foreach ([
            '/events/speeds/' . $unscored->id . '/print-results',
            '/results/' . $empty->id . '/print/standings',
            '/results/' . $empty->id . '/print/breakdown',
        ] as $path) {
            Pdf::fake();
            $this->actingAs($this->organiser)->get($this->url($path))->assertOk();
            Pdf::assertRespondedWithPdf(fn (PdfBuilder $pdf) => $pdf->contains('No results yet'));
        }

        // A competition with no Results sheets at all
        $empty->delete();
        Pdf::fake();
        $this->actingAs($this->organiser)->get($this->url('/results/print-all'))->assertOk();
        Pdf::assertRespondedWithPdf(fn (PdfBuilder $pdf) => $pdf->contains('No results yet'));
    }

    public function test_sheet_printouts_need_results_access_to_the_sheets_competition(): void
    {
        $sheet = $this->sheet('Senior');
        $other = User::factory()->create();
        $this->comp->addAccount($other, ['teams']);

        foreach (['standings', 'breakdown'] as $kind) {
            $this->actingAs($other)->get($this->url('/results/' . $sheet->id . '/print/' . $kind))->assertForbidden();
        }
        $this->actingAs($other)->get($this->url('/results/print-all'))->assertForbidden();

        // Another competition's sheet can't be printed through this one
        $theirs = $this->comp->replicate();
        $theirs->save();
        $sheet->competition = $theirs->id;
        $sheet->save();
        $this->actingAs($this->organiser)->get($this->url('/results/' . $sheet->id . '/print/standings'))->assertNotFound();
    }

    public function test_the_old_sheet_print_urls_go_to_the_new_ones(): void
    {
        $sheet = $this->sheet('Senior');
        $old = $this->appUrl('/comp/results/view-schema/' . $sheet->id);

        $this->actingAs($this->organiser)->get($old . '/print-basic')
            ->assertStatus(301)->assertRedirect($this->url('/results/' . $sheet->id . '/print/standings'));
        $this->actingAs($this->organiser)->get($old . '/print')
            ->assertStatus(301)->assertRedirect($this->url('/results/' . $sheet->id . '/print/breakdown'));

        $other = User::factory()->create();
        $this->actingAs($other)->get($old . '/print')->assertForbidden();
    }

    public function test_a_results_sheets_page_links_to_its_standings_and_breakdown(): void
    {
        $sheet = $this->sheet('Senior');

        $this->actingAs($this->organiser)->get($this->url('/results/' . $sheet->id))
            ->assertInertia(fn (Assert $page) => $page
                ->where('printStandingsUrl', route('manage.competitions.results.print.standings', [$this->comp, $sheet]))
                ->where('printBreakdownUrl', route('manage.competitions.results.print.breakdown', [$this->comp, $sheet])));
    }

    public function test_results_printouts_render_with_javascript_off(): void
    {
        $this->actingAs($this->organiser)->get($this->url('/events/speeds/' . $this->speed->id . '/print-results'));

        Pdf::assertBrowsershot(fn (Browsershot $browsershot) => ($browsershot->createPdfCommand()['options']['disableJavascript'] ?? false) === true);
    }
}
