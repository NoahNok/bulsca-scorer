<?php

namespace App\Http\Controllers\Orders;

use App\Exceptions\HeatException;
use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\Pool;
use App\Services\HeatService;
use App\Traits\RecordActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Nette\NotImplementedException;

class HeatController extends Controller
{
    use RecordActivity;

    const POOLED_HEATS_MESSAGE = 'Pooled heats are generated with the heats-and-draws:pool-heats command.';

    public function index(Competition $comp)
    {


        return view('competition.heats-and-orders.index', ['comp' => $comp]);
    }

    public function generate(Competition $comp, HeatService $heatService)
    {
        if ($comp->use_pools) {
            return redirect()->back()->with('alert-error', self::POOLED_HEATS_MESSAGE);
        }

        try {
            if ($comp->heats_per_event) {

                $events = $comp->getSpeedEvents;

                foreach ($events as $event) {
                    $heatService->generateHeatsForEvent($event);
                }
            } else {
                $heatService->generateHeatsForEvent($comp->getSpeedEvents()->orderBy('id')->first());
            }
            $comp->clearHeatCache();

            $this->recordActivity('HEATS_GENERATED', related: $comp);

            return redirect()->back()->with('success', 'Heats Generated');
        } catch (HeatException $s) {
            return redirect()->back()->with('alert-error', 'Failed to generate: an entry is missing a seed time');
        }
    }

    public function hide(Competition $comp)
    {
        $comp->show_heats = !$comp->show_heats;
        $comp->save();

        return redirect()->back();
    }

    public function edit(Competition $comp, CompetitionSpeedEvent $event, Request $request)
    {
        $this->ensureEventBelongsToCompetition($comp, $event);

        $pools = $comp->use_pools ? $comp->getPools()->get() : collect();
        $pool = $request->integer('pool') ? $pools->firstWhere('id', $request->integer('pool')) : $pools->first();

        abort_if($request->integer('pool') && !$pool, 404);

        return view('competition.heats-and-orders.heats.edit', ['comp' => $comp, 'event' => $event, 'pools' => $pools, 'pool' => $pool]);
    }

    public function swap(Competition $comp, CompetitionSpeedEvent $event, Request $request)
    {
        $this->ensureEventBelongsToCompetition($comp, $event);

        $pool = $this->requestedPool($comp, $request);

        $currentAllocation = $event->heats()->findOrFail($request->input('team'));
        $targetAllocation = $event->heats()->find($request->input('target-heat'));

        // Heat numbers restart per pool, so entities can only move within their own pool
        abort_if($currentAllocation->pool != $pool?->id || ($targetAllocation && $targetAllocation->pool != $pool?->id), 422, 'Entities can only be moved within their pool.');

        $activityDescription = $targetAllocation
            ? "Swapped heats: " . $currentAllocation->entity->getName($comp) . " (" . Pool::heatLabel($pool, $currentAllocation->heat) . ", Lane {$currentAllocation->lane}) <-> " . $targetAllocation->entity->getName($comp) . " (" . Pool::heatLabel($pool, $targetAllocation->heat) . ", Lane {$targetAllocation->lane})"
            : null;

        DB::transaction(function () use ($currentAllocation, $targetAllocation, $request) {
            // If target is a blank space
            if ($targetAllocation == null) {
                $heatLaneSplit = explode(':', $request->input('target-heatlane'));
                $currentAllocation->heat = $heatLaneSplit[0];
                $currentAllocation->lane = $heatLaneSplit[1];
                $currentAllocation->save();

                return;
            }

            $currentHeat = $currentAllocation->heat;
            $currentLane = $currentAllocation->lane;

            $currentAllocation->heat = -1;
            $currentAllocation->lane = -1;
            $currentAllocation->save();

            $currentAllocation->heat = $targetAllocation->heat;
            $currentAllocation->lane = $targetAllocation->lane;

            $targetAllocation->heat = $currentHeat;
            $targetAllocation->lane = $currentLane;

            $currentAllocation->save();
            $targetAllocation->save();
        });

        $comp->clearHeatCache();

        if ($targetAllocation == null) {
            $this->recordActivity('HEAT_SWAP', "Moved " . $currentAllocation->entity->getName($comp) . " to " . Pool::heatLabel($pool, $currentAllocation->heat) . ", Lane {$currentAllocation->lane}", context: ['current_allocation' => $currentAllocation->id], related: [$comp, $currentAllocation->entity]);
        } else {
            $this->recordActivity('HEAT_SWAP', $activityDescription, context: ['current_allocation' => $currentAllocation->id, 'target_allocation' => $targetAllocation->id], related: [$comp, $currentAllocation->entity, $targetAllocation->entity]);
        }

        return redirect()->back();
    }

