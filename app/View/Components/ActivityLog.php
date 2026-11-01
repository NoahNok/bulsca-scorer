<?php

namespace App\View\Components;

use App\Models\Activity\ActivityRelation;
use App\Services\ActivityFeed;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\View\Component;

/**
 * The Admin Activity Monitor's list (Blade until Admin is redesigned): ActivityFeed with every filter
 * as a link. A competition's own Activity is the Svelte page Manage/Competition/Activity.
 */
class ActivityLog extends Component
{
    /**
     * The current URL with a |-separated filter changed, back on the first page
     */
    private function urlWithValues(string $param, array $values): string
    {
        $query = array_filter([...request()->query(), $param => implode('|', $values), 'page' => null], fn ($v) => $v !== null && $v !== '');

        return request()->url() . (count($query) > 0 ? '?' . Arr::query($query) : '');
    }

    /**
     * Adds the value if it's not in the filter, removes it if it is
     */
    private function urlToggling(ActivityFeed $feed, string $param, string $value): string
    {
        $values = $feed->selected($param);

        return $this->urlWithValues($param, in_array($value, $values) ? array_diff($values, [$value]) : [...$values, $value]);
    }

    public function render(): View|Closure|string
    {
        $feed = new ActivityFeed(request()->query());

        $activities = $feed->paginate()->withQueryString();
        $entries = collect($feed->entries())->keyBy('id');

        $types = collect($feed->types())->map(fn ($type) => [...$type, 'url' => $this->urlToggling($feed, 'activity', $type['value'])]);

        $chipGroups = collect($feed->chipGroups())->map(fn ($group) => [
            'label' => $group['label'],
            'chips' => collect($group['chips'])->map(fn ($chip) => [
                'label' => $chip['label'],
                'remove_url' => $this->urlToggling($feed, $chip['param'], $chip['value']),
            ])->all(),
        ])->all();

        $clearUrl = request()->url();

        $footerLinks = function (ActivityRelation $relation) use ($feed) {
            $item = $feed->relatedItem($relation);

            return [
                'filter' => $item && $item['filter'] ? [
                    'url' => $this->urlToggling($feed, 'related', $item['filter']['value']),
                    'active' => $item['filter']['active'],
                ] : null,
                'link' => $item['link'] ?? null,
            ];
        };

        return view('components.activity-log', compact('activities', 'entries', 'types', 'chipGroups', 'clearUrl', 'footerLinks'));
    }
}
