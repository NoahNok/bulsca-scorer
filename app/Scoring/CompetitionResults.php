<?php

namespace App\Scoring;

use App\Models\AbstractClasses\Event;
use App\Models\Competition;
use App\Models\League;
use App\Models\MasterSchema;
use App\Models\ResultSchema;
use App\Scoring\Engine\MasterScorer;
use App\Scoring\Engine\MasterStanding;
use App\Scoring\Engine\SheetScorer;
use App\Scoring\Engine\SheetStanding;
use App\Scoring\Input\SheetDefinition;
use App\Scoring\Input\SheetEvent;
use App\Scoring\Loading\LoadedEvent;
use App\Scoring\Results\EventResult;
use App\Scoring\Results\MasterResults;
use App\Scoring\Results\MasterRow;
use App\Scoring\Results\SheetRow;
use App\Scoring\Results\SheetRowEvent;
use Illuminate\Support\Facades\Cache;

/**
 * What pages read results from: an Event's results, a Results sheet's rows and a Master sheet's
 * rows, as plain data (`App\Scoring\Results`) with names already on them.
 *
 * Rows are cached with the competition's results version (`ResultsVersion`) and used only while
 * that hasn't moved, so a cached row is right or not used, never stale, and nothing has to clear
 * them. Where results aren't versioned (What-If) nothing is cached. Within a request each Event is
 * loaded once and scored once per League, however many sheets and pages ask (`ScoringRun`).
 *
 * There is one of these per request (it is bound `scoped`). It isn't final so that a page's test
 * can stand one in that fails to score.
 */
class CompetitionResults
{
    /** @var array<string, array{version: int, rows: array}> Rows already read or worked out in this request */
    private array $read = [];

    public function __construct(
        private readonly ScoringRun $run,
        private readonly ResultsVersion $versions,
    ) {}

    /**
     * The Event's results, best placed first: everyone's, or a League's Entries scored against each
     * other. An Event without a formula has none. `$fresh` works them out whatever is cached.
     *
     * @return EventResult[]
     */
    public function event(Event $event, ?League $league = null, bool $fresh = false): array
    {
        // One cache entry per Event, holding each League asked for, so it can go with the Event
        return $this->versioned(
            $event->getCompetition,
            self::eventKey($event),
            (string) ($league?->id ?? 'all'),
            function () use ($event, $league) {
                $loaded = $this->run->loaded($event);

                return $loaded ? $loaded->results($this->run->results($event, $league?->id)) : [];
            },
            $fresh,
        );
    }

    /**
     * The Results sheet's rows, best placed first. `$fresh` works them out whatever is cached.
     *
     * @return SheetRow[]
     */
    public function sheet(ResultSchema $sheet, bool $fresh = false): array
    {
        return $this->versioned($sheet->getCompetition, self::sheetKey($sheet), 'rows', fn() => $this->scoreSheet($sheet), $fresh);
    }

    /**
     * The Master sheet's rows, best placed first, with who its Results sheets left without a Club.
     * Worked out each time from its sheets' rows, which are cached; `$fresh` works those out too.
     */
    public function master(MasterSchema $master, bool $fresh = false): MasterResults
    {
        $comp = $master->getCompetition;
        $schema = $master->schema;

        $sumOver = $schema['sum_over'] ?? 'position';
        $clubs = $comp->getClubs->whereNotIn('id', $schema['exclude'] ?? [])->keyBy('id');

        $sheetRows = [];
        $withoutClub = [];

        foreach ($master->getSheets() as $sheet) {
            $rows = $this->sheet($sheet, $fresh);

            // An Entry without a Club counts for none
            $sheetRows[$sheet->id] = array_map(fn(SheetRow $row) => [$row->entry->clubId, $row], $rows);

            $names = array_values(array_unique(array_map(
                fn(SheetRow $row) => $row->entry->name,
                array_filter($rows, fn(SheetRow $row) => $row->entry->clubId === null),
            )));

            if ($names) {
                $withoutClub[$sheet->name] = $names;
            }
        }

        $standings = MasterScorer::score($clubs->keys()->all(), $sheetRows, $schema['default_value'], $sumOver);

        return new MasterResults(
            array_map(fn(MasterStanding $standing) => new MasterRow(
                LoadedEvent::refTo($clubs[$standing->clubId], $comp),
                $standing->sheetResults,
                $standing->total,
                $standing->position,
            ), $standings),
            $withoutClub,
        );
    }

