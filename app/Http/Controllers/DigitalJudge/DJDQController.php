<?php

namespace App\Http\Controllers\DigitalJudge;

use App\DigitalJudge\DigitalJudge;
use App\Http\Controllers\Controller;
use App\Http\Requests\DigitalJudge\DQRequest;
use App\Http\Requests\DigitalJudge\JudgeDQSubmissionRequest;
use App\Models\AbstractClasses\Entity;
use App\Models\AbstractClasses\Event;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\DigitalJudge\JudgeDQSubmission;
use App\Models\DQCode;
use App\Models\Organisation\Organisation;
use App\Models\Penalty;
use App\Models\PenaltyCode;
use App\Models\SERC;
use App\Models\SERCDisqualification;
use App\Models\SERCPenalty;
use App\Models\SpeedEvent;
use App\Models\SpeedResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class DJDQController extends Controller
{
    public function index()
    {
        return view('digitaljudge.dq.index', ['comp' => DigitalJudge::getClientCompetition()]);
    }

    public function submit(DQRequest $dQRequest)
    {
        $dQRequest->validated();

        $event = $dQRequest->input('event');



        if (str_starts_with($event, 'sp')) {
            // SPEED EVENT
            $eventId = substr($event, 3);




            if ($dQRequest->input('type') == 'dq') {
                // DQ

                $sr = SpeedResult::where('competition_team', $dQRequest->input('team'))->where('event', $eventId)->first();

                $sr->disqualification = $dQRequest->input('code');
                $sr->save();
            } else {
                // Penalty
                $penaltiesSplit = explode(",", $dQRequest->input('code'));


                $valid = [];
                foreach ($penaltiesSplit as $penalty) {
                    $penalty = trim($penalty);
                    if (preg_match("/^P[0-9]{3}$/", $penalty) == 0) {
                        break;
                    }
                    array_push($valid, $penalty);
                }

                $sr = SpeedResult::where('competition_team', $dQRequest->input('team'))->where('event', $eventId)->first();

                Penalty::where('speed_result', $sr->id)->delete();

                foreach ($valid as $penalty) {
                    $p = new Penalty();
                    $p->speed_result = $sr->id;
                    $p->code = $penalty;
                    $p->save();
                }
            }
        } else {
            $eventId = substr($event, 3);
            // SERC EVENT



            if ($dQRequest->input('type') == 'dq') {

                if ($dQRequest->input('code') == null) {
                    SERCDisqualification::where(['team' => $dQRequest->input('team'), 'serc' => $eventId])->delete();
                } else {
                    $sd = SERCDisqualification::firstOrNew(['team' => $dQRequest->input('team'), 'serc' => $eventId]);
                    $sd->code = $dQRequest->input('code');
                    $sd->save();
                }
            } else {

                $sd = SERCPenalty::firstOrNew(['team' => $dQRequest->input('team'), 'serc' => $eventId]);

                $sd->codes = $dQRequest->input('code') ?: "";

                $sd->save();
            }
        }


        return redirect()->back()->with('success', 'Disqualification submitted');
    }

    public function current(string $event, int $team, string $type)
    {

        $eventId = substr($event, 3);
        if (str_starts_with($event, 'sp')) {
            // SPEED EVENT


            if ($type == 'dq') {

                return response()->json(['current' => SpeedResult::where('competition_team', $team)->where('event', $eventId)->first()->disqualification]);
            } else {
                $pen = SpeedResult::where('competition_team', $team)->where('event', $eventId)->first()->getPenaltiesAsString();
                return response()->json(['current' =>  $pen == "" ? null : $pen]);
            }


            $eventId = substr($event, 3);

            $team = CompetitionTeam::find($team);
        } else {
            // SERC EVENT
            if ($type == 'dq') {
                $sd = SERCDisqualification::firstOrNew(['team' => $team, 'serc' => $eventId]);
                return response()->json(['current' => $sd->code]);
            } else {
                $sd = SERCPenalty::firstOrNew(['team' => $team, 'serc' => $eventId]);
                return response()->json(['current' => $sd->codes]);
            }
        }
    }


    ######################### JUDGE DQ REQUESTS #########################
    public function issue()
    {
        DigitalJudge::setStatus('DQ/Pen | Issuing');
        return view('digitaljudge.dq.judge-issue', ['comp' => DigitalJudge::getClientCompetition(), 'judge_name' => DigitalJudge::getClientName()]);
    }

    public function resolveCode(string $code)
    {

        $organisation = DigitalJudge::getClientCompetition()->getOrganisation;

        if (str_starts_with($code, 'p')) {
            $code = substr($code, 1);

            return response()->json(['description' => PenaltyCode::message($code, $organisation) ?? "Penalty code not found", 'type' => 'penalty']);
        } else {
            $code = substr($code, 2);

            return response()->json(['description' => DQCode::message($code, $organisation) ?? "DQ code not found"]);
        }
    }

    public function submission(JudgeDQSubmissionRequest $request)
    {

        $validated = $request->validated();

        $event = null;
        $eventId = substr($validated['event'], 3);
        if (str_starts_with($validated['event'], 'sp')) {
            $event = CompetitionSpeedEvent::find($eventId);
        } else {
            $event = SERC::find($eventId);
        }

        $submission = new JudgeDQSubmission();
        $submission->competition = DigitalJudge::getClientCompetition()->id;
        $submission->getEvent()->associate($event);
        $submission->heat_lane = $validated['heat_lane'];
        $submission->turn = $validated['turn'];
        $submission->length = $validated['length'];
        $submission->code = $validated['code'];
        $submission->details = $validated['details'];
        $submission->name = $validated['name'];
        $submission->position = $validated['position'];
        $submission->seconder_name = $validated['seconder_name'];
        $submission->seconder_position = $validated['seconder_position'];
        $submission->save();


        $activeSubmissions = Session::get('activeSubmissions', []);
        array_push($activeSubmissions, $submission->id);
        Session::put('activeSubmissions', $activeSubmissions);

        $activity_type = str_starts_with($submission->code, 'P') ? 'PENALTY_SUBMISSION' : 'DISQUALIFICATION_SUBMISSION';
        $entity = $submission->getHeat?->entity;
        $event = $submission->getEvent;
        $submission->recordActivity($activity_type, "{$submission->name} ({$submission->position}) submitted a {$submission->code} for {$entity->getName()} in {$event->getName()}", related: [$entity, $event, $event->getCompetition, $submission], context: ['code' => $submission->code]);


        DigitalJudge::setStatus('DQ/Pen | ' . $submission->code . ' for ' . $entity->getName() . ' in ' . $event->getName());

        return response()->json(['success' => true, 'result' => $submission->id]);
    }

    public function submissionStatus(JudgeDQSubmission $submission)
    {
        return response()->json(['success' => true, 'result' => $submission->resolved]);
    }

    public function getSubmission(JudgeDQSubmission $submission)
    {
        return response()->json(['success' => true, 'result' => $submission->only('id', 'event_type', 'event_id', 'heat_lane', 'turn', 'length', 'code', 'details', 'name', 'position', 'seconder_name', 'seconder_position', 'resolved')]);
    }

    public function resolve()
    {

        DigitalJudge::setStatus('DQ/Pen | Resolving submissions');

        return view('digitaljudge.dq.head-resolve', ['comp' => DigitalJudge::getClientCompetition()]);
    }

    public function resolveSubmission(int $submission, Request $request)
    {

        // Prevent race resulting in double application of penalties       
        $submission = JudgeDQSubmission::lockForUpdate()->find($submission);
        if ($submission->resolved != null) {
            return response()->json(['success' => false, 'message' => 'Submission already resolved']);
        }


        $result = $request->input('resolved') == "true" ? true : false;
        $submission->resolved = $result;
        $submission->save();

        // If true actually apply the DQ/Penalty to the team
        if ($result) {
            $event = $submission->getEvent;
            $entity = $submission->getHeat->entity;
            $code = Str::upper($submission->code);

            $violation = null;

            if (str_starts_with($code, 'P')) {
                $code = substr($code, 1);
                $violation = $event->addEntityPenalty($entity, $code);
            } else {
                $code = substr($code, 2);
                $event->clearEntityDisqualifications($entity);
                $violation = $event->addEntityDisqualification($entity, $code);
            }

            $submission->violation()->associate($violation);
            $submission->save();

            $violation->reportActivity("APPROVED", $submission);
        } else {

            $activity_type = str_starts_with($submission->code, 'P') ? 'PENALTY_REJECTED' : 'DISQUALIFICATION_REJECTED';
            $entity = $submission->getHeat?->entity;
            $event = $submission->getEvent;
            $submission->recordActivity($activity_type, "Referee rejected submission {$submission->code} for {$entity->getName()} in {$event->getName()} by {$submission->name} ({$submission->position})", related: [$entity, $event, $event->getCompetition, $submission], context: ['code' => $submission->code]);
        }

        $activeSubmissions = Session::get('activeSubmissions', []);
        $activeSubmissions = array_diff($activeSubmissions, [$submission->id]);
        Session::put('activeSubmissions', $activeSubmissions);

        DigitalJudge::setStatus('DQ/Pen | ' . ($result ? 'Approved ' : 'Rejected ') . $submission->code . ' for ' . $entity->getName() . ' in ' . $event->getName());


        return response()->json(['success' => true]);
    }

    public function getNeedingResolving()
    {
        $comp = DigitalJudge::getClientCompetition();
        $submissions = JudgeDQSubmission::where('competition', $comp->id)->whereNull('resolved')->get();

        foreach ($submissions as $submission) {
            $submission->eventName = $submission->getEvent->getName();
            $submission->teamName = $submission->getHeat?->entity->getName($comp) ?? null;
            $submission->heat = $submission->getHeat->heat ?? null;
            $submission->lane = $submission->getHeat->lane ?? null;
        }

        return response()->json(['success' => true, 'result' => $submissions]);
    }


    public function getAccepted()
    {
        $accepted = JudgeDQSubmission::where('competition',  DigitalJudge::getClientCompetition()->id)->where('resolved', true)->orderBy('updated_at', 'DESC')->get();
        $comp = DigitalJudge::getClientCompetition();
        $organisation = DigitalJudge::getClientCompetition()->getOrganisation;

        foreach ($accepted as $submission) {
            $submission->eventName = $submission->getEvent?->getName() ?: "Event not found";
            $submission->teamName = $submission->getHeat?->entity->getName($comp) ?? null;
            $submission->heat = $submission->getHeat->heat ?? null;
            $submission->lane = $submission->getHeat->lane ?? null;
            $submission->code_desc = $this->internalResolveCode($submission->code, $organisation);
        }

        return $accepted->groupBy('eventName');
    }

    private function internalResolveCode($code, ?Organisation $organisation = null)
    {

        $code = Str::lower($code);

        if (str_starts_with($code, 'p')) {
            $code = substr($code, 1);

            return PenaltyCode::message($code, $organisation) ?? "Penalty code not found";
        } else {
            $code = substr($code, 2);

            return DQCode::message($code, $organisation) ?? "DQ code not found";
        }
    }


    public function removeSubmission(JudgeDQSubmission $submission)
    {


        if ($submission->appealed) {
            return response()->json(['success' => true]);
        }

        try {
            $submission->violation->reportActivity("REMOVED", $submission);
            $this->removeCode($submission);

            $submission->delete();

            DigitalJudge::setStatus('DQ/Pen | Removed ' . $submission->code . ' for ' . $submission->getHeat?->entity->getName() . ' in ' . $submission->getEvent?->getName());

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false]);
        }
    }

    public function appealSubmission(JudgeDQSubmission $submission)
    {

        if ($submission->appealed) {
            return response()->json(['success' => true]);
        }

        try {
            $submission->violation->reportActivity("APPEALED", $submission);
            $this->removeCode($submission);

            $submission->appealed = true;

            $submission->save();

            DigitalJudge::setStatus('DQ/Pen | Appealed ' . $submission->code . ' for ' . $submission->getHeat?->entity->getName() . ' in ' . $submission->getEvent?->getName());

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false]);
        }
    }



    private function removeCode(JudgeDQSubmission $submission)
    {


        $event = $submission->getEvent;
        $entity = $submission->getHeat->entity;

        $code = Str::upper($submission->code);



        if (str_starts_with($code, 'P')) {

            $code = substr($code, 1);

            $penalty = $event->getEntityPenalties($entity)->where('code', $code)->first();

            if ($penalty) {
                $penalty->delete();
            }
        } else {

            $code = substr($code, 2);

            $disqualifications = $event->getEntityDisqualifications($entity)->where('code', $code)->first();

            if ($disqualifications) {
                $disqualifications->delete();
            }
        }
    }


    public function getEventRelatedCodes(string $eventName)
    {

        if (str_starts_with($eventName, 'sp')) {

            $event = CompetitionSpeedEvent::find(substr($eventName, 3));

            $eventName = $event->getBaseEvent()->name;
        } else {

            $eventName = 'SERC';
        }

        $organisation = DigitalJudge::getClientCompetition()->getOrganisation;

        if (!$organisation) {
            return response()->json(['related' => [], 'other' => []]);
        }

        $dqs = $organisation->disqualificationCodes()->with('eventCodes')->get();
        $pens = $organisation->penaltyCodes()->with('eventCodes')->get();



        $related = ['dq' => [], 'pen' => []];
        $other = ['dq' => [], 'pen' => []];

        foreach ($dqs as $dq) {
            $eventCode = $dq->eventCodes->where('event', $eventName)->first();

            $dq = [
                'id' => $dq->id,
                'code' => $dq->code,
                'description' => $dq->description
            ];

            if (!$eventCode) {
                if (array_key_exists('OTHER', $other['dq'])) {
                    $data = $other['dq']['OTHER'];
                    $data[] = $dq;
                    $other['dq']['OTHER'] = $data;
                } else {
                    $other['dq']['OTHER'] = [$dq];
                }
            } else {

                if (array_key_exists($eventCode->type, $related['dq'])) {

                    $data = $related['dq'][$eventCode->type];
                    $data[] = $dq;
                    $related['dq'][$eventCode->type] = $data;
                } else {
                    $related['dq'][$eventCode->type] = [$dq];
                }
            }
        }

        foreach ($pens as $pen) {
            $eventCode = $pen->eventCodes->where('event', $eventName)->first();

            $pen = [
                'id' => $pen->id,
                'code' => $pen->code,
                'description' => $pen->description
            ];

            if (!$eventCode) {
                if (array_key_exists('OTHER', $other['pen'])) {
                    $data = $other['pen']['OTHER'];
                    $data[] = $pen;
                    $other['pen']['OTHER'] = $data;
                } else {
                    $other['pen']['OTHER'] = [$pen];
                }
            } else {

                if (array_key_exists($eventCode->type, $related['pen'])) {

                    $data = $related['pen'][$eventCode->type];
                    $data[] = $pen;
                    $related['pen'][$eventCode->type] = $data;
                } else {
                    $related['pen'][$eventCode->type] = [$pen];
                }
            }
        }












        return response()->json(compact('related', 'other'));
    }
}
