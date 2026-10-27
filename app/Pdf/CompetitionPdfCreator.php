<?php

namespace App\Pdf;

use App\Models\Competition;
use App\Models\Pool;
use App\Models\SERC;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
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
     * One section per event and Pool, each starting on a new page. Heat numbers restart per Pool.
     * With Pools, every event in a Pool comes before the next Pool, so each Pool's officials get
     * their own stack. Without heats per event, every event runs the first event's heats, so
     * $everyEvent false keeps just that one set.
     */
    private function heatSections(bool $everyEvent = true): array
    {
        $heats = $this->comp->getHeats();

        if (!$this->comp->heats_per_event && !empty($heats) && $everyEvent) {
            $heats = $this->comp->getSpeedEvents->map(fn($event) => [...$heats[0], 'event' => $event])->all();
        } elseif (!$this->comp->heats_per_event) {
            $heats = array_slice($heats, 0, 1);
        }

        $sections = [];

        foreach ($heats as $eventHeats) {
            foreach ($eventHeats['pools'] as $poolHeats) {
                $sections[] = ['event' => $eventHeats['event'], 'pool' => $poolHeats['pool'], 'heats' => $poolHeats['heats']];
            }
        }

        // usort is stable, so events keep their order within each Pool
        usort($sections, fn($a, $b) => ($a['pool']?->id ?? 0) <=> ($b['pool']?->id ?? 0));

        return $sections;
    }

    /**
     * The short code printed at the foot of a sheet, e.g. "Swim & Tow" -> "ST".
     */
    public static function initials(string $name): string
    {
        preg_match_all('/\b\w/', $name, $matches);

        return implode('', $matches[0]);
    }

    private function pack(string $view, string $packName, array $data): PdfBuilder
    {
        $shared = ['comp' => $this->comp, 'packName' => $packName];

        return Pdf::view($view, [...$data, ...$shared])
            ->headerView('pdfs.partials.header', $shared)
            ->footerView('pdfs.partials.footer')
            ->format(Format::A4)
            ->margins(20, 10, 15, 10)
            // Packs need no JavaScript, and Chrome may run without its sandbox (LARAVEL_PDF_NO_SANDBOX),
            // so don't run any script that organiser-entered HTML might carry
            ->withBrowsershot(fn(Browsershot $browsershot) => $browsershot->disableJavascript())
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
     * Blank tables for judges to rough-mark every team against each of one SERC's Objectives.
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
        return array_map(function ($section) {
            $pool = $section['pool'];
            $eventName = $this->comp->heats_per_event ? $section['event']->getName() : 'Speeds';
            $blocks = [];

            foreach ($section['heats'] as $heatNo => $lanes) {
                $blocks[] = [
                    'name' => $eventName . ' · ' . Pool::heatLabel($pool, $heatNo),
                    'leagues' => $this->leagueNames($lanes),
                    'lines' => $lanes->map(fn($lane) => "Lane {$lane->lane}: {$lane->entity?->getName($this->comp)}")->all(),
                ];
            }

            return ['title' => $pool ? "$eventName · {$pool->name}" : $eventName, 'blocks' => $blocks];
        }, $this->heatSections(everyEvent: false));
    }

    private function sercMarshallingSections(): array
    {
        $useTanks = $this->comp->getScoringSettings?->use_tanks ?? false;
        $sections = [];

        foreach ($this->comp->getDraws() as $sercDraws) {
            $tanks = $sercDraws['draws'];
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
                        'leagues' => $this->leagueNames($draws),
                        'lines' => $draws->map(fn($draw) => "{$draw->draw}: {$draw->entity?->getName($this->comp)}")->all(),
                    ];
                }
            }

            $sections[] = ['title' => $sercDraws['serc']->getName(), 'blocks' => $blocks];
        }

        return $sections;
    }

    private function leagueNames($rows): string
    {
        return $rows->map(fn($row) => $row->entity?->getLeague()?->name)->filter()->unique()->implode(', ');
    }
}
