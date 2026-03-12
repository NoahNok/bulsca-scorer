<?php

namespace App\Models\Activity;

use App\Models\AbstractClasses\Entity;
use App\Models\AbstractClasses\Event;
use App\Models\AbstractClasses\Violation;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\DigitalJudge\JudgeDQSubmission;
use App\Models\Event\Disqualification;
use App\Models\Event\Penalty;
use App\Models\SERC;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityRelation extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'related_id',
        'related_type',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function related()
    {
        return $this->morphTo();
    }

    private function renderNullRelated(Activity $activity)
    {
        $related_class = $this->related_type;


        return match ($related_class) {

            Penalty::class => view('components.activity-log.null', ['name' => 'Penalty Removed', 'description' => 'This penalty has been removed.']),
            Disqualification::class => view('components.activity-log.null', ['name' => 'Disqualification Removed', 'description' => 'This disqualification has been removed.']),
            CompetitionSpeedEvent::class => view('components.activity-log.null', ['name' => 'Speed Event Deleted', 'description' => 'This speed event has been deleted.']),
            SERC::class => view('components.activity-log.null', ['name' => 'SERC Deleted', 'description' => 'This SERC has been deleted.']),
            JudgeDQSubmission::class => view('components.activity-log.null', ['name' => 'Submission Removed', 'description' => 'This submission has been deleted.']),




            default => 'N/A',
        };
    }

    public function render(Activity $activity)
    {
        $related = $this->related;


        return match (true) {
            $related instanceof Competition => view('components.activity-log.competition', ['name' => $related->name, 'org' => $related->getOrganisation, 'link' => route('comps.view', $related)]),
            $related instanceof Event => view('components.activity-log.event', ['name' => $related->getName(), 'is_serc' => $related instanceof SERC, 'event' => $related]),
            $related instanceof Entity => view('components.activity-log.entity', ['name' => $related->getName(), 'type' => class_basename($related)]),
            $related instanceof Violation => view('components.activity-log.violation', ['name' => "{$related}", 'description' => $related->getMessage()]),
            $related instanceof JudgeDQSubmission => view('components.activity-log.violation-submission', ['name' => "{$related->name} ({$related->position})", 'appealed' => $related->appealed]),
            $related === null => $this->renderNullRelated($activity),


            default => 'Related Item Deleted',
        };



        // Add more cases for other related types if needed
        return 'Unknown related type';
    }
}
