<?php

namespace App\Http\Controllers\Manage;

use App\Exceptions\HeatException;
use App\Helpers\CompetitionHeader;
use App\Http\Controllers\Controller;
use App\Models\AbstractClasses\Entity;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\Orders\Heat;
use App\Models\Pool;
use App\Services\HeatService;
use App\Traits\RecordActivity;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

/**
 * The heat editor (Svelte, full width): an event's Heats in one Pool as a lane × Heat grid, and every edit
 * to them. Each edit saves at once, goes back to the editor and is recorded as Activity. With pools, every
 * edit names the Pool it's in and stays inside it, as heat numbers restart in each Pool.
 */
class HeatsController extends Controller
{
    use RecordActivity;

    const POOLED_HEATS_MESSAGE = 'Pooled heats are generated with the heats-and-draws:pool-heats command.';

    public function __construct(private HeatService $heats) {}

    public function edit(Competition $comp, CompetitionSpeedEvent $event, Request $request)
    {
        $events = $comp->heatEvents();

        // Without heats per event, every event shares the first one's Heats
        if (!$events->contains($event)) {
            return redirect()->route('manage.competitions.heats_and_draws.heats.edit', [$comp, $events->first(), ...$request->only('pool')]);
        }

        $pool = $this->heats->pool($comp, $request->integer('pool') ?: null);

        return Inertia::render('Manage/Competition/HeatEditor', [
            'competition' => CompetitionHeader::for($comp),
            'perEvent' => (bool) $comp->heats_per_event,
            'events' => $events->map(fn ($option) => ['id' => $option->id, 'name' => $option->getName()])->values(),
            'event' => ['id' => $event->id, 'name' => $event->getName()],
            'pools' => $comp->use_pools ? $comp->getPools()->get()->map(fn (Pool $option) => ['id' => $option->id, 'name' => $option->name])->values() : [],
            'pool' => $pool?->id,
            'grid' => $this->heats->grid($comp, $event, $pool),
            'unassigned' => $this->heats->unassigned($comp, $event),
            'useSeeds' => (bool) $comp->use_seeds,
            // Pooled heats come from the pool-heats command, so there's no layout to go back to
            'canReset' => !$comp->use_pools,
        ]);
    }

    public function generate(Competition $comp)
    {
        // Once there are Heats, laying them out again is each event's Reset, which confirms first
        if (Heat::whereIn('speed_event', $comp->getSpeedEvents()->select('id'))->exists()) {
            throw ValidationException::withMessages(['heats' => 'There are heats already. Reset an event\'s heats from its editor instead.']);
        }

        $this->layOut($comp);
        $this->recordActivity('HEATS_GENERATED', related: $comp);

        return back()->with('success', 'Heats generated');
    }

    public function reset(Competition $comp, CompetitionSpeedEvent $event)
    {
        $this->ensureHoldsHeats($comp, $event);
        $this->layOut($comp, $event);
        $this->recordActivity('HEATS_RESET', "Reset {$event->getName()}'s heats", related: $comp);

        return back()->with('success', 'Heats reset');
    }

    public function swap(Competition $comp, CompetitionSpeedEvent $event, Request $request)
    {
        $this->ensureHoldsHeats($comp, $event);
        $data = $request->validate([
            'from' => ['required', 'integer'],
            'to' => ['required', 'integer', 'different:from'],
        ]);
        $pool = $this->requestedPool($comp, $request);

        $first = $event->heats()->with('entity')->findOrFail($data['from']);
        $second = $event->heats()->with('entity')->findOrFail($data['to']);
        $this->ensureInPool($pool, $first, $second);

        $description = "Swapped {$this->place($comp, $pool, $first)} and {$this->place($comp, $pool, $second)}";
        $this->heats->swapLanes($first, $second);
        $this->changed($comp, 'HEAT_SWAP', $description, ['from' => $first->id, 'to' => $second->id], [$first->entity, $second->entity]);

        return back()->with('success', 'Lanes swapped');
    }

    public function swapHeats(Competition $comp, CompetitionSpeedEvent $event, Request $request)
    {
        $this->ensureHoldsHeats($comp, $event);
        $data = $request->validate([
            'first' => ['required', 'integer', 'min:1'],
            'second' => ['required', 'integer', 'min:1', 'different:first'],
        ]);
        $pool = $this->requestedPool($comp, $request);

        $this->heats->swapHeats($event, $pool, $data['first'], $data['second']);
        $this->changed($comp, 'HEATS_SWAPPED', "Swapped Heat {$data['first']} and Heat {$data['second']}{$this->in($pool)}", [...$data, 'pool' => $pool?->id]);

        return back()->with('success', 'Heats swapped');
    }

    public function assign(Competition $comp, CompetitionSpeedEvent $event, Request $request)
    {
        $this->ensureHoldsHeats($comp, $event);
        $data = $request->validate([
            'entity' => ['required', 'integer'],
            'heat' => ['required', 'integer', 'min:1'],
            'lane' => ['required', 'integer', 'min:1'],
        ]);
        $pool = $this->requestedPool($comp, $request);
        $entity = $this->entity($comp, $event, $data['entity']);

        $this->heats->assign($event, $pool, $entity, $data['heat'], $data['lane']);
        $this->changed(
            $comp,
            'HEAT_MOVED',
            "Moved {$entity->getName($comp)} to " . Pool::heatLabel($pool, $data['heat']) . ", Lane {$data['lane']}",
            [...$data, 'pool' => $pool?->id],
            [$entity],
        );

        return back()->with('success', 'Entry moved');
    }

