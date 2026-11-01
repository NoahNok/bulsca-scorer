<?php

namespace App\Models\Activity;

use App\Models\AbstractClasses\Entity;
use App\Models\AbstractClasses\Event;
use App\Models\AbstractClasses\Violation;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\Competitor;
use App\Models\DigitalJudge\JudgeDQSubmission;
use App\Models\DigitalJudge\Violation\ViolationSubmission;
use App\Models\Event\Disqualification;
use App\Models\Event\Penalty;
use App\Models\SERC;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Something an Activity is about: an event, Entity, submission, DQ or the competition. It knows how
 * it's named in the Activity lists (App\Services\ActivityFeed, and the Admin Activity Monitor's Blade
 * partials), even once it's been deleted.
 */
class ActivityRelation extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'related_id',
        'related_type',
    ];

    /** Short names for each kind of related model, as ?related= filters use them, e.g. related=serc:3|team:12 */
    public const ALIASES = [
        'speed' => CompetitionSpeedEvent::class,
        'serc' => SERC::class,
        'comp' => Competition::class,
        'team' => CompetitionTeam::class,
        'competitor' => Competitor::class,
        'submission' => JudgeDQSubmission::class,
        'dq' => Disqualification::class,
        'penalty' => Penalty::class,
        'violation' => ViolationSubmission::class,
    ];

    // What a related item is called once it's gone, by alias
    private const DELETED_NAMES = [
        'speed' => 'Speed event deleted',
        'serc' => 'SERC deleted',
        'comp' => 'Competition deleted',
        'team' => 'Team deleted',
        'competitor' => 'Competitor deleted',
        'submission' => 'Submission removed',
        'violation' => 'Submission removed',
        'dq' => 'DQ removed',
        'penalty' => 'Penalty removed',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function related()
    {
        return $this->morphTo();
    }

    /**
     * Its alias in ALIASES, or null for a kind of model the Activity lists don't know
     */
    public function alias(): ?string
    {
        $alias = array_search($this->related_type, self::ALIASES);

        return $alias === false ? null : $alias;
    }

    /**
     * How a related model is named in Activity, e.g. "DQ12 for Team A"
     */
    public static function nameOf(Model $related): string
    {
        return match (true) {
            $related instanceof Competition => $related->name,
            $related instanceof Event, $related instanceof Entity => $related->getName(),
            $related instanceof Violation => "{$related}",
            $related instanceof JudgeDQSubmission => "{$related->name} ({$related->position})",
            $related instanceof ViolationSubmission => "{$related->code()} for {$related->entity?->getName()}",
            default => "#{$related->getKey()}",
        };
    }

    public function name(): string
    {
        return $this->related ? self::nameOf($this->related) : (self::DELETED_NAMES[$this->alias()] ?? 'Deleted');
    }

    /**
     * A line more about it: a competition's organisation, a DQ or penalty's message, a submission's code
     */
    public function detail(): ?string
    {
        $related = $this->related;

        return match (true) {
            $related instanceof Competition => $related->getOrganisation?->name,
            $related instanceof Violation => $related->getMessage(),
            $related instanceof JudgeDQSubmission => $related->code,
            default => null,
        };
    }

    /**
     * A submission that's been appealed, so no longer stands
     */
    public function appealed(): bool
    {
        $related = $this->related;

        return match (true) {
            $related instanceof JudgeDQSubmission => (bool) $related->appealed,
            $related instanceof ViolationSubmission => $related->status === 'APPEALED',
            default => false,
        };
    }

    /**
     * The page for the related item, if it has one, shown as a link next to it in the log
     */
    public function link(): ?string
    {
        $related = $this->related;

        return match (true) {
            $related instanceof Competition => route('comps.view', $related),
            $related instanceof SERC => route('manage.competitions.events.sercs.show', [$related->competition, $related->id]),
            $related instanceof CompetitionSpeedEvent => route('manage.competitions.events.speeds.show', [$related->competition, $related->id]),
            default => null,
        };
    }

    /**
     * The Admin Activity Monitor's Blade partial for it
     */
    public function render()
    {
        $related = $this->related;
        $name = $this->name();

        return match (true) {
            $related === null => view('components.activity-log.null', ['name' => $name, 'description' => $name]),
            $related instanceof Competition => view('components.activity-log.competition', ['name' => $name, 'org' => $related->getOrganisation]),
            $related instanceof Event => view('components.activity-log.event', ['name' => $name, 'is_serc' => $related instanceof SERC]),
            $related instanceof Entity => view('components.activity-log.entity', ['name' => $name, 'type' => class_basename($related)]),
            $related instanceof Violation => view('components.activity-log.violation', ['name' => $name, 'description' => $this->detail()]),
            $related instanceof JudgeDQSubmission, $related instanceof ViolationSubmission => view('components.activity-log.violation-submission', ['name' => $name, 'appealed' => $this->appealed()]),
            default => $name,
        };
    }
}
