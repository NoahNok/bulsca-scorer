<?php

namespace App\Http\Controllers\DigitalJudge;

use App\DigitalJudge\DigitalJudge;
use App\Http\Controllers\Controller;
use App\Http\Requests\DigitalJudge\DQRequest;
use App\Models\CompetitionTeam;
use App\Models\DigitalJudge\JudgeLog;
use App\Models\Penalty;
use App\Models\SERC;
use App\Models\SERCDisqualification;
use App\Models\SERCPenalty;
use App\Models\SpeedResult;
use Illuminate\Http\Request;

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

        $jl = new JudgeLog();
        $jl->competition = DigitalJudge::getClientCompetition()->id;
        $jl->judgeName = DigitalJudge::getClientName();
        $jl->team = $dQRequest->input('team');

        if (str_starts_with($event, 'sp')) {
            // SPEED EVENT
            $eventId = substr($event, 3);

            $jl->speed_event = $eventId;


            if ($dQRequest->input('type') == 'dq') {
                // DQ

                $sr = SpeedResult::where('competition_team', $dQRequest->input('team'))->where('event', $eventId)->first();
                $jl->from = $sr->disqualification ?: "-";
                $sr->disqualification = $dQRequest->input('code');
                $sr->save();


                $jl->to = $dQRequest->input('code') == "" ? "-" : $dQRequest->input('code');
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
                $jl->from = $sr->getPenaltiesAsString() == "" ? "-" : $sr->getPenaltiesAsString();
                $jl->to = implode(",", $valid) == "" ? "-" : implode(",", $valid);
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

            $jl->judge = SERC::find($eventId)->getJudges->first()->id;

            if ($dQRequest->input('type') == 'dq') {
                $jl->from =  SERCDisqualification::where(['team' => $dQRequest->input('team'), 'serc' => $eventId])->first()?->code ?: "-";
                if ($dQRequest->input('code') == null) {
                    SERCDisqualification::where(['team' => $dQRequest->input('team'), 'serc' => $eventId])->delete();
                } else {
                    $sd = SERCDisqualification::firstOrNew(['team' => $dQRequest->input('team'), 'serc' => $eventId]);
                    $sd->code = $dQRequest->input('code');
                    $sd->save();
                }

                $jl->to = $dQRequest->input('code') == "" ? "-" : $dQRequest->input('code');
            } else {

                $sd = SERCPenalty::firstOrNew(['team' => $dQRequest->input('team'), 'serc' => $eventId]);
                $jl->from = $sd->codes == "" ? "-" : $sd->codes;
                $sd->codes = $dQRequest->input('code') ?: "";
                $jl->to = $sd->codes == "" ? "-" : $sd->codes;
                $sd->save();
            }
        }

        $jl->save();
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
}
