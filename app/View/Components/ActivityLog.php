<?php

namespace App\View\Components;

use App\Models\Activity\Activity;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\Activity\ActivityRelation;
use App\Models\DigitalJudge\JudgeDQSubmission;
use App\Models\DigitalJudge\Violation\ViolationSubmission;
use App\Models\Event\Disqualification;
use App\Models\Event\Penalty;
use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\View\Component;

class ActivityLog extends Component
{

    private array $filters;
    private array $related = [];
    private array $by = [];
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
        'by',
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
                } elseif ($key == 'by') {
                    // user ids, or 'system' for activity with no user
                    $this->by = collect(explode('|', $value))->filter(fn($v) => $v === self::BY_SYSTEM || ctype_digit($v))->values()->all();
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

    private const BY_SYSTEM = 'system';

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
        'violation' => ViolationSubmission::class,
        // Add more mappings as needed
    ];

    // The chip group each alias belongs to. Filters in the same group are ORed, different groups are ANDed
    private const RELATED_KINDS = [
        'speed' => 'Event',
        'serc' => 'Event',
        'team' => 'Team',
        'competitor' => 'Competitor',
        'submission' => 'Submission',
        'dq' => 'DQ',
        'penalty' => 'Penalty',
        'violation' => 'Submission',
        'comp' => 'Competition',
    ];

    // Chip names for a bare alias, e.g. related=serc
    private const RELATED_ALL_LABELS = [
        'speed' => 'All speed events',
        'serc' => 'All SERCs',
        'comp' => 'All competitions',
        'team' => 'All teams',
        'competitor' => 'All competitors',
        'submission' => 'All submissions',
        'dq' => 'All DQs',
        'penalty' => 'All penalties',
        'violation' => 'All submissions',
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
     * The current URL with a |-separated filter changed, back on the first page
     */
    private function urlWithValues(string $param, array $values): string
    {
        $query = array_filter([...request()->query(), $param => implode('|', $values), 'page' => null], fn($v) => $v !== null && $v !== '');

        return request()->url() . (count($query) > 0 ? '?' . Arr::query($query) : '');
    }

    private function currentValues(string $param): array
    {
        return collect(explode('|', (string) request()->query($param, '')))->reject(fn($v) => $v === '')->values()->all();
    }

    private function urlWithout(string $param, string $value): string
    {
        return $this->urlWithValues($param, array_diff($this->currentValues($param), [$value]));
    }

    /**
     * Adds the value if it's not in the filter, removes it if it is
     */
    private function urlToggling(string $param, string $value): string
    {
        $values = $this->currentValues($param);

        return in_array($value, $values) ? $this->urlWithout($param, $value) : $this->urlWithValues($param, [...$values, $value]);
    }

    /**
     * Removable chips for every filter, grouped by kind, so it's clear why entries are missing
     */
    private function chipGroups(array $related, ?string $compId): array
    {
        // On a competition's log, only name things from that competition, so ids can't be used to look up others
        $relatedName = function ($rel) use ($compId) {
            if ($rel['id'] === null) {
                return self::RELATED_ALL_LABELS[$rel['alias']];
            }

            $model = $rel['class']::find($rel['id']);

            $modelCompId = match (true) {
                $model instanceof Competition => $model->id,
                $model instanceof ViolationSubmission => $model->competition_id,
                default => $model?->competition,
            };

            $inComp = $compId === null || $modelCompId == $compId;

            return match (true) {
                $model === null || !$inComp => "#{$rel['id']}",
                $model instanceof Competition => $model->name,
                $model instanceof ViolationSubmission => "{$model->code()} for {$model->entity?->getName()}",
                method_exists($model, 'getName') => $model->getName(),
                default => "#{$rel['id']}",
            };
        };

        $activities = (array) ($this->filters['activity'] ?? []);
        $types = collect($activities)->map(fn($type) => [
            'label' => str_replace('_', ' ', $type),
            'remove_url' => $this->urlWithout('activity', $type),
        ]);

        $relatedGroups = collect($related)->groupBy(fn($rel) => self::RELATED_KINDS[$rel['alias']])->map(fn($group) => $group->map(fn($rel) => [
            'label' => $relatedName($rel),
            'remove_url' => $this->urlWithout('related', $rel['raw']),
        ]));

        // Like other chips, a competition's log only names people who did something on it
        $users = ($compId ? $this->peopleIn($compId) : User::query())->whereIn('id', array_diff($this->by, [self::BY_SYSTEM]))->pluck('name', 'id');
        $people = collect($this->by)->map(fn($id) => [
            'label' => $id === self::BY_SYSTEM ? 'System' : ($users[$id] ?? "#{$id}"),
            'remove_url' => $this->urlWithout('by', $id),
        ]);

        $kinds = array_unique(self::RELATED_KINDS);

        return collect(['Type' => $types, ...collect($kinds)->mapWithKeys(fn($kind) => [$kind => $relatedGroups[$kind] ?? collect()]), 'Actioned by' => $people])
            ->filter(fn($chips) => $chips->isNotEmpty())
            ->map(fn($chips, $label) => ['label' => $label, 'chips' => $chips->values()->all()])
            ->values()->all();
    }

    /**
     * What a footer item links to: the filter it toggles and its own page.
     * Neither for something deleted, or for what the page already forces, like a competition's own log
     */
    private function footerLinks(ActivityRelation $relation, array $forcedRelated): array
    {
        $alias = array_search($relation->related_type, self::RELATED_ALIASES);
        $value = "{$alias}:{$relation->related_id}";

        if ($relation->related === null || in_array($value, collect($forcedRelated)->pluck('raw')->all())) {
            return ['filter' => null, 'link' => null];
        }

        return [
            'filter' => $alias === false ? null : [
                'url' => $this->urlToggling('related', $value),
                'active' => in_array($value, $this->currentValues('related')),
            ],
            'link' => $relation->link(),
        ];
    }

    /**
     * Users who have recorded Activity on a competition
     */
    private function peopleIn($compId)
    {
        return User::whereIn('id', Activity::whereHas('relations', fn($q) => $q->where('related_type', Competition::class)->where('related_id', $compId))->select('user_id'));
    }

    /**
     * Options for the "Add …" selects on a competition's log, leaving out what's already picked
     */
    private function pickers(Competition $comp, array $related): array
    {
        $picked = collect($related)->pluck('raw')->merge($this->by)->all();
        $option = fn($value, $label) => ['value' => (string) $value, 'label' => $label];
        $unpicked = fn($options) => collect($options)->reject(fn($o) => in_array($o['value'], $picked))->values()->all();

        $people = $this->peopleIn($comp->id)->orderBy('name')->get()->map(fn($user) => $option($user->id, $user->name));

        return [
            ['label' => 'Event', 'param' => 'related', 'placeholder' => 'Add event…', 'groups' => [
                '' => $unpicked([$option('serc', 'All SERCs'), $option('speed', 'All speed events')]),
                'SERCs' => $unpicked($comp->getSERCs->map(fn($serc) => $option("serc:{$serc->id}", $serc->getName()))),
                'Speed events' => $unpicked($comp->getSpeedEvents->map(fn($speed) => $option("speed:{$speed->id}", $speed->getName()))),
            ]],
            ['label' => 'Team', 'param' => 'related', 'placeholder' => 'Add team…', 'groups' => [
                '' => $unpicked($comp->getCompetitionTeams->map(fn($team) => $option("team:{$team->id}", $team->getName($comp)))->sortBy('label')),
            ]],
            // Only offer a Competitor picker when the competition has any
            ...($comp->getCompetitors->isEmpty() ? [] : [['label' => 'Competitor', 'param' => 'related', 'placeholder' => 'Add competitor…', 'groups' => [
                '' => $unpicked($comp->getCompetitors->map(fn($competitor) => $option("competitor:{$competitor->id}", $competitor->getName($comp)))->sortBy('label')),
            ]]]),
            ['label' => 'Actioned by', 'param' => 'by', 'placeholder' => 'Add person…', 'groups' => [
                '' => $unpicked([...$people, $option(self::BY_SYSTEM, 'System')]),
            ]],
        ];
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

        // Values of the same kind are ORed (team A or team B), different kinds are ANDed (and in SERC 2).
        // Forced filters are their own groups so a page's competition can never be ORed away
        $groups = [
            ...collect($forcedRelated)->map(fn($rel) => [$rel])->all(),
            ...collect($related)->groupBy(fn($rel) => self::RELATED_KINDS[$rel['alias']])->map->all()->values()->all(),
        ];

        foreach ($groups as $group) {
            $activitiesQuery = $activitiesQuery->whereHas('relations', function ($query) use ($group) {
                $query->where(function ($query) use ($group) {
                    foreach ($group as $rel) {
                        $query->orWhere(function ($query) use ($rel) {
                            $query->where('related_type', $rel['class']);

                            if ($rel['id'] !== null) {
                                $query->where('related_id', $rel['id']);
                            }
                        });
                    }
                });
            });
        }



        if (count($this->by) > 0) {
            $userIds = array_diff($this->by, [self::BY_SYSTEM]);
            $includeSystem = in_array(self::BY_SYSTEM, $this->by);

            $activitiesQuery = $activitiesQuery->where(function ($query) use ($userIds, $includeSystem) {
                $query->whereIn('user_id', $userIds);

                if ($includeSystem) {
                    $query->orWhereNull('user_id');
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
        $activeTypes = $this->currentValues('activity');
        $types = Activity::uniqueActivities()->map(fn($type) => [
            'label' => str_replace('_', ' ', $type),
            'active' => in_array($type, $activeTypes),
            'url' => $this->urlToggling('activity', $type),
        ]);
        $compId = collect($forcedRelated)->firstWhere('class', Competition::class)['id'] ?? null;

        $chipGroups = $this->chipGroups($related, $compId);
        $clearUrl = request()->url();

        $comp = $compId ? Competition::find($compId) : null;
        $pickers = $comp ? $this->pickers($comp, $related) : [];

        $footerLinks = fn(ActivityRelation $relation) => $this->footerLinks($relation, $forcedRelated);

        return view('components.activity-log', compact('activities', 'context', 'types', 'chipGroups', 'clearUrl', 'pickers', 'footerLinks'));
    }
}
