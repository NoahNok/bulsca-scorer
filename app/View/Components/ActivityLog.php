<?php

namespace App\View\Components;

use App\Models\Activity\Activity;
use App\Models\CompetitionTeam;
use App\Models\DigitalJudge\JudgeDQSubmission;
use App\Models\Event\Disqualification;
use App\Models\Event\Penalty;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ActivityLog extends Component
{

    private array $filters;
    private array $related;
    private $context;

    /**
     * Create a new component instance.
     */
    public function __construct($filters = [], $related = [], $context = null)
    {
        $this->filters = $filters;
        $this->related = $related;
        $this->context = $context;
    }

    private array $allowedQueryFilters = [
        'activity',
        'related',
        // Add more allowed filters as needed
    ];

    private function loadSearchQueryFiltersFromRequest()
    {

        // loop through request query parameters and if they are in allowedQueryFilters add them to filters
        foreach (request()->query() as $key => $value) {

            // skip empty values
            if (empty($value)) {
                continue;
            }

            if (in_array($key, $this->allowedQueryFilters)) {

                // handle related filter separately, it should be in the format related[related_type]=related_id
                if ($key == 'related') {
                    $this->resolveRelated($value);
                } else {

                    // Check for | in value for multiple values
                    if (str_contains($value, '|')) {
                        $value = explode('|', $value);
                    }


                    $this->filters[$key] = $value;
                }
            }
        }
    }

    private function resolveRelated($related)
    {
        // if not array, make it an array
        if (!is_array($related)) {
            $related = [$related];
        }

        // these will be in the format of sp:12 se:11 etc where they should then reference the full class
        foreach ($related as $rel) {
            $split = explode(':', $rel);

            $class = match ($split[0]) {
                'speed' => \App\Models\CompetitionSpeedEvent::class,
                'serc' => \App\Models\SERC::class,
                'comp' => \App\Models\Competition::class,
                'team' => CompetitionTeam::class,
                'submission' => JudgeDQSubmission::class,
                'dq' => Disqualification::class,
                'penalty' => Penalty::class,
                // Add more mappings as needed
                default => null,
            };

            if (!$class) {
                continue;
            }

            $this->related[$class] = count($split) > 1 ? $split[1] : null;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $this->loadSearchQueryFiltersFromRequest();

        $activitiesQuery = Activity::with('user', 'relations.related');

        if (count($this->related) > 0) {


            // loop related, if its a model use it to query, otherwise assume its a key value pair of related_type and related_id
            foreach ($this->related as $key => $value) {

                if (is_object($value)) {

                    $activitiesQuery = $activitiesQuery->whereHas('relations', function ($query) use ($value) {
                        $query->where('related_type', get_class($value))
                            ->where('related_id', $value->id);
                    });
                } else {

                    // Need to add support for multiple relations but as an OR query rather than an AND query, so we need to use whereHas multiple times and not nest them
                    $activitiesQuery = $activitiesQuery->whereHas('relations', function ($query) use ($key, $value) {
                        if ($value === null) {
                            $query->where('related_type', $key);
                        } else {
                            $query->where('related_type', $key)
                                ->where('related_id', $value);
                        }
                    });
                }
            }
        }

        if (count($this->filters) > 0) {
            // loop filters as key value pairs and apply to query
            foreach ($this->filters as $key => $value) {


                if (is_array($value)) {
                    $activitiesQuery = $activitiesQuery->whereIn($key, $value);
                } else {
                    $activitiesQuery = $activitiesQuery->where($key, $value);
                }
            }
        }

        $activities = $activitiesQuery->latest()->paginate(20);
        $context = $this->context;
        $types = Activity::uniqueActivities();

        return view('components.activity-log', compact('activities', 'context', 'types'));
    }
}