    public function swapHeats(Competition $comp, CompetitionSpeedEvent $event, Request $request)
    {
        $this->ensureEventBelongsToCompetition($comp, $event);

        $first = $request->input('first');
        $second = $request->input('second');
        $pool = $this->requestedPool($comp, $request);

        $firstLanes = $event->heats()->where('heat', $first)->inPool($pool?->id)->get();
        $secondLanes = $event->heats()->where('heat', $second)->inPool($pool?->id)->get();

        DB::transaction(function () use ($firstLanes, $secondLanes, $first, $second) {
            $firstLanes->each(function ($lane) {
                $lane->heat = -3;
                $lane->save();
            });

            $secondLanes->each(function ($lane) use ($first) {
                $lane->heat = $first;
                $lane->save();
            });

            $firstLanes->each(function ($lane) use ($second) {
                $lane->heat = $second;
                $lane->save();
            });
        });

        $comp->clearHeatCache();

        $where = $pool ? " in {$pool->name}" : '';
        $this->recordActivity('HEATS_SWAPPED', "Swapped heats $first and $second$where", context: ['first' => $first, 'second' => $second, 'pool' => $pool?->name], related: $comp);

        return response()->json(['result' => 'ok']);
    }

    public function reset(Competition $comp, CompetitionSpeedEvent $event, HeatService $heatService)
    {

        $this->ensureEventBelongsToCompetition($comp, $event);

        if ($comp->use_pools) {
            return redirect()->back()->with('alert-error', self::POOLED_HEATS_MESSAGE);
        }

        try {
            $heatService->generateHeatsForEvent($event);

            $comp->clearHeatCache();

            $this->recordActivity('HEATS_RESET', related: $comp);

            return redirect()->back()->with('success', 'Heats Reset');
        } catch (HeatException $e) {
            return redirect()->back()->with('alert-error', 'Failed to generate: an entry is missing a seed time');
        }
    }

    public function deleteHeat(Competition $comp, CompetitionSpeedEvent $event, Request $request)
    {
        $this->ensureEventBelongsToCompetition($comp, $event);

        $heat = (int) $request->validate(['heat' => ['required', 'integer', 'min:1']])['heat'];
        $pool = $this->requestedPool($comp, $request);

        DB::transaction(function () use ($event, $heat, $pool) {
            $event->heats()->where('heat', $heat)->inPool($pool?->id)->delete();
            $event->heats()->where('heat', '>', $heat)->inPool($pool?->id)->decrement('heat');
        });

        $comp->clearHeatCache();

        return response()->json(['result' => 'ok']);
    }

    public function assign(Competition $comp, CompetitionSpeedEvent $event, Request $request)
    {
        $this->ensureEventBelongsToCompetition($comp, $event);

        $data = $request->validate([
            'entity' => ['required', 'integer'],
            'heat' => ['required', 'integer', 'min:1'],
            'lane' => ['required', 'integer', 'min:1'],
        ]);

        $pool = $this->requestedPool($comp, $request);

        $entityClass = $event->getScorableEntity();
        $entity = $entityClass::where('competition', $comp->id)->findOrFail($data['entity']);

        if ($event->heats()->where('heat', $data['heat'])->where('lane', $data['lane'])->inPool($pool?->id)->exists()) {
            return response()->json(['message' => 'That lane is already occupied.'], 422);
        }

        $event->heats()->whereMorphedTo('entity', $entity)->delete();
        $allocation = $event->heats()->create([
            'entity_id' => $entity->id,
            'entity_type' => $entity->getMorphClass(),
            'heat' => $data['heat'],
            'lane' => $data['lane'],
            'pool' => $pool?->id,
        ]);

        $comp->clearHeatCache();

        return response()->json(['result' => 'ok', 'allocation_id' => $allocation->id]);
    }

    public function insertHeat(Competition $comp, CompetitionSpeedEvent $event, Request $request)
    {
        $this->ensureEventBelongsToCompetition($comp, $event);

        $position = (int) $request->validate([
            'position' => ['required', 'integer', 'min:1'],
        ])['position'];

        $pool = $this->requestedPool($comp, $request);

        $event->heats()->where('heat', '>=', $position)->inPool($pool?->id)->increment('heat');
        $comp->clearHeatCache();

        return response()->json(['result' => 'ok']);
    }

    public function unassign(Competition $comp, CompetitionSpeedEvent $event, Request $request)
    {
        $this->ensureEventBelongsToCompetition($comp, $event);

        $entityId = $request->validate([
            'entity' => ['required', 'integer'],
        ])['entity'];

        $entityClass = $event->getScorableEntity();
        $entity = $entityClass::where('competition', $comp->id)->findOrFail($entityId);

        $event->heats()->whereMorphedTo('entity', $entity)->delete();
        $comp->clearHeatCache();

        return response()->json(['result' => 'ok']);
    }

    /**
     * The pool being edited. Required when the competition uses pools, and must be one of its own.
     */
    private function requestedPool(Competition $comp, Request $request): ?Pool
    {
        if (!$comp->use_pools) {
            return null;
        }

        $id = $request->validate(['pool' => ['required', 'integer']])['pool'];

        return $comp->getPools()->findOrFail($id);
    }

    private function ensureEventBelongsToCompetition(Competition $comp, CompetitionSpeedEvent $event): void
    {
        abort_unless((int) $event->competition === (int) $comp->id, 404);
    }
}
