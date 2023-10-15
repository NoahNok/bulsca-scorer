<?php

namespace App\Http\Controllers\DigitalJudge;

use App\DigitalJudge\DigitalJudge;
use App\Http\Controllers\Controller;
use App\Models\CompetitionSpeedEvent;
use App\Models\DigitalJudge\JudgeLog;
use App\Models\Penalty;
use App\Models\SpeedEvent;
use App\Models\SpeedResult;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Continue_;

class SpeedJudgingController extends Controller
{
    public function timesIndex(CompetitionSpeedEvent $speed)
    {

        return view('digitaljudge.speeds.times.index', ['speed' => $speed, 'comp' => DigitalJudge::getClientCompetition()]);
    }

    public function timesJudge(CompetitionSpeedEvent $speed, int $heat)
    {
        return view('digitaljudge.speeds.times.judge', ['speed' => $speed, 'comp' => DigitalJudge::getClientCompetition(), 'heat' => $heat]);
    }

    public function saveHeatTimes(CompetitionSpeedEvent $speed, int $heat, Request $request)
    {

        $teams = [];

        dump($request->all());
        foreach ($request->all() as $key => $value) {
            if (!str_starts_with($key, "team-")) continue;
            $splt = explode("-", $key);


            $teams[$splt[1]][$splt[2]] = $value;
        }

        dump($teams);

        foreach ($teams as $team => $values) {

            $sr = SpeedResult::where('competition_team', $team)->where('event', $speed->id)->first();


            if ($values['dq'] != "") {
                $sr->disqualification = $values['dq'];
            } else {
                $sr->disqualification = null;
            }

            Penalty::where('speed_result', $sr->id)->delete();
            foreach (explode(",", $values['p']) as $penalty) {
                if ($penalty == "") continue;

                $p = new Penalty();
                $p->speed_result = $sr->id;
                $p->code = trim($penalty);
                $p->save();
            }



            $minSecSplit = explode(":", $values['time']);

            if ($speed->getName() == "Rope Throw" && count($minSecSplit) == 1) {
                $sr->result = $minSecSplit[0];
            } else {

                if (count($minSecSplit) != 2) {
                    continue;
                }

                $min = $minSecSplit[0];
                $secMillisSplit = explode(".", $minSecSplit[1]);
                if (count($secMillisSplit) != 2) {
                    continue;
                }

                $totalMillis = $min * 60000 + $secMillisSplit[0] * 1000 + $secMillisSplit[1];


                $sr->result = $totalMillis;
            }




            $sr->save();

            $jl = new JudgeLog();

            $jl->competition = DigitalJudge::getClientCompetition()->id;
            $jl->team = $team;
            $jl->judgeName = DigitalJudge::getClientName();
            $jl->speed_event = $speed->id;
            $jl->save();
        }





        if ($heat + 1 > DigitalJudge::getClientCompetition()->getMaxHeats()) {
            return redirect()->route('dj.speeds.times.index', $speed);
        }

        return redirect()->route('dj.speeds.times.judge', [$speed, $heat + 1]);
    }
}
