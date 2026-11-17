<?php

namespace App\Scoring\Golden;

use App\Models\AbstractClasses\Event;
use App\Models\Competition;
use App\Models\League;
use App\Models\MasterSchema;
use App\Models\ResultSchema;
use App\Scoring\CompetitionResults;
use App\Scoring\Results\EventResult;
use App\Scoring\Results\EventResultPart;
use App\Scoring\Results\MasterRow;
use App\Scoring\Results\SheetRow;
use App\Scoring\Results\SheetRowEvent;
use App\Scoring\Results\Violation;
use App\Scoring\ScoringRun;

/**
 * Everything the scoring engine works out for a competition, as plain data: each Event's ranked
 * results (for everyone and for each League), each Results sheet and each Master sheet. The golden
 * tests compare this against a stored copy, so a change to the engine that moves any number, place
 * or row order shows up.
 *
 * This is the one place that calls into the engine. When the engine's entry points change, re-point
 * the three `*Rows()` methods and nothing else in the golden tooling has to move.
 */
class CompetitionSnapshot
{
    public static function of(Competition $comp): array
    {
        // Nothing is shared between competitions, so nothing is kept from the last one
        app(ScoringRun::class)->forget();

        $leagues = $comp->getLeagues()->orderBy('id')->get();

        $events = [];
        foreach ($comp->getSpeedEvents()->orderBy('id')->get() as $event) {
            $events["speed:{$event->id}"] = self::event($event, $leagues);
        }
        foreach ($comp->getSERCs()->orderBy('id')->get() as $event) {
            $events["serc:{$event->id}"] = self::event($event, $leagues);
        }

        $sheets = [];
        foreach ($comp->getResultSchemas()->orderBy('id')->get() as $sheet) {
            $sheets["sheet:{$sheet->id}"] = [
                'name' => $sheet->name,
                'results' => self::guarded(fn() => self::sheetRows($sheet)),
            ];
        }

        $masters = [];
        foreach ($comp->getMasterSchemas()->orderBy('id')->get() as $master) {
            $masters["master:{$master->id}"] = [
                'name' => $master->name,
                'results' => self::guarded(fn() => self::masterRows($master)),
            ];
        }

        return [
            'competition' => $comp->name,
            'events' => $events,
            'sheets' => $sheets,
            'masters' => $masters,
        ];
    }

    private static function event(Event $event, iterable $leagues): array
    {
        $results = ['all' => self::guarded(fn() => self::eventRows($event, null))];

        foreach ($leagues as $league) {
            $rows = self::guarded(fn() => self::eventRows($event, $league));

            // Most Leagues hold nobody in a given Event; leave those out
            if ($rows !== []) {
                $results["league:{$league->id}"] = $rows;
            }
        }

        return ['name' => $event->getName(), 'results' => $results];
    }

    private static function eventRows(Event $event, ?League $league): array
    {
        return self::settleTies(array_map(fn(EventResult $r) => [
            'entity' => $r->entry->key(),
            'name' => $r->entry->name,
            'result' => self::number($r->result),
            'resolved' => self::number($r->resolvedResult),
            'points' => self::number($r->points),
            'position' => self::number($r->position),
            'dqs' => array_map(fn(Violation $dq) => (int) $dq->code, $r->disqualifications),
            'pens' => array_map(fn(Violation $pen) => (int) $pen->code, $r->penalties),
            'combined' => array_map(fn(EventResultPart $part) => $part->entry->key(), $r->parts),
        ], app(CompetitionResults::class)->event($event, $league, fresh: true)), 'points');
    }

    private static function sheetRows(ResultSchema $sheet): array
    {
        return self::settleTies(array_map(fn(SheetRow $r) => [
            'entity' => $r->entry->key(),
            'name' => $r->entry->name,
            'total' => self::number($r->totalPoints),
            'position' => self::number($r->position),
            'events' => array_map(fn(SheetRowEvent $e) => [
                'event' => "{$e->eventType}:{$e->eventId}",
                'entity' => $e->entry->key(),
                'position' => self::number($e->position),
                'points' => self::number($e->points),
                'adjusted' => self::number($e->adjustedPoints),
            ], $r->events),
        ], app(CompetitionResults::class)->sheet($sheet, fresh: true)), 'total');
    }

    private static function masterRows(MasterSchema $master): array
    {
        $results = app(CompetitionResults::class)->master($master, fresh: true);

        $rows = self::settleTies(array_map(fn(MasterRow $r) => [
            'entity' => $r->club->key(),
            'name' => $r->club->name,
            'sheets' => array_map(self::number(...), $r->sheetResults),
            'total' => self::number($r->total),
            'position' => self::number($r->position),
        ], $results->rows), 'total');

        return ['rows' => $rows, 'without_club' => $results->withoutClub];
    }

    /**
     * A competition whose scoring throws today has to keep throwing the same way, so the failure is
     * part of the snapshot rather than the end of it
     */
    private static function guarded(callable $rows): array
    {
        try {
            return $rows();
        } catch (\Throwable $e) {
            return ['error' => class_basename($e)];
        }
    }

    /**
     * The engine lists entries that tie exactly (same place, same points) in whatever order the
     * database happened to return them, which isn't the same from one database to the next. So
     * within each run of exact ties the rows are put in entity order; everything else keeps the
     * engine's order.
     */
    private static function settleTies(array $rows, string $pointsKey): array
    {
        $settled = [];
        $run = [];

        foreach ($rows as $row) {
            if ($run && ($row['position'] !== $run[0]['position'] || $row[$pointsKey] !== $run[0][$pointsKey])) {
                array_push($settled, ...self::inEntityOrder($run));
                $run = [];
            }

            $run[] = $row;
        }

        return [...$settled, ...self::inEntityOrder($run)];
    }

    private static function inEntityOrder(array $rows): array
    {
        usort($rows, fn($a, $b) => strnatcmp($a['entity'], $b['entity']));

        return $rows;
    }

    /**
     * Numbers as JSON holds them: whole floats as ints (1000.0 and 1000 are the same result), and
     * the values JSON can't hold as strings
     */
    private static function number(mixed $value): mixed
    {
        if (is_string($value) && is_numeric($value)) {
            $value = $value + 0;
        }

        if (!is_float($value)) {
            return $value;
        }

        if (is_nan($value)) {
            return 'NAN';
        }

        if (is_infinite($value)) {
            return $value > 0 ? 'INF' : '-INF';
        }

        return floor($value) == $value && abs($value) < 1e15 ? (int) $value : $value;
    }
}
