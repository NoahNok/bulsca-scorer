<?php

namespace App\Http\Controllers\Manage\Concerns;

use App\DTO\Result;
use App\Models\AbstractClasses\Event;
use App\Models\Competition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * What a speed event's and a SERC's pages do alike. Each controller keeps its own route actions, since
 * route model binding needs the event's own class, and calls these.
 */
trait ManagesEventPages
{
    /**
     * Shows the event in results, or leaves it out
     */
    protected function setVisibility(Event $event, Request $request): RedirectResponse
    {
        $event->viewable = $request->validate(['viewable' => ['required', 'boolean']])['viewable'];
        $event->save();

        return back()->with('success', $event->viewable ? 'Shown in results' : 'Hidden from results');
    }

    /**
     * Deletes the event once its name is typed out, back to the Events list. `$noun` names it in the
     * error ("event", "SERC").
     */
    protected function destroyNamed(Competition $comp, Event $event, Request $request, string $noun): RedirectResponse
    {
        $name = $event->getName();

        if ($request->validate(['name' => ['required', 'string']])['name'] != $name) {
            throw ValidationException::withMessages(['name' => "That isn't the {$noun}'s name."]);
        }

        $event->delete();

        return redirect()->route('manage.competitions.events', $comp)->with('success', "{$name} deleted");
    }

    /**
     * The Entities a ranked result stands for: just its own, or each one in a combined result
     *
     * @return Collection<int, Result>
     */
    protected function partsOf(Result $result): Collection
    {
        return $result->isCombined() ? $result->combined : collect([$result]);
    }

    /**
     * A ranked row's key (`type:id`, joined by commas for a combined result) and each Entity's name
     */
    protected function keyAndNames(Competition $comp, Result $result): array
    {
        $parts = $this->partsOf($result);

        return [
            'key' => $parts->map(fn (Result $part) => $part->entity->getMorphClass() . ':' . $part->entity->id)->implode(','),
            'names' => $parts->map(fn (Result $part) => $part->entity->getName($comp))->values(),
        ];
    }
}
