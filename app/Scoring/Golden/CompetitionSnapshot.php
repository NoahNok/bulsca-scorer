<?php

namespace App\Scoring\Golden;

use App\DTO\RankedResult;
use App\Models\AbstractClasses\Entity;
use App\Models\AbstractClasses\Event;
use App\Models\Competition;
use App\Models\League;
use App\Models\MasterSchema;
use App\Models\ResultSchema;
use App\Models\SERC;
use App\Scoring\ScoringRun;
use Illuminate\Database\Eloquent\Relations\Relation;

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
                'results' => self::guarded(fn() => self::sheetRows($sheet, $comp)),
            ];
        }

        $masters = [];
        foreach ($comp->getMasterSchemas()->orderBy('id')->get() as $master) {
            $masters["master:{$master->id}"] = [
                'name' => $master->name,
                'results' => self::guarded(fn() => self::masterRows($master, $comp)),
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
        $comp = $event->getCompetition;

        return self::settleTies(array_map(fn(RankedResult $r) => [
            'entity' => self::entityKey($r->entity),
            'name' => $r->entity->getName($comp),
            'result' => self::number($r->result),
            'resolved' => self::number($r->resolvedResult),
            'points' => self::number($r->points),
            'position' => self::number($r->position),
            'dqs' => $r->disqualifications->map(fn($dq) => (int) $dq->code)->values()->all(),
            'pens' => $r->penalties->map(fn($pen) => (int) $pen->code)->values()->all(),
            'combined' => $r->combined->map(fn($c) => self::entityKey($c->entity))->values()->all(),
        ], array_values($event->getRankedResults($league))), 'points');
    }

    private static function sheetRows(ResultSchema $sheet, Competition $comp): array
    {
        return self::settleTies($sheet->getResults(true)->map(fn($r) => [
            'entity' => self::entityKey($r->entity),
            'name' => $r->entity->getName($comp),
            'total' => self::number($r->totalPoints),
            'position' => self::number($r->position),
            'events' => $r->events->map(fn($e) => [
                'event' => ($e->event instanceof SERC ? 'serc' : 'speed') . ":{$e->event->id}",
                'entity' => self::entityKey($e->entity),
                'position' => self::number($e->position),
                'points' => self::number($e->points),
                'adjusted' => self::number($e->adjustedPoints),
            ])->values()->all(),
        ])->values()->all(), 'total');
    }

    private static function masterRows(MasterSchema $master, Competition $comp): array
    {
        $rows = self::settleTies($master->getResults()->map(fn($r) => [
            'entity' => self::entityKey($r->entity),
            'name' => $r->entity->getName($comp),
            'sheets' => array_map(self::number(...), $r->sheetResults),
            'total' => self::number($r->total),
            'position' => self::number($r->position),
        ])->values()->all(), 'total');

        return ['rows' => $rows, 'without_club' => $master->withoutClub];
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

    private static function entityKey(Entity $entity): string
    {
        $type = array_search($entity::class, Relation::morphMap(), true) ?: class_basename($entity);

        return "{$type}:{$entity->id}";
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
