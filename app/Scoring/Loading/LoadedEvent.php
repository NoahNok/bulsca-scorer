<?php

namespace App\Scoring\Loading;

use App\DTO\RankedResult;
use App\DTO\ResolvedResult;
use App\DTO\ScaledResult;
use App\Models\AbstractClasses\Entity;
use App\Models\AbstractClasses\Event;
use App\Models\Club;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\Competitor;
use App\Models\Event\Disqualification;
use App\Models\Event\Penalty;
use App\Scoring\Engine\ScoredResult;
use App\Scoring\Input\Entry;
use App\Scoring\Input\EventInput;
use App\Scoring\Results\EntryRef;
use App\Scoring\Results\EventResult;
use App\Scoring\Results\EventResultPart;
use App\Scoring\Results\Violation;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

/**
 * An Event's scoring input, with the models it was read from kept alongside so that scored results
 * can be handed back as the `RankedResult`s the pages were written for. Those go when the pages
 * read from `App\Scoring` directly (step 5 of docs/scoring-engine.md).
 */
final class LoadedEvent
{
    /** @var array<int, ResolvedResult> The parts of combined results already built, by the scored result's object id */
    private array $parts = [];

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
     * @param ScoredResult[] $results
     * @return RankedResult[]
     */
    public function rankedResults(array $results): array
    {
        return array_map(function (ScoredResult $result) {
            $ranked = new RankedResult(
                $result->id,
                $result->resolvedResult,
                $result->result,
                $this->entities[$result->entity->key()],
                $this->event,
                $result->position,
                $result->points,
                $this->violations($result, $result->disqualifications, $this->disqualifications, Disqualification::class),
                $this->violations($result, $result->penalties, $this->penalties, Penalty::class),
                new EloquentCollection($result->combined->map(fn(ScoredResult $part) => $this->part($part))->all()),
            );

            $ranked->total_marking_points = $result->total_marking_points;

            return $ranked;
        }, $results);
    }

    /**
     * A scored result as a Results sheet's pages expect it, with what it counts for in the sheet.
     * As ever, it doesn't carry the results it was combined from or how many Marks it had.
     */
    public function scaledResult(ScoredResult $result, float $adjustedPoints): ScaledResult
    {
        return ScaledResult::fromRanked($this->rankedResults([$result])[0], $adjustedPoints);
    }

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

    /**
     * The model an Entry was read from
     */
    public function entity(Entry $entry): Entity
    {
        return $this->entities[$entry->key()];
    }

    /**
     * A result's penalties or disqualifications as models: the stored ones, then one made for each
     * that the formula added automatically
     *
     * @param class-string<Penalty|Disqualification> $class
     */
    private function violations(ScoredResult $result, Collection $codes, array $stored, string $class): EloquentCollection
    {
        $models = isset($stored[$result->entity->id]) ? $stored[$result->entity->id]->all() : [];

        foreach ($codes->slice(count($models)) as $automatic) {
            $model = new $class();
            $model->code = $automatic->code;
            $models[] = $model;
        }

        return new EloquentCollection($models);
    }

    /**
     * One of the results a combined result was made from. The first is the combined result itself.
     */
    private function part(ScoredResult $result): ResolvedResult
    {
        return $this->parts[spl_object_id($result)] ??= $this->resolved($result);
    }

    private function resolved(ScoredResult $result): ResolvedResult
    {
        $resolved = new ResolvedResult(
            $result->id,
            $result->resolvedResult,
            $result->result,
            $this->entities[$result->entity->key()],
            $this->event,
            $this->violations($result, $result->disqualifications, $this->disqualifications, Disqualification::class),
            $this->violations($result, $result->penalties, $this->penalties, Penalty::class),
            null,
            $result->points,
        );

        $resolved->total_marking_points = $result->total_marking_points;

        return $resolved;
    }
}
