<?php

namespace App\Pdf;

use App\Models\Competition;
use App\Models\Pool;
use App\Models\SERC;
use Illuminate\Support\Str;
use Spatie\LaravelPdf\Enums\Format;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;

/**
 * Builds each Pack as a PDF. See docs/adr/0002-browsershot-for-pdf-packs.md.
 */
class CompetitionPdfCreator
{
    private Competition $comp;

    public function __construct(Competition $comp)
    {
        $this->comp = $comp;
    }

    /**
     * Every speed event's heats. Without heats per event, every event runs the first event's heats.
     */
    private function heatsForEachEvent(): array
    {
        $heats = $this->comp->getHeats();

        if ($this->comp->heats_per_event || empty($heats)) {
            return $heats;
        }

        return $this->comp->getSpeedEvents->map(fn($event) => [...$heats[0], 'event' => $event])->all();
    }

    /**
     * One section per event and Pool, each starting on a new page. Heat numbers restart per Pool.
     */
    private function heatSections(): array
    {
        $sections = [];

        foreach ($this->heatsForEachEvent() as $heatevent) {
            foreach ($heatevent['pools'] as $poolHeats) {
                $sections[] = ['event' => $heatevent['event'], 'pool' => $poolHeats['pool'], 'heats' => $poolHeats['heats']];
            }
        }

        return $sections;
    }

    private function pack(string $view, string $packName, array $data): PdfBuilder
    {
        $shared = ['comp' => $this->comp, 'packName' => $packName];

        return Pdf::view($view, [...$data, ...$shared])
            ->headerView('pdfs.partials.header', $shared)
            ->footerView('pdfs.partials.footer')
            ->format(Format::A4)
            ->margins(20, 10, 15, 10)
            ->inline(Str::slug("{$this->comp->name} {$packName}") . '.pdf');
    }

    public function chiefTimekeeper(bool $onePerPage = true): PdfBuilder
    {
        return $this->pack('pdfs.heats.chief-timekeeper', 'Chief Timekeeper Pack', [
            'sections' => $this->heatSections(),
            'onePerPage' => $onePerPage,
        ]);
    }

    public function sercMarking(): PdfBuilder
    {
        // Draws are shared by every SERC, so the first SERC's draws are used for all of them
        return $this->pack('pdfs.sercs.serc-marking', 'SERC Marking Pack', [
            'sercs' => $this->comp->getSERCs,
            'tanks' => $this->comp->getDraws()[0]['draws'] ?? collect(),
        ]);
    }

    /**
     * Blank tables for judges to rough-mark every team against one SERC's Objectives.
     */
    public function rawMarkingSheets(SERC $serc): PdfBuilder
    {
        return $this->pack('pdfs.sercs.raw-marking', "{$serc->getName()} Raw Marking Sheets", [
            'serc' => $serc,
            'teams' => $this->comp->getCompetitionTeams,
        ]);
    }

    /**
     * Names grouped and ordered by heat (speeds) or draw (SERCs), for the marshals calling teams up.
     */
    public function marshalling(string $type): PdfBuilder
    {
        $isSerc = $type === 'serc';

        return $this->pack('pdfs.marshalling', $isSerc ? 'SERC Marshalling Pack' : 'Speed Marshalling Pack', [
            'sections' => $isSerc ? $this->sercMarshallingSections() : $this->speedMarshallingSections(),
        ]);
    }

    private function speedMarshallingSections(): array
    {
        $sections = [];

        foreach ($this->comp->getHeats() as $heatevent) {
            $eventName = $this->comp->heats_per_event ? $heatevent['event']->getName() : 'Speeds';

            foreach ($heatevent['pools'] as $poolHeats) {
                $pool = $poolHeats['pool'];
                $blocks = [];

                foreach ($poolHeats['heats'] as $heatNo => $lanes) {
                    $blocks[] = [
                        'name' => $eventName . ' · ' . Pool::heatLabel($pool, $heatNo),
                        'leagues' => $this->leagues($lanes),
                        'lines' => $lanes->map(fn($lane) => "Lane {$lane->lane}: {$lane->entity?->getName($this->comp)}")->all(),
                    ];
                }

                $sections[] = ['title' => $pool ? "$eventName · {$pool->name}" : $eventName, 'blocks' => $blocks];
            }

            // Without heats per event, every event runs the same heats
            if (!$this->comp->heats_per_event) {
                break;
            }
        }

        return $sections;
    }

    private function sercMarshallingSections(): array
    {
        $useTanks = $this->comp->getScoringSettings?->use_tanks ?? false;
        $sections = [];

        foreach ($this->comp->getDraws() as $drawevent) {
            $tanks = $drawevent['draws'];
            $blocks = [];

            if ($useTanks) {
                // Tanks run side by side, so marshal draw by draw across every tank
                $maxDraw = $tanks->map(fn($tank) => $tank->max('draw'))->max();

                for ($drawNo = 1; $drawNo <= $maxDraw; $drawNo++) {
                    $blocks[] = [
                        'name' => "Draw {$drawNo}",
                        'leagues' => '',
                        'lines' => $tanks->map(function ($tank, $index) use ($drawNo) {
                            $tankNo = $index + 1;

                            return "Tank {$tankNo}: " . ($tank->firstWhere('draw', $drawNo)?->entity?->getName($this->comp) ?? '-');
                        })->all(),
                    ];
                }
            } else {
                foreach ($tanks as $index => $draws) {
                    $tankNo = $index + 1;
                    $blocks[] = [
                        'name' => "Tank {$tankNo}",
                        'leagues' => $this->leagues($draws),
                        'lines' => $draws->map(fn($draw) => "{$draw->draw}: {$draw->entity?->getName($this->comp)}")->all(),
                    ];
                }
            }

            $sections[] = ['title' => $drawevent['serc']->getName(), 'blocks' => $blocks];
        }

        return $sections;
    }

    private function leagues($rows): string
    {
        return $rows->map(fn($row) => $row->entity?->getLeague()?->name)->filter()->unique()->implode(', ');
    }
}
