<?php

namespace App\View\Components;

use App\Models\Activity\Activity;
use App\Models\Competition;
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

    // Short names for related models in ?related= filters, e.g. related=serc:3|team:12
    private const RELATED_ALIASES = [
        'speed' => \App\Models\CompetitionSpeedEvent::class,
        'serc' => \App\Models\SERC::class,
        'comp' => \App\Models\Competition::class,
        'team' => CompetitionTeam::class,
        'competitor' => \App\Models\Competitor::class,
        'submission' => JudgeDQSubmission::class,
        'dq' => Disqualification::class,
        'penalty' => Penalty::class,
        // Add more mappings as needed
    ];

    private const RELATED_LABELS = [
        'speed' => 'Speed Event',
        'serc' => 'SERC',
        'comp' => 'Competition',
        'team' => 'Team',
        'competitor' => 'Competitor',
        'submission' => 'Submission',
        'dq' => 'DQ',
        'penalty' => 'Penalty',
    ];

    /**
     * @return array<int, array{alias: string, class: class-string, id: ?string, raw: string}>
     */
    private function resolveRelated($related): array
    {
        // if not array, make it an array
        if (!is_array($related)) {
            $related = [$related];
        }

        $returnedRelated = [];
        foreach ($related as $rel) {
            $split = explode(':', $rel);

            $class = self::RELATED_ALIASES[$split[0]] ?? null;

            if (!$class) {
                continue;
            }

            $id = count($split) > 1 && $split[1] !== '' ? $split[1] : null;

            $returnedRelated[] = ['alias' => $split[0], 'class' => $class, 'id' => $id, 'raw' => $rel];
        }

        return $returnedRelated;
    }

    /**
     * A removable chip for each ?related= filter, so it's clear why entries are missing
     */
    private function relatedChips(array $related, array $forcedRelated): array
    {
        // On a competition's log, only name things from that competition, so ids can't be used to look up others
        $compId = collect($forcedRelated)->firstWhere('class', Competition::class)['id'] ?? null;

        return collect($related)->filter(fn($rel) => $rel['id'] !== null)->map(function ($rel) use ($related, $compId) {
            $model = $rel['class']::find($rel['id']);

            $inComp = $compId === null || ($model instanceof Competition ? $model->id : $model?->competition) == $compId;

            $name = match (true) {
                $model === null || !$inComp => "#{$rel['id']}",
                $model instanceof Competition => $model->name,
                method_exists($model, 'getName') => $model->getName(),
                default => "#{$rel['id']}",
            };

            $remaining = collect($related)->reject(fn($other) => $other === $rel)->pluck('raw')->implode('|');

            return [
                'label' => self::RELATED_LABELS[$rel['alias']] . ": {$name}",
                'remove_url' => request()->fullUrlWithQuery(['related' => $remaining ?: null, 'page' => null]),
            ];
        })->values()->all();
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

        // An activity must be related to every model asked for, so each needs its own whereHas
        foreach ([...$forcedRelated, ...$related] as $rel) {
            $activitiesQuery = $activitiesQuery->whereHas('relations', function ($query) use ($rel) {
                $query->where('related_type', $rel['class']);

                if ($rel['id'] !== null) {
                    $query->where('related_id', $rel['id']);
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
        $chips = $this->relatedChips($related, $forcedRelated);


        return view('components.activity-log', compact('activities', 'context', 'types', 'chips'));
    }
}
