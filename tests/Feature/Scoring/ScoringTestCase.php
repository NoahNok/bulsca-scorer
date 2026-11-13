<?php

namespace Tests\Feature\Scoring;

use App\Models\AbstractClasses\Entity;
use App\Models\AbstractClasses\Event;
use App\Models\Club;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\Competitor;
use App\Models\Event\ScoringSchema;
use App\Models\League;
use App\Models\MasterSchema;
use App\Models\Orders\EntityEventSeed;
use App\Models\ResultSchema;
use App\Models\ResultSchemaEvent;
use App\Models\SERC;
use App\Models\SERCJudge;
use App\Models\SERCMarkingPoint;
use App\Models\SERCResult;
use App\Models\SpeedEvent;
use App\Models\SpeedResult;
use App\Scoring\CompetitionResults;
use App\Scoring\Results\EntryRef;
use App\Scoring\Results\EventResult;
use App\Scoring\Results\SheetRow;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Small made-up competitions for pinning down what the scoring engine does, one rule at a time, with
 * results worked out by hand. The golden tests (tests/Golden) cover real competitions; these say why.
 *
 * Everything goes through the engine's public entry points (an Event's ranked results, a sheet's
 * results, a Master sheet's results), so the tests hold while the engine behind them is rebuilt.
 */
abstract class ScoringTestCase extends TestCase
{
    use RefreshDatabase;

    /** Points for a time against a 60s record: 60s is 1000, and every 6s slower is 100 fewer */
    protected const TIME_POINTS = [
        'equation' => '(1-(item.resolvedResult-record)/record)*1000',
        'global_variables' => [['name' => 'record', 'order' => null, 'expression' => '60000']],
    ];

    /** The result as it is, most wins */
    protected const RAW = ['equation' => 'item.resolvedResult', 'global_variables' => []];

    protected Competition $comp;

    protected Club $club;

    protected function setUp(): void
    {
        parent::setUp();

        $this->comp = new Competition();
        $this->comp->name = 'Scoring Champs';
        $this->comp->when = now();
        $this->comp->where = 'Test Pool';
        $this->comp->max_lanes = 8;
        $this->comp->anytimepin = false;
        $this->comp->seed_per_event = false;
        $this->comp->heats_per_event = false;
        $this->comp->show_competition = true;
        $this->comp->save();

        $this->club = $this->club('Cardiff');
    }

    protected function club(string $name): Club
    {
        return Club::create(['competition' => $this->comp->id, 'name' => $name]);
    }

    protected function team(string $letter, ?Club $club = null, ?League $league = null): CompetitionTeam
    {
        $team = CompetitionTeam::create(['competition' => $this->comp->id, 'club' => ($club ?? $this->club)->id, 'team' => $letter]);

        if ($league) {
            $team->leagues()->attach($league);
        }

        return $team;
    }

    /**
     * @return CompetitionTeam[] keyed by letter
     */
    protected function teams(string ...$letters): array
    {
        return array_combine($letters, array_map(fn($letter) => $this->team($letter), $letters));
    }

    protected function competitor(string $name, CompetitionTeam $team, ?League $league = null): Competitor
    {
        $competitor = Competitor::create(['competition' => $this->comp->id, 'team' => $team->id, 'name' => $name]);

        if ($league) {
            $competitor->leagues()->attach($league);
        }

        return $competitor;
    }

    protected function league(string $name): League
    {
        $league = new League();
        $league->name = $name;
        $league->competition = $this->comp->id;
        $league->save();

        return $league;
    }

    protected function formula(array $schema): ScoringSchema
    {
        $formula = new ScoringSchema();
        $formula->name = 'Formula';
        $formula->schema = $schema;
        $formula->save();

        return $formula;
    }

    /**
     * A speed event scored by `formula`, with a result for each entity: `[[entity, time], …]`
     */
    protected function speedEvent(?array $formula, array $results = [], string $scores = 'team', string $name = 'Swim & Tow'): CompetitionSpeedEvent
    {
        $base = new SpeedEvent();
        $base->name = $name;
        $base->save();

        $event = new CompetitionSpeedEvent();
        $event->event = $base->id;
        $event->competition = $this->comp->id;
        $event->scorable_entity = $scores;
        $event->record = 0;
        $event->scoring_schema = $formula === null ? null : $this->formula($formula)->id;
        $event->save();

        foreach ($results as [$entity, $time]) {
            $this->time($event, $entity, $time);
        }

        return $event;
    }

    protected function time(CompetitionSpeedEvent $event, Entity $entity, ?int $time): SpeedResult
    {
        $result = new SpeedResult();
        $result->event = $event->id;
        $result->entity()->associate($entity);
        $result->result = $time;
        $result->save();

        return $result;
    }

    protected function seedTime(CompetitionSpeedEvent $event, Entity $entity, int $seed): void
    {
        EntityEventSeed::create(['speed_event' => $event->id, 'entity_type' => $entity->getMorphClass(), 'entity_id' => $entity->id, 'seed' => $seed]);
    }

