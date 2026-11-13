<?php

namespace App\Scoring\Loading;

use App\Models\AbstractClasses\Entity;
use App\Models\AbstractClasses\Event;
use App\Models\Club;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\Competitor;
use App\Scoring\Engine\ScoredResult;
use App\Scoring\Input\Entry;
use App\Scoring\Input\EventInput;
use App\Scoring\Results\EntryRef;
use App\Scoring\Results\EventResult;
use App\Scoring\Results\EventResultPart;
use App\Scoring\Results\Violation;
use App\Models\Event\Disqualification;
use App\Models\Event\Penalty;
use Illuminate\Support\Collection;

/**
 * An Event's scoring input, with the models it was read from kept alongside so that scored results
 * can be handed back as the rows pages read (`App\Scoring\Results`), named and with the ids of
 * their stored penalties and disqualifications.
 */
final class LoadedEvent
{
    /** @var array<string, EntryRef> By `Entry::key()` */
    private array $refs = [];

    /**
     * @param array<string, Entity> $entities By `Entry::key()`
     * @param array<int, Collection<int, Penalty>> $penalties Stored penalties, by Entity id
     * @param array<int, Collection<int, Disqualification>> $disqualifications Stored disqualifications, by Entity id
     */
    public function __construct(
        public readonly EventInput $input,
        private readonly Event $event,
        private readonly array $entities,
        private readonly array $penalties,
        private readonly array $disqualifications,
    ) {}

    /**
     * Scored results as the rows pages read (`App\Scoring\Results`): plain data, with each Entry's
     * name as the competition shows it and the ids of its stored penalties and disqualifications
     *
     * @param ScoredResult[] $results
     * @return EventResult[]
     */
    public function results(array $results): array
    {
        return array_map(fn(ScoredResult $result) => new EventResult(
            $result->id,
            $this->entryRef($result->entity),
            $result->result,
            $result->resolvedResult,
            $result->points,
            $result->position,
            $this->violationsOf($result, Violation::PENALTY),
            $this->violationsOf($result, Violation::DISQUALIFICATION),
            $result->combined->map(fn(ScoredResult $part) => new EventResultPart(
                $this->entryRef($part->entity),
                $part->result,
                $this->violationsOf($part, Violation::DISQUALIFICATION),
            ))->values()->all(),
            $result->total_marking_points,
        ), $results);
    }

    /**
     * An Entry as a row of results names it
     */
    public function entryRef(Entry $entry): EntryRef
    {
        return $this->refs[$entry->key()] ??= self::refTo($this->entities[$entry->key()], $this->event->getCompetition, $entry->leagueIds);
    }

    /**
     * @param int[] $leagueIds
     */
    public static function refTo(Entity $entity, Competition $comp, array $leagueIds = []): EntryRef
    {
        return new EntryRef(
            $entity->getMorphClass(),
            $entity->id,
            $entity->getName($comp),
            // The Club it counts for on a Master sheet
            match (true) {
                $entity instanceof Club => $entity->id,
                $entity instanceof CompetitionTeam => $entity->club,
                $entity instanceof Competitor => $entity->getTeam?->club,
                default => null,
            },
            $leagueIds,
            $entity instanceof CompetitionTeam ? $entity->team : null,
        );
    }

    /**
     * A result's penalties or disqualifications: the stored ones with their ids, then each that
     * the formula added automatically
     *
     * @return Violation[]
     */
    private function violationsOf(ScoredResult $result, string $kind): array
    {
        $codes = $kind === Violation::PENALTY ? $result->penalties : $result->disqualifications;
        $stored = ($kind === Violation::PENALTY ? $this->penalties : $this->disqualifications)[$result->entity->id] ?? null;
        $stored = $stored ? $stored->values() : collect();

        $violations = [];

        foreach ($stored as $model) {
            $violations[] = new Violation($kind, $model->code, $model->id);
        }

        foreach ($codes->slice(count($violations)) as $automatic) {
            $violations[] = new Violation($kind, $automatic->code);
        }

        return $violations;
    }
}
