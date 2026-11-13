<?php

namespace App\Scoring;

use App\Models\AbstractClasses\Violation;
use App\Models\Club;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\Competitor;
use App\Models\Event\ScoringSchema;
use App\Models\League;
use App\Models\MasterSchema;
use App\Models\Orders\EntityEventSeed;
use App\Models\Pivots\Leagueable;
use App\Models\ResultSchema;
use App\Models\ResultSchemaEvent;
use App\Models\SERC;
use App\Models\SERCMarkingPoint;
use App\Models\SERCResult;
use App\Models\SpeedResult;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;

/**
 * Each competition's results version (`competitions.results_version`): a number that moves on with
 * every write that can change a result. Results cached against it are used only while it hasn't
 * moved, so a cached result is right or not used, never stale.
 *
 * Saving or deleting any model `competitionsOf()` knows moves its competition's version on
 * (`AppServiceProvider`). A write that raises no model event (a query builder `update()` or
 * `delete()`, raw SQL) does not: call `bump()` after one.
 *
 * The version is the time of the change in microseconds, and always more than the last, so a
 * number given out inside a transaction that is rolled back is never given out again.
 *
 * What-If has no versions (its database isn't migrated with the main one): `of()` is null there,
 * and nothing is cached.
 *
 * There is one of these per request (it is bound `scoped`).
 */
final class ResultsVersion
{
    private const WHAT_IF = 'whatif';

    /** @var array<string, int> */
    private array $versions = [];

    /** @var array<string, ?int> */
    private array $parents = [];

    /**
     * The competition's version now, or null where results aren't versioned
     */
    public function of(Competition $comp): ?int
    {
        $connection = $comp->getConnection();

        if ($connection->getName() === self::WHAT_IF) {
            return null;
        }

        return $this->versions["{$connection->getName()}|{$comp->id}"]
            ??= (int) $connection->table('competitions')->where('id', $comp->id)->value('results_version');
    }

    /**
     * Read every version again when next asked: after a transaction is rolled back, the versions
     * given out inside it are gone
     */
    public function forget(): void
    {
        $this->versions = [];
    }

    /**
     * Something that decides this competition's results has changed
     */
    public function bump(Competition|int $comp, ?Connection $connection = null): void
    {
        $connection ??= $comp instanceof Competition ? $comp->getConnection() : DB::connection();
        $id = $comp instanceof Competition ? $comp->id : $comp;

        app(ScoringRun::class)->forget();

        if ($connection->getName() === self::WHAT_IF) {
            return;
        }

        $connection->update(
            'UPDATE competitions SET results_version = GREATEST(results_version + 1, ?) WHERE id = ?',
            [(int) (microtime(true) * 1_000_000), $id],
        );

        unset($this->versions["{$connection->getName()}|{$id}"]);
    }

    /**
     * A model was saved or deleted: move on the version of each competition whose results it can change
     */
    public function changed(Model $model): void
    {
        if ($model->getConnection()->getName() === self::WHAT_IF) {
            return;
        }

        foreach (array_unique(array_filter($this->competitionsOf($model))) as $id) {
            $this->bump((int) $id, $model->getConnection());
        }
    }

    /**
     * The competitions whose results this model is read for. Everything the engine reads is here:
     * add a model to this list when the engine starts to read it.
     *
     * @return array<int|null>
     */
    private function competitionsOf(Model $model): array
    {
        $attribute = fn(string $name) => $model->getAttributes()[$name] ?? null;

        return match (true) {
            // The competition's own settings (seeds per event)
            $model instanceof Competition => [$model->id],

            // Entries, Leagues, Events (formula, who is scored), sheets and Master sheets
            $model instanceof CompetitionTeam,
            $model instanceof Competitor,
            $model instanceof League,
            $model instanceof SERC,
            $model instanceof CompetitionSpeedEvent,
            $model instanceof ResultSchema,
            $model instanceof MasterSchema => [$attribute('competition')],

            // A Club counts wherever it has a team
            $model instanceof Club => [
                $attribute('competition'),
                ...$model->getConnection()->table('competition_teams')->where('club', $model->id)->distinct()->pluck('competition'),
            ],

            // Times and Marks, marking points and their weights
            $model instanceof SpeedResult => [$this->parent($model, 'competition_speed_events', $attribute('event'))],
            $model instanceof SERCMarkingPoint => [$this->parent($model, 'sercs', $attribute('serc'))],
            $model instanceof SERCResult => [
                $this->parent($model, 'sercs', $this->parent($model, 'serc_marking_points', $attribute('marking_point'), 'serc')),
            ],

            // Penalties and disqualifications
            $model instanceof Violation => [
                $this->parent($model, self::isSerc($attribute('event_type')) ? 'sercs' : 'competition_speed_events', $attribute('event_id')),
            ],

            // Seed times, and who is in a League
            $model instanceof EntityEventSeed => [$this->parent($model, 'competition_speed_events', $attribute('speed_event'))],
            $model instanceof Leagueable => [$this->parent($model, 'leagues', $attribute('league_id'))],

            // A sheet's Events and their weights
            $model instanceof ResultSchemaEvent => [$this->parent($model, 'result_schemas', $attribute('schema'))],

            // An Event formula is shared by every Event that uses it
            $model instanceof ScoringSchema => [
                ...$model->getConnection()->table('sercs')->where('scoring_schema', $model->id)->distinct()->pluck('competition'),
                ...$model->getConnection()->table('competition_speed_events')->where('scoring_schema', $model->id)->distinct()->pluck('competition'),
            ],

            default => [],
        };
    }

    /**
     * A column (the competition, unless told otherwise) of the row this model belongs to. Looked up
     * once: a Mark's marking point doesn't move to another SERC, nor an Event to another competition.
     */
    private function parent(Model $model, string $table, mixed $id, string $column = 'competition'): ?int
    {
        if ($id === null) {
            return null;
        }

        $key = "{$model->getConnection()->getName()}|{$table}|{$id}|{$column}";

        if (!array_key_exists($key, $this->parents)) {
            $this->parents[$key] = $model->getConnection()->table($table)->where('id', $id)->value($column);
        }

        return $this->parents[$key];
    }

    private static function isSerc(?string $morphType): bool
    {
        $class = Relation::getMorphedModel((string) $morphType) ?? (string) $morphType;

        return is_a(ltrim($class, '\\'), SERC::class, true);
    }
}
