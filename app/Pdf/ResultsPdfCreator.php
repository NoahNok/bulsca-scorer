<?php

namespace App\Pdf;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\ResultSchema;
use App\Models\SERC;
use Spatie\LaravelPdf\Enums\Orientation;
use Spatie\LaravelPdf\PdfBuilder;

/**
 * Builds each Results printout: a PDF of the rankings for an Event or a Results sheet, through
 * CompetitionPdf. Unlike Packs they have no cover, and the header says PROVISIONAL while results are.
 */
class ResultsPdfCreator
{
    private Competition $comp;

    public function __construct(Competition $comp)
    {
        $this->comp = $comp;
    }

    private function printout(string $view, string $title, array $data, Orientation $orientation = Orientation::Portrait): PdfBuilder
    {
        return CompetitionPdf::make($this->comp, $view, $title, $data, $orientation, (bool) $this->comp->areResultsProvisional());
    }

    /**
     * Overall, then each League by name, leaving out any with no results
     */
    private function leagueSections(CompetitionSpeedEvent|SERC $event): array
    {
        $sections = [['title' => 'Overall', 'results' => $event->getRankedResults()]];

        foreach ($this->comp->getLeagues->sortBy('name') as $league) {
            $sections[] = ['title' => $league->name, 'results' => $event->getRankedResults($league)];
        }

        return array_values(array_filter($sections, fn ($section) => count($section['results']) > 0));
    }

    public function speedEvent(CompetitionSpeedEvent $event): PdfBuilder
    {
        return $this->printout('pdfs.results.speed', "{$event->getName()} Results", [
            'event' => $event,
            'sections' => $this->leagueSections($event),
            // Disqualified results show their points only when the scoring lets them rank
            'showDqPoints' => (bool) ($event->scoringSchema?->schema['allow_disqualified_to_rank'] ?? false),
        ]);
    }

    public function serc(SERC $serc): PdfBuilder
    {
        return $this->printout('pdfs.results.serc', "{$serc->getName()} Results", [
            'serc' => $serc,
            'sections' => $this->leagueSections($serc),
        ]);
    }

    /**
     * A Results sheet's final positions and points
     */
    public function standings(ResultSchema $sheet): PdfBuilder
    {
        return $this->printout('pdfs.results.standings', "{$sheet->name} Standings", [
            'results' => $sheet->getResults() ?? [],
        ]);
    }

    /**
     * A Results sheet with every Event's points and position beside the totals
     */
    public function breakdown(ResultSchema $sheet): PdfBuilder
    {
        return $this->printout('pdfs.results.breakdown', "{$sheet->name} Breakdown", [
            'sections' => $this->breakdownSections([$sheet]),
        ], Orientation::Landscape);
    }

    /**
     * Every Results sheet's Breakdown in one PDF, including sheets hidden from results
     */
    public function allSheets(): PdfBuilder
    {
        return $this->printout('pdfs.results.breakdown', 'All Results sheets', [
            'sections' => $this->breakdownSections($this->comp->getResultSchemas()->orderBy('id')->get()),
        ], Orientation::Landscape);
    }

    /**
     * A section per sheet, leaving out sheets with nothing ranked yet
     */
    private function breakdownSections(iterable $sheets): array
    {
        $sections = [];

        foreach ($sheets as $sheet) {
            $results = $sheet->getResults() ?? [];

            if (count($results) > 0) {
                $sections[] = ['title' => $sheet->name, 'results' => $results];
            }
        }

        return $sections;
    }
}