    public function unassign(Competition $comp, CompetitionSpeedEvent $event, Request $request)
    {
        $this->ensureHoldsHeats($comp, $event);
        $id = $request->validate(['entity' => ['required', 'integer']])['entity'];
        $pool = $this->requestedPool($comp, $request);
        $entity = $this->entity($comp, $event, $id);

        $lane = $event->heats()->whereMorphedTo('entity', $entity)->inPool($pool?->id)->with('entity')->first();
        if (!$lane) {
            throw ValidationException::withMessages(['heats' => "{$entity->getName($comp)} isn't in these heats."]);
        }

        $description = "Unassigned {$entity->getName($comp)} from " . Pool::heatLabel($pool, $lane->heat) . ", Lane {$lane->lane}";
        $lane->delete();
        $this->changed($comp, 'HEAT_UNASSIGNED', $description, ['heat' => $lane->heat, 'lane' => $lane->lane, 'pool' => $pool?->id], [$entity]);

        return back()->with('success', 'Entry unassigned');
    }

    public function insertHeat(Competition $comp, CompetitionSpeedEvent $event, Request $request)
    {
        $this->ensureHoldsHeats($comp, $event);
        $position = (int) $request->validate(['position' => ['required', 'integer', 'min:1']])['position'];
        $pool = $this->requestedPool($comp, $request);

        $this->heats->insertHeat($event, $pool, $position);
        $this->changed($comp, 'HEAT_ADDED', "Added Heat {$position}{$this->in($pool)}", ['heat' => $position, 'pool' => $pool?->id]);

        return back()->with('success', 'Heat added');
    }

    public function deleteHeat(Competition $comp, CompetitionSpeedEvent $event, Request $request)
    {
        $this->ensureHoldsHeats($comp, $event);
        $heat = (int) $request->validate(['heat' => ['required', 'integer', 'min:1']])['heat'];
        $pool = $this->requestedPool($comp, $request);

        $unassigned = $this->heats->deleteHeat($event, $pool, $heat);
        $this->changed(
            $comp,
            'HEAT_REMOVED',
            "Removed Heat {$heat}{$this->in($pool)} and unassigned {$unassigned} entr" . ($unassigned === 1 ? 'y' : 'ies'),
            ['heat' => $heat, 'pool' => $pool?->id, 'entries_unassigned' => $unassigned],
        );

        return back()->with('success', 'Heat removed');
    }

    /**
     * Generates the Heats (or one event's, to reset them), saying why when it can't
     */
    private function layOut(Competition $comp, ?CompetitionSpeedEvent $event = null): void
    {
        if ($comp->use_pools) {
            throw ValidationException::withMessages(['heats' => self::POOLED_HEATS_MESSAGE]);
        }

        try {
            $this->heats->generate($comp, $event);
        } catch (HeatException) {
            throw ValidationException::withMessages(['heats' => 'Every entry needs a seed time before heats can be generated.']);
        }
    }

    /**
     * Clears the cached Heats and records the edit as Activity
     *
     * @param Entity[] $entities
     */
    private function changed(Competition $comp, string $activity, string $description, array $context, array $entities = []): void
    {
        $comp->clearHeatCache();
        $this->recordActivity($activity, $description, context: $context, related: [$comp, ...array_filter($entities)]);
    }

    /**
     * The Pool an edit is in: required when the competition uses pools, and one of its own
     */
    private function requestedPool(Competition $comp, Request $request): ?Pool
    {
        if (!$comp->use_pools) {
            return null;
        }

        return $this->heats->pool($comp, $request->validate(['pool' => ['required', 'integer']])['pool']);
    }

    private function ensureInPool(?Pool $pool, Heat ...$lanes): void
    {
        foreach ($lanes as $lane) {
            if ($lane->pool != $pool?->id) {
                throw ValidationException::withMessages(['heats' => 'Entries can only move within their pool.']);
            }
        }
    }

    /**
     * Without heats per event only the first event holds Heats, so there's nothing to edit on the others
     */
    private function ensureHoldsHeats(Competition $comp, CompetitionSpeedEvent $event): void
    {
        abort_unless($comp->heatEvents()->contains($event), 404);
    }

    private function entity(Competition $comp, CompetitionSpeedEvent $event, int $id): Entity
    {
        return $event->getScorableEntity()::where('competition', $comp->id)->findOrFail($id);
    }

    /**
     * "Team A (Heat 2, Lane 3)", for Activity
     */
    private function place(Competition $comp, ?Pool $pool, Heat $lane): string
    {
        return "{$lane->entity?->getName($comp)} (" . Pool::heatLabel($pool, $lane->heat) . ", Lane {$lane->lane})";
    }

    private function in(?Pool $pool): string
    {
        return $pool ? " in {$pool->name}" : '';
    }
}
