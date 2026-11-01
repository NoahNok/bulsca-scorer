<?php

namespace App\Services;

use App\Models\AbstractClasses\Entity;
use App\Models\AbstractClasses\Event;
use App\Models\AbstractClasses\Violation;
use App\Models\Activity\Activity;
use App\Models\Activity\ActivityRelation;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\Competitor;
use App\Models\DigitalJudge\JudgeDQSubmission;
use App\Models\DigitalJudge\Violation\ViolationSubmission;
use App\Models\Event\Disqualification;
use App\Models\Event\Penalty;
use App\Models\SERC;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

/**
 * Activity filtered the way the `related`, `by` and `activity` query parameters say, as plain arrays: a
 * competition's Activity page (forced to that competition) and the Admin Activity Monitor (everything)
 * both read it. Filters of the same kind are ORed (team A or team B), different kinds ANDed (and in SERC 2).
 */
class ActivityFeed
{
    public const PER_PAGE = 20;

    public const BY_SYSTEM = 'system';

    /** The Activity about Marks, Times and Places: the History shortcut */
    public const HISTORY = ['SERC_MARKED', 'SERC_RESULT_UPDATED', 'SPEED_RESULT_MARKED', 'SPEED_RESULT_UPDATED', 'OOF_MARKED'];

    // Short names for related models in ?related= filters, e.g. related=serc:3|team:12
    private const RELATED_ALIASES = [
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

    // The chip group each alias belongs to, in the order the groups show
    private const RELATED_KINDS = [
        'speed' => 'Event',
        'serc' => 'Event',
        'team' => 'Team',
        'competitor' => 'Competitor',
        'submission' => 'Submission',
        'violation' => 'Submission',
        'dq' => 'DQ',
        'penalty' => 'Penalty',
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

    // What a related item shows once it's gone
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

    private const TITLES = [
        'COMPETITION_CREATED' => 'Competition created',
        'EVENT_CREATED' => 'Event added',
        'EVENT_DELETED' => 'Event deleted',
        'HEATS_GENERATED' => 'Heats generated',
        'HEATS_RESET' => 'Heats reset',
        'HEATS_SWAPPED' => 'Heats swapped',
        'HEAT_SWAP' => 'Heat lanes swapped',
        'HEAT_ADDED' => 'Added to a heat',
        'HEAT_MOVED' => 'Moved in heats',
        'HEAT_REMOVED' => 'Heat removed',
        'HEAT_UNASSIGNED' => 'Taken out of heats',
        'POOLED_HEATS_CLEARED' => 'Pool heats cleared',
        'DRAW_SWAP' => 'Draw places swapped',
        'DRAW_ENTRY_ADDED' => 'Added to the draw',
        'DRAW_ENTRY_MOVED' => 'Moved in the draw',
        'DRAW_ENTRY_REMOVED' => 'Taken out of the draw',
        'DRAW_TANK_ADDED' => 'Tank added',
        'DRAW_TANK_REMOVED' => 'Tank removed',
        'DRAW_TANKS_COMPACTED' => 'Tanks compacted',
        'DRAW_TANK_SETUP' => 'Tank setup saved',
        'SERC_MARKED' => 'SERC marked',
        'SERC_RESULT_UPDATED' => 'SERC result updated',
        'SERC_LEAGUE_OBJECTIVES_CHANGED' => 'League Objectives changed',
        'SERCS_RELEASED' => 'SERCs released',
        'SERCS_LOCKED' => 'SERCs locked',
        'SPEED_RESULT_MARKED' => 'Time entered',
        'SPEED_RESULT_UPDATED' => 'Time updated',
        'OOF_MARKED' => 'Places entered',
        'RESULTS_CONFIRMED' => 'Results confirmed',
        'DISQUALIFICATION_SUBMISSION' => 'DQ submitted',
        'DISQUALIFICATION_REJECTED' => 'DQ rejected',
        'DISQUALIFICATION_APPROVED' => 'DQ approved',
        'DISQUALIFICATION_APPLIED' => 'DQ given',
        'DISQUALIFICATION_APPEALED' => 'DQ appealed',
        'DISQUALIFICATION_REMOVED' => 'DQ removed',
        'PENALTY_SUBMISSION' => 'Penalty submitted',
        'PENALTY_REJECTED' => 'Penalty rejected',
        'PENALTY_APPROVED' => 'Penalty approved',
        'PENALTY_APPLIED' => 'Penalty given',
        'PENALTY_APPEALED' => 'Penalty appealed',
        'PENALTY_REMOVED' => 'Penalty removed',
        'VIOLATION_SUBMITTED' => 'Submission sent',
        'VIOLATION_ACCEPTED' => 'Submission accepted',
        'VIOLATION_REJECTED' => 'Submission rejected',
        'VIOLATION_APPEALED' => 'Submission appealed',
        'VIOLATION_REMOVED' => 'Submission removed',
    ];

    // Words a humanised type keeps in capitals
    private const ACRONYMS = ['serc' => 'SERC', 'sercs' => 'SERCs', 'oof' => 'OOF', 'dq' => 'DQ', 'dqs' => 'DQs'];

    // The tables of changes, for the types whose context has them
    private const CHANGE_COLUMNS = [
        'SERC_MARKED' => ['label' => 'Marking point', 'old' => 'Old mark', 'new' => 'New mark'],
        'SERC_RESULT_UPDATED' => ['label' => 'Marking point', 'old' => 'Old mark', 'new' => 'New mark'],
        'OOF_MARKED' => ['label' => 'Team', 'old' => 'Old place', 'new' => 'New place'],
    ];

    /** @var array<int, array{alias: string, class: class-string, id: ?string, raw: string}> */
    private array $related;

    /** @var string[] user ids, or 'system' */
    private array $by;

    /** @var string[] */
    private array $activity;

    private ?LengthAwarePaginator $page = null;

    /**
     * @param array<string, mixed> $query the request's query parameters
     * @param ?Competition $comp the competition everything is forced to, if any
     */
    public function __construct(private array $query, private ?Competition $comp = null)
    {
        $this->related = $this->resolveRelated($this->selected('related'));
        $this->by = array_values(array_filter($this->selected('by'), fn ($v) => $v === self::BY_SYSTEM || ctype_digit($v)));
        $this->activity = $this->selected('activity');
    }

    /**
     * A |-separated filter's values, as they are in the url, without repeats
     *
     * @return string[]
     */
    public function selected(string $param): array
    {
        $value = $this->query[$param] ?? '';

        // Each value once, so a hand-written url can't show the same chip twice
        return is_string($value) ? array_values(array_unique(array_filter(explode('|', $value), fn ($v) => $v !== ''))) : [];
    }

    /**
     * Each filter's values, for building urls from
     *
     * @return array{related: string[], by: string[], activity: string[]}
     */
    public function filters(): array
    {
        return ['related' => $this->selected('related'), 'by' => $this->selected('by'), 'activity' => $this->selected('activity')];
    }

    /**
     * A readable title for an Activity type, e.g. "SERC result updated"
     */
    public static function title(string $type): string
    {
        if (isset(self::TITLES[$type])) {
            return self::TITLES[$type];
        }

        $words = collect(explode('_', strtolower($type)))->filter()->map(fn ($word) => self::ACRONYMS[$word] ?? $word)->implode(' ');

        return $words === '' ? 'Activity' : Str::ucfirst($words);
    }

    /**
     * This page of Activity, newest first, with what the entries show loaded
     */
    public function paginate(): LengthAwarePaginator
    {
        if ($this->page) {
            return $this->page;
        }

        $query = Activity::with('user', 'relations.related');

        // The forced competition is its own group so it can never be ORed away
        $groups = [
            ...($this->comp ? [[['class' => Competition::class, 'id' => $this->comp->id]]] : []),
            ...collect($this->related)->groupBy(fn ($rel) => self::RELATED_KINDS[$rel['alias']])->map->all()->values()->all(),
        ];

        foreach ($groups as $group) {
            $query->whereHas('relations', function ($query) use ($group) {
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
            $query->where(function ($query) {
                $query->whereIn('user_id', array_diff($this->by, [self::BY_SYSTEM]));

                if (in_array(self::BY_SYSTEM, $this->by)) {
                    $query->orWhereNull('user_id');
                }
            });
        }

        if (count($this->activity) > 0) {
            $query->whereIn('activity', $this->activity);
        }

        $page = $this->query['page'] ?? 1;

        return $this->page = $query->latest()->paginate(self::PER_PAGE, page: is_numeric($page) ? (int) $page : 1);
    }

    /**
     * This page's entries as the Activity page shows them
     */
    public function entries(): array
    {
        return collect($this->paginate()->items())->map(fn (Activity $activity) => $this->entry($activity))->all();
    }

    /**
     * The next older page, or null on the last
     */
    public function olderPage(): ?int
    {
        $page = $this->paginate();

        return $page->hasMorePages() ? $page->currentPage() + 1 : null;
    }

    private function entry(Activity $activity): array
    {
        $columns = self::CHANGE_COLUMNS[$activity->activity] ?? null;
        $changes = $activity->context['changes'] ?? null;

        return [
            'id' => $activity->id,
            'at' => $activity->created_at->toIso8601String(),
            'type' => $activity->activity,
            'title' => self::title($activity->activity),
            'description' => $activity->description,
            // Null means the System
            'by' => $activity->user?->name,
            'related' => $activity->relations->map(fn (ActivityRelation $relation) => $this->relatedItem($relation))->filter()->values()->all(),
            'changes' => $columns && is_array($changes) ? [
                'columns' => $columns,
                'rows' => collect($changes)->map(fn ($change) => [
                    'label' => (string) ($change['name'] ?? ''),
                    'old' => isset($change['old']) ? (string) $change['old'] : null,
                    'new' => isset($change['new']) ? (string) $change['new'] : null,
                ])->values()->all(),
            ] : null,
        ];
    }

    /**
     * What an entry shows of something it's related to, with the filter it toggles and its own page.
     * Neither for something deleted, or for what the page already forces, like a competition's own page.
     * Null for a kind of thing the feed doesn't know.
     *
     * @return ?array{kind: string, name: string, detail: ?string, state: string, link: ?string, filter: ?array{value: string, active: bool}}
     */
    public function relatedItem(ActivityRelation $relation): ?array
    {
        $alias = array_search($relation->related_type, self::RELATED_ALIASES);

        if ($alias === false) {
            return null;
        }

        $related = $relation->related;
        $value = "{$alias}:{$relation->related_id}";
        $forced = $alias === 'comp' && $this->comp && $relation->related_id == $this->comp->id;
        $kind = $alias === 'violation' ? 'submission' : $alias;

        if ($related === null) {
            return ['kind' => $kind, 'name' => self::DELETED_NAMES[$alias], 'detail' => null, 'state' => 'deleted', 'link' => null, 'filter' => null];
        }

        [$name, $detail, $appealed] = match (true) {
            $related instanceof Competition => [$related->name, $related->getOrganisation?->name, false],
            $related instanceof Event => [$related->getName(), null, false],
            $related instanceof Entity => [$related->getName(), null, false],
            $related instanceof Violation => ["{$related}", $related->getMessage(), false],
            $related instanceof JudgeDQSubmission => ["{$related->name} ({$related->position})", $related->code, (bool) $related->appealed],
            $related instanceof ViolationSubmission => ["{$related->code()} for {$related->entity?->getName()}", null, $related->status === 'APPEALED'],
        };

        return [
            'kind' => $kind,
            'name' => $name,
            'detail' => $detail,
            'state' => $appealed ? 'appealed' : 'present',
            'link' => $forced ? null : $relation->link(),
            'filter' => $forced ? null : ['value' => $value, 'active' => in_array($value, $this->selected('related'))],
        ];
    }

    /**
     * Every filter as chips, grouped by kind, so it's clear why entries are missing. On a competition only
     * things from that competition are named, so ids can't be used to look up others.
     *
     * @return array<int, array{label: string, chips: array<int, array{label: string, param: string, value: string}>}>
     */
    public function chipGroups(): array
    {
        $types = collect($this->activity)->map(fn ($type) => ['label' => self::title($type), 'param' => 'activity', 'value' => $type]);

        $related = collect($this->related)->groupBy(fn ($rel) => self::RELATED_KINDS[$rel['alias']])->map(fn ($group) => $group->map(fn ($rel) => [
            'label' => $this->relatedName($rel),
            'param' => 'related',
            'value' => $rel['raw'],
        ]));

        $users = $this->people()->whereIn('id', array_diff($this->by, [self::BY_SYSTEM]))->pluck('name', 'id');
        $people = collect($this->by)->map(fn ($id) => [
            'label' => $id === self::BY_SYSTEM ? 'System' : ($users[$id] ?? "#{$id}"),
            'param' => 'by',
            'value' => $id,
        ]);

        $kinds = array_unique(self::RELATED_KINDS);

        return collect(['Type' => $types, ...collect($kinds)->mapWithKeys(fn ($kind) => [$kind => $related[$kind] ?? collect()]), 'Actioned by' => $people])
            ->filter(fn ($chips) => $chips->isNotEmpty())
            ->map(fn ($chips, $label) => ['label' => $label, 'chips' => $chips->values()->all()])
            ->values()->all();
    }

    private function relatedName(array $rel): string
    {
        if ($rel['id'] === null) {
            return self::RELATED_ALL_LABELS[$rel['alias']];
        }

        $model = $rel['class']::find($rel['id']);

        $modelCompId = match (true) {
            $model instanceof Competition => $model->id,
            $model instanceof ViolationSubmission => $model->competition_id,
            default => $model?->competition,
        };

        $inComp = $this->comp === null || $modelCompId == $this->comp->id;

        return match (true) {
            $model === null || !$inComp => "#{$rel['id']}",
            $model instanceof Competition => $model->name,
            $model instanceof ViolationSubmission => "{$model->code()} for {$model->entity?->getName()}",
            method_exists($model, 'getName') => $model->getName(),
            default => "#{$rel['id']}",
        };
    }

    /**
     * Activity types to filter by, readable: on a competition only those that happened on it
     *
     * @return array<int, array{value: string, label: string, active: bool}>
     */
    public function types(): array
    {
        $types = $this->comp
            ? $this->onComp(Activity::query())->select('activity')->distinct()->pluck('activity')
            : Activity::uniqueActivities();

        return $types->map(fn ($type) => ['value' => $type, 'label' => self::title($type), 'active' => in_array($type, $this->activity)])
            ->sortBy('label')->values()->all();
    }

    /**
     * The History shortcut: exactly the Mark, Time and Place types, active when they're what's selected
     *
     * @return array{value: string[], active: bool}
     */
    public function history(): array
    {
        $selected = array_unique($this->activity);
        sort($selected);
        $history = self::HISTORY;
        sort($history);

        return ['value' => self::HISTORY, 'active' => $selected === $history];
    }

    /**
     * Options for the "Add …" pickers on a competition's page, leaving out what's already picked. None
     * without a competition.
     *
     * @return array<int, array{label: string, param: string, placeholder: string, groups: array<int, array{label: ?string, options: array<int, array{value: string, label: string}>}>}>
     */
    public function pickers(): array
    {
        $comp = $this->comp;

        if (!$comp) {
            return [];
        }

        $picked = [...$this->selected('related'), ...$this->by, ...$this->activity];
        $option = fn ($value, $label) => ['value' => (string) $value, 'label' => $label];
        $unpicked = fn ($options) => collect($options)->reject(fn ($o) => in_array($o['value'], $picked))->values()->all();
        $group = fn (?string $label, $options) => ['label' => $label, 'options' => $unpicked($options)];

        $people = $this->people()->orderBy('name')->get()->map(fn ($user) => $option($user->id, $user->name));

        return [
            ['label' => 'Event', 'param' => 'related', 'placeholder' => 'Add event…', 'groups' => [
                $group(null, [$option('serc', 'All SERCs'), $option('speed', 'All speed events')]),
                $group('SERCs', $comp->getSERCs->map(fn ($serc) => $option("serc:{$serc->id}", $serc->getName()))),
                $group('Speed events', $comp->getSpeedEvents->map(fn ($speed) => $option("speed:{$speed->id}", $speed->getName()))),
            ]],
            ['label' => 'Team', 'param' => 'related', 'placeholder' => 'Add team…', 'groups' => [
                $group(null, $comp->getCompetitionTeams->map(fn ($team) => $option("team:{$team->id}", $team->getName($comp)))->sortBy('label')),
            ]],
            // Only offer a Competitor picker when the competition has any
            ...($comp->getCompetitors->isEmpty() ? [] : [['label' => 'Competitor', 'param' => 'related', 'placeholder' => 'Add competitor…', 'groups' => [
                $group(null, $comp->getCompetitors->map(fn ($competitor) => $option("competitor:{$competitor->id}", $competitor->getName($comp)))->sortBy('label')),
            ]]]),
            ['label' => 'Actioned by', 'param' => 'by', 'placeholder' => 'Add person…', 'groups' => [
                $group(null, [...$people, $option(self::BY_SYSTEM, 'System')]),
            ]],
            ['label' => 'Type', 'param' => 'activity', 'placeholder' => 'Add type…', 'groups' => [
                $group(null, collect($this->types())->map(fn ($type) => $option($type['value'], $type['label']))),
            ]],
        ];
    }

    /**
     * People who can be named: on a competition, only those who did something on it
     */
    private function people()
    {
        return $this->comp ? User::whereIn('id', $this->onComp(Activity::query())->select('user_id')) : User::query();
    }

    private function onComp($query)
    {
        return $query->whereHas('relations', fn ($q) => $q->where('related_type', Competition::class)->where('related_id', $this->comp->id));
    }

    /**
     * @return array<int, array{alias: string, class: class-string, id: ?string, raw: string}>
     */
    private function resolveRelated(array $related): array
    {
        return collect($related)->map(function ($raw) {
            $split = explode(':', $raw);
            $class = self::RELATED_ALIASES[$split[0]] ?? null;

            return $class ? ['alias' => $split[0], 'class' => $class, 'id' => count($split) > 1 && $split[1] !== '' ? $split[1] : null, 'raw' => $raw] : null;
        })->filter()->values()->all();
    }
}
