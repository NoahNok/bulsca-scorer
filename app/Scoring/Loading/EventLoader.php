<?php

namespace App\Scoring\Loading;

use App\Models\AbstractClasses\Entity;
use App\Models\AbstractClasses\Event;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\Competitor;
use App\Models\SERC;
use App\Scoring\Input\Entry;
use App\Scoring\Input\EventInput;
use App\Scoring\Input\EventRow;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

/**
 * Reads everything needed to score an Event into plain inputs, in a fixed handful of queries
 * however many Entries it has: results, the Entries with their Leagues and Club or team,
 * penalties, disqualifications and seeds. Leagues are not queried for: `EventScorer` leaves out
 * the rows of Entries that aren't in the League it is asked for.
 *
 * Rows are read in a set order (see `speed()` and `serc()`), because the order decides how exact
 * ties are listed and which of them a Results sheet's tie-breaks place first (Q7).
 */
final class EventLoader
{
    public static function load(Event $event): LoadedEvent
    {
        $penalties = $event->penalties()->orderBy('id')->get()->groupBy('entity_id');
        $disqualifications = $event->disqualifications()->orderBy('id')->get()->groupBy('entity_id');

        [$rows, $entities] = $event instanceof SERC
            ? self::serc($event, $penalties, $disqualifications)
            : self::speed($event, $penalties, $disqualifications);

        return new LoadedEvent(
            new EventInput($event instanceof SERC ? EventInput::SERC : EventInput::SPEED, $event->id, $event->scoringSchema->formula(), $rows),
            $event,
            $entities,
            $penalties->all(),
            $disqualifications->all(),
        );
    }

    /**
     * A speed event's times, in the order they were first entered (id order)
     *
     * @return array{EventRow[], array<string, Entity>}
     */
    private static function speed(CompetitionSpeedEvent $event, Collection $penalties, Collection $disqualifications): array
    {
        $results = $event->getConnection()->table('speed_results')
            ->where('event', $event->id)
            ->whereNotNull('result')
            ->orderBy('id')
            ->get(['id', 'result', 'entity_type', 'entity_id']);

        $models = self::entities($event, $results);

        // Q5: always the competition's first speed event's seeds, even with seeds per event
        // Matched on the Entity's id alone, like penalties. An Entity has one seed per event; were
        // there more, the first (in id order) is the one used. One without a time counts as 0.
        $seeds = [];
        foreach ($event->getCompetition->getSpeedEvents->first()->seeds()->orderBy('id')->get(['id', 'entity_id', 'seed']) as $seed) {
            $seeds[$seed->entity_id] ??= $seed->seed ?? 0;
        }

        $rows = [];
        $entities = [];

        foreach ($results as $result) {
            $entity = $models[self::classOf($result->entity_type)][$result->entity_id] ?? null;
            $entry = self::entry($entity);
            $entities[$entry->key()] = $entity;

            $rows[] = new EventRow(
                $result->id,
                $entry,
                $result->result,
                self::codes($penalties, $entity->id),
                self::codes($disqualifications, $entity->id),
                // Q6: without a seed of its own it is scored as if its seed were 0
                $seeds[$entity->id] ?? 0,
            );
        }

        return [$rows, $entities];
    }

    /**
     * A SERC's Marks, summed by marking point weight for each Entry. Marks are read marking point
     * by marking point and, within one, in the order they were entered: an Entry's Marks are added
     * up in that order and Entries are listed in the order they first appear.
     *
     * @return array{EventRow[], array<string, Entity>}
     */
    private static function serc(SERC $event, Collection $penalties, Collection $disqualifications): array
    {
        $marks = $event->getConnection()->table('serc_results')
            ->join('serc_marking_points', 'serc_marking_points.id', '=', 'serc_results.marking_point')
            ->where('serc_marking_points.serc', $event->id)
            ->orderBy('serc_marking_points.id')
            ->orderBy('serc_results.id')
            ->get(['serc_results.id', 'serc_results.result', 'serc_results.entity_type', 'serc_results.entity_id', 'serc_marking_points.weight']);

        $models = self::entities($event, $marks);

        $totals = [];
        foreach ($marks as $mark) {
            $entity = $models[self::classOf($mark->entity_type)][$mark->entity_id] ?? null;

            $totals[$entity->id] ??= ['id' => $mark->id, 'entity' => $entity, 'total' => 0, 'marks' => 0];
            $totals[$entity->id]['total'] += $mark->result * $mark->weight;
            $totals[$entity->id]['marks']++;
        }

        $rows = [];
        $entities = [];

        foreach ($totals as $total) {
            $entity = $total['entity'];
            $entry = self::entry($entity);
            $entities[$entry->key()] = $entity;

            $rows[] = new EventRow(
                $total['id'],
                $entry,
                $total['total'],
                self::codes($penalties, $entity->id),
                self::codes($disqualifications, $entity->id),
                markingPoints: $total['marks'],
            );
        }

        return [$rows, $entities];
    }

    /**
     * The Entities these rows belong to, with what an Entry needs loaded alongside: each model
     * class's own, keyed by id
     *
     * @return array<class-string, Collection<int, Entity>>
     */
    private static function entities(Event $event, Collection $rows): array
    {
        $models = [];
        $comp = $event->getCompetition;

        foreach ($rows->groupBy(fn($row) => self::classOf($row->entity_type)) as $class => $ofClass) {
            // With what an Entry needs, and what its name does (`Entity::getFormattedName()`), so
            // that naming the rows takes no queries per Entry
            $with = match ($class) {
                CompetitionTeam::class => [
                    'leagues',
                    'getClub',
                    ...(str_contains((string) $comp->team_format, ':S') ? ['getCompetitors'] : []),
                ],
                Competitor::class => [
                    'leagues',
                    'getTeam',
                    ...(str_contains((string) $comp->competitor_format, ':C') ? ['getTeam.getClub'] : []),
                ],
                default => ['leagues'],
            };

            $models[$class] = $class::on($event->getConnectionName())
                ->with($with)
                ->whereIn('id', $ofClass->pluck('entity_id')->unique()->values())
                ->get()
                ->keyBy('id');
        }

        return $models;
    }

    private static function entry(Entity $entity): Entry
    {
        [$clubId, $teamId] = match (true) {
            $entity instanceof CompetitionTeam => [$entity->getClub?->id ?? -1, $entity->id],
            $entity instanceof Competitor => [$entity->getTeam?->club, $entity->getTeam?->id],
            default => [$entity->id, null],
        };

        return new Entry(
            $entity->id,
            $entity->getMorphClass(),
            $clubId,
            $teamId,
            $entity->leagues->pluck('id')->sort()->values()->all(),
            $entity->getAttributes(),
        );
    }

    /**
     * An Entity's penalty or disqualification codes. They are matched on the Entity's id alone, as
     * an Event's Entries are all of one type.
     */
    private static function codes(Collection $violations, int $entityId): array
    {
        return ($violations[$entityId] ?? collect())->pluck('code')->all();
    }

    private static function classOf(string $morphType): string
    {
        return Relation::getMorphedModel($morphType) ?? $morphType;
    }
}
