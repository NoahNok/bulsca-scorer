<?php

namespace App\Models;

use App\DTO\OverallResult;
use App\Helpers\ClassHelpers;
use App\Scoring\Engine\SheetScorer;
use App\Scoring\Engine\SheetStanding;
use App\Scoring\Input\SheetDefinition;
use App\Scoring\Input\SheetEvent;
use App\Scoring\ScoringRun;
use App\Models\ResultSchemas\ClubSERCResultSchema;
use App\Models\ResultSchemas\HighestSumResultSchema;
use App\Models\ResultSchemas\NationalsResultSchema;
use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ResultSchema extends Model
{
    use HasFactory, Cloneable;

    protected $casts = [
        'schema' => 'array',
    ];

    public function getResults($refreshCache = false): Collection
    {
        if ($refreshCache) {
            $this->forgetResults();
        }

        return Cache::tags("{$this->getCompetition->cacheKey()}.result-schemas")->rememberForever($this->resultsCacheKey(), function () {
            return $this->getCachedResults();
        });
    }

    /**
     * Drops the cached results, so the next look works them out again (after the sheet's edited)
     */
    public function forgetResults(): void
    {
        Cache::tags("{$this->getCompetition->cacheKey()}.result-schemas")->forget($this->resultsCacheKey());
    }

    private function resultsCacheKey(): string
    {
        return 'result_schema_' . $this->id . '_results';
    }

    /**
     * The sheet's rows, best placed first. Each Event is scored for the sheet's League (once per
     * request, shared with every other sheet that includes it: `ScoringRun`), and `SheetScorer`
     * works the standings out from those. The rows are handed back as the DTOs the pages expect.
     *
     * @return Collection<int, OverallResult>
     */
    private function getCachedResults(): Collection
    {
        $run = app(ScoringRun::class);

        // A League that no longer exists is no League: the sheet is of everyone
        $league = null;
        if (array_key_exists('league', $this->schema)) {
            $league = League::find($this->schema['league']);
        }

        $events = [];
        $eventResults = [];
        $loaded = [];

        foreach ($this->getEvents as $sheetEvent) {
            $event = $sheetEvent->getActualEvent;

            $events[] = $definition = new SheetEvent($event->getType(), $event->id, (float) $sheetEvent->weight);
            $eventResults[] = $run->results($event, $league?->id);
            $loaded[spl_object_id($definition)] = $run->loaded($event);
        }

        $standings = SheetScorer::score(SheetDefinition::fromSchema($this->schema, $events), $eventResults);

        return collect($standings)->map(function (SheetStanding $standing) use ($loaded) {
            $entity = null;
            $results = collect();

            foreach ($standing->events as $result) {
                $from = $loaded[spl_object_id($result->event)];

                $results->push($from->scaledResult($result->scored, $result->adjustedPoints));

                if ($result->entity === $standing->entity) {
                    $entity ??= $from->entity($standing->entity);
                }
            }

            return new OverallResult(
                events: $results,
                entity: $entity,
                totalPoints: $standing->totalPoints,
                position: $standing->position,
            );
        });
    }

    public function getEvents()
    {
        return $this->hasMany(ResultSchemaEvent::class, 'schema', 'id');
    }

    public function getCompetition()
    {
        return $this->hasOne(Competition::class, 'id', 'competition');
    }
}
