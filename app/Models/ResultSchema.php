<?php

namespace App\Models;

use App\DTO\OverallResult;
use App\Helpers\ClassHelpers;
use App\Scoring\Engine\SheetScorer;
use App\Scoring\Engine\SheetStanding;
use App\Scoring\Input\SheetDefinition;
use App\Scoring\Input\SheetEvent;
use App\Scoring\ResultsVersion;
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

    /**
     * The sheet's rows, best placed first. They are cached with the competition's results version
     * (`App\Scoring\ResultsVersion`) and used only while that hasn't moved, so nothing has to clear
     * them: any write that can change a result moves the version on. `$refreshCache` works them out
     * regardless.
     *
     * @return Collection<int, OverallResult>
     */
    public function getResults($refreshCache = false): Collection
    {
        $comp = $this->getCompetition;
        $version = app(ResultsVersion::class)->of($comp);

        // Not versioned (What-If): nothing to say a cached result is still right
        if ($version === null) {
            return $this->scoreResults();
        }

        $key = "{$comp->cacheKey()}.sheet.{$this->id}.results";
        $cached = $refreshCache ? null : Cache::get($key);

        if (is_array($cached) && ($cached['version'] ?? null) === $version) {
            return $cached['results'];
        }

        $results = $this->scoreResults();

        // One entry per sheet, replaced when the version moves, so old versions don't pile up
        Cache::forever($key, ['version' => $version, 'results' => $results]);

        return $results;
    }

    /**
     * The sheet's rows worked out now. Each Event is scored for the sheet's League (once per
     * request, shared with every other sheet that includes it: `ScoringRun`), and `SheetScorer`
     * works the standings out from those. The rows are handed back as the DTOs the pages expect.
     *
     * @return Collection<int, OverallResult>
     */
    private function scoreResults(): Collection
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

    protected static function booted()
    {
        // Its cached results have no version to outlive, so they go with it
        static::deleted(function (ResultSchema $sheet) {
            Cache::forget("competition.{$sheet->competition}.sheet.{$sheet->id}.results");
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