    /**
     * A SERC scored by `formula`, with one Objective holding a marking point per weight, and each
     * entity's marks in marking point order: `[[entity, [mark, …]], …]`
     */
    protected function serc(array $formula, array $weights = [1], array $marks = [], string $scores = 'team', string $name = 'Dry SERC'): SERC
    {
        $serc = new SERC();
        $serc->name = $name;
        $serc->type = 'DRY';
        $serc->scorable_entity = $scores;
        $serc->competition = $this->comp->id;
        $serc->scoring_schema = $this->formula($formula)->id;
        $serc->save();

        $objective = new SERCJudge();
        $objective->name = 'Casualty 1';
        $objective->description = '';
        $objective->serc = $serc->id;
        $objective->save();

        $points = [];
        foreach ($weights as $index => $weight) {
            $point = new SERCMarkingPoint();
            $point->name = 'Marking point ' . ($index + 1);
            $point->weight = $weight;
            $point->judge = $objective->id;
            $point->serc = $serc->id;
            $point->save();
            $points[] = $point;
        }

        foreach ($marks as [$entity, $entityMarks]) {
            foreach ($entityMarks as $index => $mark) {
                $result = new SERCResult();
                $result->marking_point = $points[$index]->id;
                $result->entity()->associate($entity);
                $result->result = $mark;
                $result->save();
            }
        }

        return $serc;
    }

    /**
     * A Results sheet over `events`, each an Event or `[Event, weight]`
     */
    protected function sheet(array $settings, array $events): ResultSchema
    {
        $sheet = new ResultSchema();
        $sheet->name = 'Overall';
        $sheet->competition = $this->comp->id;
        $sheet->schema = [
            'equation' => 'item.points',
            'global_variables' => [],
            'league' => null,
            'ignore_dq' => false,
            'group_on' => 'team',
            'rank_higher' => true,
            'break_ties' => [],
            ...$settings,
        ];
        $sheet->save();

        foreach ($events as $event) {
            [$event, $weight] = is_array($event) ? $event : [$event, 1];

            $row = new ResultSchemaEvent();
            $row->schema = $sheet->id;
            $row->event_id = $event->id;
            $row->event_type = $event instanceof SERC ? '\App\Models\SERC' : '\App\Models\CompetitionSpeedEvent';
            $row->weight = $weight;
            $row->save();
        }

        return $sheet;
    }

    /**
     * @param ResultSchema[] $sheets
     */
    protected function master(array $settings, array $sheets): MasterSchema
    {
        $master = new MasterSchema();
        $master->name = 'Club trophy';
        $master->competition = $this->comp->id;
        $master->schema = [
            'sheets' => array_map(fn($sheet) => ['id' => $sheet->id, 'weight' => 1], $sheets),
            'group_on' => 'club',
            'sum_over' => 'position',
            'default_value' => 10,
            'exclude' => [],
            ...$settings,
        ];
        $master->save();

        return $master;
    }

    /**
     * The Event's results as pages read them: everyone's, or a League's
     *
     * @return EventResult[]
     */
    protected function eventResults(Event $event, ?League $league = null): array
    {
        return app(CompetitionResults::class)->event($event, $league);
    }

    /**
     * The Results sheet's rows as pages read them
     *
     * @return SheetRow[]
     */
    protected function sheetRows(ResultSchema $sheet): array
    {
        return app(CompetitionResults::class)->sheet($sheet);
    }

    /**
     * An Event's ranked results in the engine's order, as `[who, place, points]` rows
     *
     * @param EventResult[] $results
     */
    protected function placings(array $results): array
    {
        return array_map(fn(EventResult $r) => [$this->who($r->entry), $r->position, round($r->points, 2)], $results);
    }

    /**
     * A sheet's results in the engine's order, as `[who, place, total]` rows
     *
     * @param SheetRow[] $rows
     */
    protected function standings(array $rows): array
    {
        return array_map(fn(SheetRow $r) => [$this->who($r->entry), $r->position, round($r->totalPoints, 2)], $rows);
    }

    /**
     * The one result for an entity
     *
     * @param EventResult[] $results
     */
    protected function resultFor(array $results, Entity $entity): EventResult
    {
        foreach ($results as $result) {
            if ($result->entry->type === $entity->getMorphClass() && $result->entry->id === $entity->id) {
                return $result;
            }
        }

        $this->fail("No result for {$this->who($entity)}");
    }

    /**
     * A team's letter, or a competitor's or Club's name as stored
     */
    protected function who(Entity|EntryRef $entity): string
    {
        if ($entity instanceof EntryRef) {
            $entity = Relation::getMorphedModel($entity->type)::findOrFail($entity->id);
        }

        return $entity instanceof CompetitionTeam ? $entity->team : $entity->name;
    }
}