    /**
     * Drop what is cached for an Event or a Results sheet that has been deleted: with no version
     * to outlive, it would otherwise stay
     */
    public static function forget(Event|ResultSchema $deleted): void
    {
        Cache::forget($deleted instanceof Event ? self::eventKey($deleted) : self::sheetKey($deleted));
    }

    /**
     * Each of the sheet's Events scored for the sheet's League, and the standings worked out from
     * those by `SheetScorer`
     *
     * @return SheetRow[]
     */
    private function scoreSheet(ResultSchema $sheet): array
    {
        // A League that no longer exists is no League: the sheet is of everyone
        $league = array_key_exists('league', $sheet->schema) ? League::find($sheet->schema['league']) : null;

        $events = [];
        $eventResults = [];
        $from = [];

        foreach ($sheet->getEvents as $sheetEvent) {
            $event = $sheetEvent->getActualEvent;

            $events[] = $definition = new SheetEvent($event->getType(), $event->id, (float) $sheetEvent->weight);
            $eventResults[] = $this->run->results($event, $league?->id);
            $from[spl_object_id($definition)] = [$this->run->loaded($event), $this->run->name($event)];
        }

        $standings = SheetScorer::score(SheetDefinition::fromSchema($sheet->schema, $events), $eventResults);

        return array_map(function (SheetStanding $standing) use ($from) {
            $entry = null;
            $rowEvents = [];

            foreach ($standing->events as $result) {
                [$loaded, $name] = $from[spl_object_id($result->event)];

                $rowEvents[] = new SheetRowEvent(
                    $result->event->type,
                    $result->event->id,
                    $name,
                    $loaded->entryRef($result->entity),
                    $result->position,
                    $result->points,
                    $result->adjustedPoints,
                );

                if ($result->entity === $standing->entity) {
                    $entry ??= $loaded->entryRef($standing->entity);
                }
            }

            return new SheetRow($entry, $rowEvents, $standing->totalPoints, $standing->position);
        }, $standings);
    }

    /**
     * One part of a cache entry, worked out and stored when it isn't there at the competition's
     * version. An entry holds the version it was worked out at and is replaced whole when the
     * version has moved, so old versions don't pile up.
     */
    private function versioned(Competition $comp, string $key, string $part, callable $score, bool $fresh = false): array
    {
        $version = $this->versions->of($comp);

        // Not versioned (What-If): nothing to say a cached row is still right
        if ($version === null) {
            return $score();
        }

        $entry = $this->read[$key] ?? Cache::get($key);

        if (!is_array($entry) || ($entry['version'] ?? null) !== $version) {
            $entry = ['version' => $version, 'rows' => []];
        }

        if ($fresh || !array_key_exists($part, $entry['rows'])) {
            $entry['rows'][$part] = $score();

            // Scoring may itself have moved the version on (it doesn't today): then these rows are
            // of the version before, which is what the entry says
            Cache::forever($key, $entry);
        }

        $this->read[$key] = $entry;

        return $entry['rows'][$part];
    }

    private static function eventKey(Event $event): string
    {
        return "competition.{$event->competition}.results.{$event->getType()}.{$event->id}";
    }

    private static function sheetKey(ResultSchema $sheet): string
    {
        return "competition.{$sheet->competition}.results.sheet.{$sheet->id}";
    }
}
