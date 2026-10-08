<?php

namespace App\Models\DigitalJudge\Violation;

use App\Models\AbstractClasses\Event;
use App\Models\Activity\Activity;
use App\Models\Activity\ActivityRelation;
use App\Models\AbstractClasses\Violation;
use App\Models\Competition;
use App\Models\DQCode;
use App\Models\Interfaces\IJsonable;
use App\Models\User;
use App\Traits\RecordActivity;
use Exception;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Override;

class ViolationSubmission extends Model implements IJsonable
{
    // THIS IS REFERENCED BY A MIGRATION, ANY CHANGES MUST BE REFLECTED INTO THE DATABASE AS THE COLUMN IS A ENUM
    public static array $STATES = ["SUBMITTED", "ACCEPTED", "REJECTED", "APPEALED", "REMOVED"];

    use HasUuids, RecordActivity;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }
    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    public function event(): MorphTo
    {
        return $this->morphTo();
    }

    public function submitted(): MorphTo
    {
        return $this->morphTo();
    }

    public function applied(): MorphTo
    {
        return $this->morphTo();
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitter_id');
    }

    public function activityRelations(): MorphMany
    {
        return $this->morphMany(ActivityRelation::class, 'related');
    }

    private function isDq(): bool
    {
        return $this->submitted instanceof (DQCode::class);
    }

    public function code(): string
    {
        return ($this->isDq() ? "DQ" : "P") . $this->submitted->code;
    }

    #[Override]
    public function jsonable(): array
    {
        $violation = $this->submitted;
        $user = $this->submitter;



        return [
            'id' => $this->id,
            'status' => $this->status,
            'entity' => $this->entity->jsonable(),
            'event' => $this->event->jsonable(),
            'violation' => [
                'id' => $violation->id,
                'code' => $violation->code,
                'description' => $violation->description,
                'type' => null,
                'vtype' => $this->isDq() ? "DQ" : "PEN"
            ],
            'details' => [
                'turn' => $this->turn,
                'length' => $this->length,
                'details' => $this->details,
            ],
            'submitter' => [
                'user' => ['id' => $user->id, 'name' => $user->name],
                'position' => $this->submitter_position
            ],
            'seconder' => [
                'name' => $this->seconder_name,
                'position' => $this->seconder_position
            ],
            'order' => $this->event->getType() == "speed" ? $this->getHeatLane() : $this->getTankDraw()
        ];
    }

    private function getHeatLane()
    {
        $heatlane = $this->event->getHeats()->whereMorphedTo('entity', $this->entity)->first();

        if (!$heatlane) {
            $entityName = $this->entity->name;
            $eventName = $this->event->getName();
            throw new Exception("Unable to locate attached heat for DQ/Pen which isn't acceptable - {$entityName} in {$eventName}");
        }

        return [
            'heat' => $heatlane->heat,
            'lane' => $heatlane->lane
        ];
    }

    private function getTankDraw()
    {
        $use_tanks = $this->competition->getScoringSettings->use_tanks;

        $tankdraw = $this->event->getDraw()->whereMorphedTo('entity', $this->entity)->first();

        $draw = ['draw' => $tankdraw->draw];

        if ($use_tanks) {
            $draw['tank'] = $tankdraw->tank;
        }

        return $draw;
    }



    // SUBMISSION MANAGEMENT

    public function updateStatus(string $status)
    {
        $from = $this->status;
        $this->status = $status;

        // Only ACCEPTED submissions should have a violation applied to the entity
        if ($status == "ACCEPTED") {
            if (!$this->applied_id) {
                $this->applyToEntity();
            }
        } else if ($this->applied_id) {
            $this->removeFromEntity();
        }

        $this->save();

        if ($from !== $status) {
            $this->logActivity($status, $from);
        }
    }

    public function applyToEntity()
    {
        /**
         * @var Event
         */
        $event = $this->event;

        $applied = null;
        if ($this->isDq()) {
            $event->clearEntityDisqualifications($this->entity);
            $applied = $event->addEntityDisqualification($this->entity, $this->submitted->code);
        } else {
            $applied = $event->addEntityPenalty($this->entity, $this->submitted->code);
        }

        $this->applied()->associate($applied);
    }

    public function removeFromEntity()
    {
        // Delete the exact record this submission applied, not just any violation with a matching code
        $this->applied?->delete();

        $this->applied()->dissociate();
    }



    // TIMELINE

    /**
     * Record a state for this submission in the activity log, so it shows on its timeline
     */
    public function logActivity(string $state, ?string $from = null): void
    {
        $what = "{$this->code()} for {$this->entity->getName()} in {$this->event->getName()}";

        $description = match ($state) {
            'SUBMITTED' => "{$what} submitted by {$this->submitter?->name} ({$this->submitter_position})",
            'ACCEPTED' => "{$what} was accepted by the referee",
            'REJECTED' => "{$what} was rejected by the referee",
            'APPEALED' => "{$what} was marked as appealed",
            'REMOVED' => "{$what} was removed by the referee",
            default => "{$what} changed to {$state}",
        };

        $this->recordActivity(
            "VIOLATION_{$state}",
            $description,
            context: ['code' => $this->code(), 'from' => $from, 'to' => $state],
            related: [$this, $this->entity, $this->event, $this->competition]
        );
    }

    /**
     * Activities linked to this submission, oldest first
     */
    public function timeline()
    {
        // RecordActivity stores the class name rather than the morph alias, so match on that.
        // Order by the relation's auto-increment id, as activity ids are random UUIDs and
        // created_at can tie within the same second
        return Activity::select('activities.*')
            ->join('activity_relations', 'activity_relations.activity_id', '=', 'activities.id')
            ->where('activity_relations.related_type', self::class)
            ->where('activity_relations.related_id', $this->id)
            ->where('activities.activity', 'like', 'VIOLATION_%')
            ->with('user')
            ->orderBy('activity_relations.id')
            ->get();
    }

    public function jsonableTimeline(): array
    {
        $entries = $this->timeline()->map(fn(Activity $activity) => [
            'id' => $activity->id,
            'state' => $activity->context['to'] ?? str_replace('VIOLATION_', '', $activity->activity),
            'from' => $activity->context['from'] ?? null,
            'at' => $activity->created_at?->toIso8601String(),
            'user' => $activity->user ? ['name' => $activity->user->name] : null,
        ]);

        // Submissions made before activity logging have no SUBMITTED entry, so fall back to when it was created
        if (!$entries->contains(fn($entry) => $entry['state'] === 'SUBMITTED')) {
            $entries->prepend([
                'id' => "submitted-{$this->id}",
                'state' => 'SUBMITTED',
                'from' => null,
                'at' => $this->created_at?->toIso8601String(),
                'user' => $this->submitter ? ['name' => $this->submitter->name] : null,
            ]);
        }

        return $entries->values()->all();
    }
}
