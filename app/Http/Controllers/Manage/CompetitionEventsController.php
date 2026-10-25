<?php

namespace App\Http\Controllers\Manage;

use App\Helpers\CompetitionHeader;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddSpeedEventRequest;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\SERC;
use App\Models\SpeedEvent;
use App\Models\SpeedResult;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

/**
 * A competition's Events page (Svelte, in CompetitionLayout): its speed events and SERCs as cards, by the
 * access you have. A speed event is added from a dialog here; a SERC from its own setup page.
 */
class CompetitionEventsController extends Controller
{
    public function index(Competition $comp)
    {
        $can = fn (string $access) => Gate::allows('access', [$comp, $access]);

        // What every card shows, whatever the event
        $card = fn (CompetitionSpeedEvent|SERC $event) => [
            'id' => $event->id,
            'name' => $event->getName(),
            'complete' => $event->isComplete(true),
            'confirmed' => $event->digitalJudgeEnabled && $event->digitalJudgeConfirmed,
            'hidden' => !$event->viewable,
        ];

        return Inertia::render('Manage/Competition/Events', [
            'competition' => CompetitionHeader::for($comp),
            // Null without access to that kind of event
            'speeds' => $can('speed') ? $comp->getSpeedEvents->map(fn (CompetitionSpeedEvent $event) => $card($event) + [
                'target' => $event->scorable_entity,
                'url' => route('manage.competitions.events.speeds.show', [$comp, $event]),
            ])->values() : null,
            'sercs' => $can('serc|serc_writer') ? $comp->getSERCs->map(fn (SERC $serc) => $card($serc) + [
                'type' => $serc->type,
                'url' => route('manage.competitions.events.sercs.show', [$comp, $serc]),
            ])->values() : null,
            'speedOptions' => $can('speed') ? SpeedEvent::orderBy('name')->get(['id', 'name']) : [],
            'newSercUrl' => route('manage.competitions.events.sercs.create', $comp),
        ]);
    }

    /**
     * Adds a speed event, with an empty result for every Entity it scores, and opens it
     */
    public function storeSpeed(Competition $comp, AddSpeedEventRequest $request)
    {
        $data = $request->validated();
        $base = SpeedEvent::findOrFail($data['event']);

        $event = new CompetitionSpeedEvent();
        $event->event = $base->id;
        $event->competition = $comp->id;
        $event->scorable_entity = $data['target_entity'];
        $event->record = $base->record;
        $event->weight = 1;
        $event->save();

        foreach ($event->getScorableEntities() as $entity) {
            $result = new SpeedResult();
            $result->entity()->associate($entity);
            $result->event = $event->id;
            $result->save();
        }

        return redirect()->route('manage.competitions.events.speeds.show', [$comp, $event])->with('success', "{$base->name} added");
    }
}
