<?php

namespace App\Models\AbstractClasses;


use App\DigitalJudge\DigitalJudge;
use App\DTO\Result;
use App\DTO\ResolvedResult;
use App\DTO\RankedResult;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\DigitalJudge\BetterJudgeLog;
use App\Models\Event\Disqualification;
use App\Models\Event\Penalty;
use App\Models\Event\ScoringSchema;
use App\Models\League;
use App\Models\SERCResult;
use App\Models\SpeedResult;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

abstract class Event extends Model
{

    use CascadesDeletes;
    protected $cascadeDeletes = [
        'penalties',
        'disqualifications'
    ];

    public abstract function getName(): string;
    /**
     * @return Result[]
     */
    public abstract function getRawResults(bool $withEmpty = false, ?League $league = null): array;
    /**
     * @return ResolvedResult[]
     */
    public abstract function getResolvedResults(?League $league = null): array;
    /**
     * @return RankedResult[]
     */
    public abstract function getRankedResults(?League $league = null): array;
    public abstract function results();
    public abstract function getCompetition();
    public function isComplete($no_check = False): bool
    {
        if ($no_check) {
            return $this->completed;
        }

        $complete = $this->checkCompletion();

        $this->completed = $complete;
        $this->save();

        return $complete;
    }
    public abstract function checkCompletion(): bool;

    protected static function booted()
    {
        static::deleting(function (Event $event) {
            $event->penalties()->delete();
            $event->disqualifications()->delete();
        });
    }

    protected function applyGrouping(SupportCollection $results): SupportCollection
    {



        $groupBy = $this->scoringSchema->schema['group_by'] ?? null; // could come from user input

        if (!$groupBy || empty($groupBy)) {
            return $results;
        }

        $grouped = collect($results)->groupBy(function (ResolvedResult $result) use ($groupBy) {
            $entityGrouping = $result->entity->getGrouping();
            $parts = collect($groupBy)->map(function ($key) use ($entityGrouping) {
                return match ($key) {
                    'league' => $entityGrouping->league_id ?? 'no-league',
                    'team'   => $entityGrouping->team_id ?? 'no-team',
                    'club'   => $entityGrouping->club_id ?? 'no-club',
                    default  => 'unknown',
                };
            });

            // Join into a single flat key like "league:1|team:5|club:9"
            return $parts->implode('|');
        });




        // Combine results within each group
        $combinedResults = $grouped->map(function ($group) {
            /** @var ResolvedResult[] $group */
            $base = $group->shift(); // take the first result as base
            foreach ($group as $result) {
                $base->combineWith($result);
            }
            return $base;
        })->values();

        return $combinedResults;
    }

    public function penalties()
    {
        return $this->morphMany(Penalty::class, 'event');
    }

    public function disqualifications()
    {
        return $this->morphMany(Disqualification::class, 'event');
    }



    public function addEntityPenalty(Entity $entity, int $code): Penalty
    {
        $penalty = $this->penalties()->make([
            'code' => $code
        ]);

        $penalty->entity()->associate($entity);

        $penalty->save();

        return $penalty;
    }

    public function addEntityDisqualification(Entity $entity, int $code): Disqualification
    {

        $disqualification = $this->disqualifications()->make([
            'code' => $code
        ]);

        $disqualification->entity()->associate($entity);

        $disqualification->save();
        return $disqualification;
    }

    public function clearEntityPenalties(Entity $entity)
    {
        $this->penalties()->whereMorphedTo('entity', $entity)->delete();
    }

    public function clearEntityDisqualifications(Entity $entity)
    {
        $this->disqualifications()->whereMorphedTo('entity', $entity)->delete();
    }

    public function getEntityPenalties(Entity $entity)
    {
        return $this->penalties()->whereMorphedTo('entity', $entity);
    }

    public function getEntityDisqualifications(Entity $entity)
    {
        return $this->disqualifications()->whereMorphedTo('entity', $entity);
    }

    public function scoringSchema()
    {
        return $this->belongsTo(ScoringSchema::class, 'scoring_schema');
    }

    public function getScorableEntity(): Entity
    {
        return new (Relation::getMorphedModel($this->scorable_entity));
    }

    public function hide()
    {
        $this->viewable = !$this->viewable;
        $this->save();
    }

    public function getScorableEntities()
    {
        return $this->getScorableEntity()::where('competition', $this->competition)->get();
    }
}
