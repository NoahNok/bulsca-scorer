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
    private array $related = [];
    private array $forcedRelated = [];
    private $context;

    /**
     * Create a new component instance.
     */
    public function __construct($filters = [], $related = [], $context = null)
    {
        $this->filters = $filters;

        $this->context = $context;

        $this->forcedRelated = $related;
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
                    $this->related = explode('|', $value);
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

    private function resolveRelated($related): array
    {
        // if not array, make it an array
        if (!is_array($related)) {
            $related = [$related];
        }

        $returnedRelated = [];
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

            $returnedRelated[$class] = count($split) > 1 ? $split[1] : null;
        }

        return $returnedRelated;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $this->loadSearchQueryFiltersFromRequest();

        $related = $this->resolveRelated($this->related);
        $forcedRelated = $this->resolveRelated($this->forcedRelated);

        $activitiesQuery = Activity::with('user', 'relations.related');

        if (count($related) > 0 || count($forcedRelated) > 0) {

            $activitiesQuery = $activitiesQuery->whereHas('relations', function ($query) use ($related, $forcedRelated) {
                foreach ($forcedRelated as $type => $type_id) {
                    $query->where(function ($query) use ($type, $type_id) {
                        $query->where('related_type', $type);

                        if ($type_id !== null && $type_id !== '') {
                            $query->where('related_id', $type_id);
                        }
                    });
                }

                foreach ($related as $type => $type_id) {
                    $query->orWhere(function ($query) use ($type, $type_id) {
                        $query->where('related_type', $type);

                        if ($type_id !== null && $type_id !== '') {
                            $query->where('related_id', $type_id);
                        }
                    });
                }
            });
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
