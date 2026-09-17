<?php

namespace App\Http\Controllers\DigitalJudge\Violation;

use App\Http\Controllers\Controller;
use App\Http\Requests\DigitalJudge\Violation\SubmitViolationRequest;
use App\Models\Club;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\Competitor;
use App\Models\DigitalJudge\Violation\ViolationSubmission;
use App\Models\DQCode;
use App\Models\Event\Disqualification;
use App\Models\Event\Penalty;
use App\Models\PenaltyCode;
use App\Models\SERC;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ViolationController extends Controller
{
    public function submissions(Competition $competition, Request $request)
    {

        $submissions = ViolationSubmission::where('competition_id', $competition->id)->where('submitter_id', $request->user()->id)->orderBy('created_at', 'desc')->get();


        return Inertia::render('Judge/Competition/Violation/Submissions', [
            'competition' => $competition,
            'submissions' => $submissions->map(function ($submission) {
                return $submission->jsonable();
            })
        ]);
    }

    public function issue(Competition $competition)
    {
        return Inertia::render('Judge/Competition/Violation/Issue', [
            'competition' => $competition->only(['id', 'name']),
            'sercs' => $competition->getSERCs()->where('digitalJudgeEnabled', true)->get()->map(function ($serc) {
                return $serc->jsonable();
            }),
            'speeds' => $competition->getSpeedEvents()->where('digitalJudgeEnabled', true)->get()->map(function ($speed) {
                return $speed->jsonable();
            })
        ]);
    }

    public function submit(Competition $competition, SubmitViolationRequest $request)
    {
        $validated = $request->validated();

        $eventId = $validated['event']['id'];
        $eventType = $validated['event']['type'];

        $event = match ($eventType) {
            'speed' => CompetitionSpeedEvent::findOrFail($eventId),
            default => SERC::findOrFail($eventId),
        };

        $entity = $event->getScorableEntity()::find($validated['entity_id']);

        if (!$entity) {
            abort(422, 'Unknown entity for violation submission.');
        }

        $submittedViolation = match ($validated['violation']['vtype']) {
            'DQ' => DQCode::findOrFail($validated['violation']['id']),
            'PEN' => PenaltyCode::findOrFail($validated['violation']['id']),
            default => abort(422, 'Unknown violation code (DQCode/PenaltyCode).'),
        };

        $submission = new ViolationSubmission();
        $submission->competition_id = $competition->id;
        $submission->details = $validated['details']['details'] ?? '';
        $submission->turn = $validated['details']['turn'] ?? null;
        $submission->length = $validated['details']['length'] ?? null;
        $submission->submitter_id = $request->user()?->id;
        $submission->submitter_position = $validated['submitter']['position'] ?? '';
        $submission->seconder_name = $validated['seconder']['name'] ?? null;
        $submission->seconder_position = $validated['seconder']['position'] ?? null;
        $submission->status = 'SUBMITTED';

        $submission->entity()->associate($entity);
        $submission->event()->associate($event);
        $submission->submitted()->associate($submittedViolation);

        $submission->save();

        return to_route('judge.competition.violation.submissions', ['competition' => $competition, 'id' => $submission->id]);
    }

    public function view(Competition $competition, ViolationSubmission $submission)
    {
        return Inertia::render("Judge/Competition/Violation/Submission", ['competition' => $competition->jsonable(), 'submission' => $submission->jsonable()]);
    }

    // Issue Context Handlers - heats/draws/codes/etc
    public function getHeatsFor(Competition $competition, CompetitionSpeedEvent $event)
    {
        return $event->getHeats()->orderBy('heat')->orderBy('lane')->get()->groupBy('heat')->map(function ($lanes, $heat) {
            return [
                'heat' => $heat,
                'lanes' => $lanes->map(function ($lane) {
                    return [
                        'lane' => $lane->lane,
                        'entity' => $lane->entity->jsonable()
                    ];
                })
            ];
        })->values();
    }

    public function getDrawFor(Competition $competition, SERC $serc)
    {
        return $serc->getTankDraw(true);
    }

    public function getEventRelatedCodes(Competition $competition, string $eventName)
    {
        if (str_starts_with($eventName, 'sp')) {

            $event = CompetitionSpeedEvent::find(substr($eventName, 3));

            $eventName = $event->getBaseEvent()->name;
        } else {

            $eventName = 'SERC';
        }

        $organisation = $competition->getOrganisation;

        if (!$organisation) {
            return response()->json(['related' => [], 'other' => []]);
        }

        $rawDqs = $organisation->disqualificationCodes()->with('eventCodes')->get();
        $rawPens = $organisation->penaltyCodes()->with('eventCodes')->get();



        $dqs = [];
        $pens = [];




        foreach ($rawDqs as $dq) {
            $eventCode = $dq->eventCodes->where('event', $eventName)->first();

            $dqs[] = [
                'id' => $dq->id,
                'code' => $dq->code,
                'description' => $dq->description,
                'type' => $eventCode?->type,
                'vtype' => 'DQ'
            ];
        }

        foreach ($rawPens as $pen) {
            $eventCode = $pen->eventCodes->where('event', $eventName)->first();

            $pens[] = [
                'id' => $pen->id,
                'code' => $pen->code,
                'description' => $pen->description,
                'type' => $eventCode?->type,
                'vtype' => "PEN"
            ];
        }

        return response()->json(compact('dqs', 'pens'));
    }
}
