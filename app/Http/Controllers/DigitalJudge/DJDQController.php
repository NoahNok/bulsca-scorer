<?php

namespace App\Http\Controllers\DigitalJudge;

use App\DigitalJudge\DigitalJudge;
use App\Http\Controllers\Controller;
use App\Http\Requests\DigitalJudge\DQRequest;
use App\Http\Requests\DigitalJudge\JudgeDQSubmissionRequest;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\DigitalJudge\JudgeDQSubmission;
use App\Models\DQCode;
use App\Models\Penalty;
use App\Models\PenaltyCode;
use App\Models\SERC;
use App\Models\SERCDisqualification;
use App\Models\SERCPenalty;
use App\Models\SpeedEvent;
use App\Models\SpeedResult;
use Illuminate\Http\Request;
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
        return view('digitaljudge.dq.judge-issue', ['comp' => DigitalJudge::getClientCompetition()]);
    }

    public function resolveCode(string $code)
    {



        if (str_starts_with($code, 'p')) {
            $code = substr($code, 1);

            return response()->json(['description' => PenaltyCode::find($code)->description ?? "Penalty code not found", 'type' => 'penalty']);
        } else {
            $code = substr($code, 2);

            return response()->json(['description' => DQCode::find($code)->description ?? "DQ code not found"]);
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
        return view('digitaljudge.dq.head-resolve', ['comp' => DigitalJudge::getClientCompetition()]);
    }

    public function resolveSubmission(JudgeDQSubmission $submission, Request $request)
    {

        $result = $request->input('resolved') == "true" ? true : false;
        $submission->resolved = $result;
        $submission->save();

        // If true actually apply the DQ/Penalty to the team
        if ($result) {
            $event = $submission->getEvent;
            $teamId = $submission->getHeat->team;
            $code = Str::upper($submission->code);


            if (str_starts_with($code, 'P')) {
                $event->addTeamPenalty($teamId, $code);
            } else {
                $event->addTeamDQ($teamId, $code);
            }
        }

        $activeSubmissions = Session::get('activeSubmissions', []);
        $activeSubmissions = array_diff($activeSubmissions, [$submission->id]);
        Session::put('activeSubmissions', $activeSubmissions);

        return response()->json(['success' => true]);
    }

    public function getNeedingResolving()
    {
        $submissions = JudgeDQSubmission::where('competition', DigitalJudge::getClientCompetition()->id)->whereNull('resolved')->get();

        foreach ($submissions as $submission) {
            $submission->eventName = $submission->getEvent->getName();
            $submission->teamName = $submission->getHeat?->getTeam->getFullname() ?? null;
            $submission->heat = $submission->getHeat->heat ?? null;
            $submission->lane = $submission->getHeat->lane ?? null;
        }

        return response()->json(['success' => true, 'result' => $submissions]);
    }


    public function getAccepted()
    {
        $accepted = JudgeDQSubmission::where('competition',  DigitalJudge::getClientCompetition()->id)->where('resolved', true)->orderBy('updated_at', 'DESC')->get();



        foreach ($accepted as $submission) {
            $submission->eventName = $submission->getEvent->getName();
            $submission->teamName = $submission->getHeat?->getTeam->getFullname() ?? null;
            $submission->heat = $submission->getHeat->heat ?? null;
            $submission->lane = $submission->getHeat->lane ?? null;
            $submission->code_desc = $this->internalResolveCode(($submission->code));
        }

        return $accepted->groupBy('eventName');
    }

    private function internalResolveCode($code)
    {

        $code = Str::lower($code);

        if (str_starts_with($code, 'p')) {
            $code = substr($code, 1);

            return PenaltyCode::find($code)->description ?? "Penalty code not found";
        } else {
            $code = substr($code, 2);

            return DQCode::find($code)->description ?? "DQ code not found";
        }
    }


    public function removeSubmission(JudgeDQSubmission $submission)
    {


        if ($submission->appealed) {
            return response()->json(['success' => true]);
        }

        try {
            $this->removeCode($submission);

            $submission->delete();

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
            $this->removeCode($submission);

            $submission->appealed = true;

            $submission->save();

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false]);
        }
    }



    private function removeCode(JudgeDQSubmission $submission)
    {


        $event = $submission->getEvent;
        $team = $submission->getHeat->getTeam;

        if ($event instanceof SERC) {
            $this->removeSercCode($submission->code, $event->id, $team->id);
        } else {
            $this->removeSpeedCode($submission->code, $event->id, $team->id);
        }
    }

    private function removeSpeedCode($code, $eventId, $teamId)
    {

        $code = Str::upper($code);

        $result = SpeedResult::where('event', $eventId)->where('competition_team', $teamId)->first();

        if (str_starts_with($code, 'P')) {
            $p = Penalty::where('speed_result', $result->id)->where('code', $code)->first();
            $p->delete();
        } else {

            if (Str::upper($result->disqualification) == $code) {
                $result->disqualification = null;
                $result->save();
            }
        }
    }

    private function removeSercCode($code, $eventID, $teamId)
    {
        $code = Str::upper($code);

        if (str_starts_with($code, 'P')) {
            $sp = SERCPenalty::where('team', $teamId)->where('serc', $eventID)->first();


            $codes = explode(",", $sp->codes);

            $codes = array_filter($codes, function ($c) use ($code) {
                return $c != $code;
            });

            if (count($codes) == 0) {
                $sp->delete();
            } else {
                $sp->codes = $codes;
                $sp->save();
            }
        } else {
            SERCDisqualification::where('team', $teamId)->where('serc', $eventID)->where('code', $code)->delete();
        }
    }

    public function getEventRelatedCodes(string $eventName)
    {

        $event = null;

        if (str_starts_with($eventName, 'sp')) {

            $event = CompetitionSpeedEvent::find(substr($eventName, 3));

            $event = $event->getBaseEvent();
        } else {
            $event = new SpeedEvent();
            $event->name = 'SERC';
        }




        return response()->json(['related' => $event->getEventCodes(), 'other' => $event->getMissingEventCodes()]);
    }
}
