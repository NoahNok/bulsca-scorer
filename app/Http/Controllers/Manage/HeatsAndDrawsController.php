<?php

namespace App\Http\Controllers\Manage;

use App\Helpers\CompetitionHeader;
use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Orders\Heat;
use App\Models\Pool;
use App\Services\DrawService;
use App\Services\HeatService;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Heats & Draws' overview (Svelte, in CompetitionLayout): the competition's Heats, one event and Pool at a
 * time, and its Draw, with the actions for both and switches to show them publicly. Editing either is on
 * its own page (HeatsController, DrawsController).
 */
class HeatsAndDrawsController extends Controller
{
    public function index(Competition $comp, Request $request, HeatService $heatService, DrawService $drawService)
    {
        $events = $comp->heatEvents();
        $event = $request->integer('event') ? $events->firstWhere('id', $request->integer('event')) : $events->first();
        abort_if($request->integer('event') && !$event, 404);

        $pool = $heatService->pool($comp, $request->integer('pool') ?: null);
        $serc = $comp->drawSerc();
        $heatsGenerated = Heat::whereIn('speed_event', $comp->getSpeedEvents()->select('id'))->exists();
        $drawn = $serc && $serc->draw()->exists();

        return Inertia::render('Manage/Competition/HeatsAndDraws', [
            'competition' => CompetitionHeader::for($comp),
            'heats' => [
                'perEvent' => (bool) $comp->heats_per_event,
                'usePools' => (bool) $comp->use_pools,
                'events' => $events->map(fn ($option) => ['id' => $option->id, 'name' => $option->getName()])->values(),
                'event' => $event?->id,
                'pools' => $comp->use_pools ? $comp->getPools()->get()->map(fn (Pool $option) => ['id' => $option->id, 'name' => $option->name])->values() : [],
                'pool' => $pool?->id,
                'grid' => $event ? $heatService->grid($comp, $event, $pool) : null,
                'generated' => $heatsGenerated,
                // Pooled heats come from the pool-heats command
                'canGenerate' => $event && !$heatsGenerated && !$comp->use_pools,
            ],
            'draw' => [
                'hasSerc' => (bool) $serc,
                'useTanks' => (bool) $comp->getScoringSettings->use_tanks,
                'tanks' => $drawn ? $drawService->tanks($comp, $serc) : [],
                'generated' => $drawn,
            ],
            'show' => [
                'heats' => (bool) $comp->show_heats,
                'draws' => (bool) $comp->show_draws,
            ],
            'eventsUrl' => route('manage.competitions.events', $comp),
        ]);
    }

    /**
     * Shows the Heats or the Draw publicly, or hides them: whichever of `show_heats` and `show_draws` is sent
     */
    public function visibility(Competition $comp, Request $request)
    {
        $data = $request->validate([
            'show_heats' => ['sometimes', 'boolean'],
            'show_draws' => ['sometimes', 'boolean'],
        ]);

        $comp->forceFill($data)->save();

        return back();
    }
}
