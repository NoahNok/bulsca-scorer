<?php

namespace App\Http\Controllers\DigitalJudge;

use App\DigitalJudge\DigitalJudge;
use App\Http\Controllers\Controller;
use App\Jobs\WebPush;
use App\Models\CompetitionSpeedEvent;

use App\Models\EventOOF;
use App\Models\Heat;
use App\Models\Penalty;
use App\Models\SpeedEvent;
use App\Models\SpeedResult;
use App\Notifications\General\DigitalJudge\SpeedMarked;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Continue_;

class SpeedJudgingController extends Controller
{
    public function timesIndex(CompetitionSpeedEvent $speed)
    {

        return view('digitaljudge.speeds.times.index', ['speed' => $speed, 'comp' => DigitalJudge::getClientCompetition(), 'head' => DigitalJudge::isClientHeadJudge()]);
    }

    public function timesJudge(CompetitionSpeedEvent $speed, int $heat)
    {

        $comp = DigitalJudge::getClientCompetition();


        if ($heat > $comp->getMaxHeats()) {
            return redirect()->route('dj.speeds.times.index', $speed)->with('alert-error', 'Heat ' . $heat . ' does not exist');
        }

        $heatTeams = $comp
            ->getHeatEntries()
            ->where('heat', $heat)
            ->where('event', $comp->scoring_type == 'rlss-nationals' ? $speed->id : null)
            ->get();

        $missingResult = false;

        // Code that checks if each team has a reuslt for the event
        foreach ($heatTeams as $team) {
            $sr = SpeedResult::where('competition_team', $team->team)
                ->where('event', $speed->id)
                ->first();

            if ($sr->result == null) {
                $missingResult = true;
                break;
            }
        }

        $isHead = $isHead = DigitalJudge::isClientHeadJudge();;

        if (!$missingResult && !$isHead) {
            return redirect()->route('dj.speeds.times.index', $speed)->with('alert-error', 'All teams have a result for Heat ' . $heat);
        }


        return view('digitaljudge.speeds.times.judge', ['speed' => $speed, 'comp' => DigitalJudge::getClientCompetition(), 'heat' => $heat]);
    }

    public function saveHeatTimes(CompetitionSpeedEvent $speed, int $heat, Request $request)
    {

        $teams = [];

        //dump($request->all());
        foreach ($request->all() as $key => $value) {
            if (!str_starts_with($key, "team-")) continue;
            $splt = explode("-", $key);


            $teams[$splt[1]][$splt[2]] = $value;
        }

        //dump($teams);

        foreach ($teams as $team => $values) {

            $sr = SpeedResult::where('competition_team', $team)->where('event', $speed->id)->first();

            if (str_starts_with($values['time'], "DN")) {
                $sr->result = 0;
                $sr->disqualification = str_starts_with($values['time'], 'DNS') ? 'DQ004' : 'DQ015';
                $sr->save();
                continue;
            }

            if (str_starts_with($values['time'], "OOT")) {
                $sr->result = 0;
                $sr->disqualification = 'DQ1001';
                $sr->save();
                continue;
            }


            $fromResult = $sr->getResultAsString();




            // See if time value starts with DNF





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

                # Judge side no uses centiseconds, so multiply by 10 for millis
                if (strlen($secMillisSplit[1]) == 2) {
                    $secMillisSplit[1] = $secMillisSplit[1] * 10;
                }

                $totalMillis = $min * 60000 + $secMillisSplit[0] * 1000 + $secMillisSplit[1];


                $sr->result = $totalMillis;
            }



            $sr->save();
            $toResult = $sr->getResultAsString();

            $from = "Result: " . $fromResult;
            $to = "Result: " . $toResult;
        }


        WebPush::dispatch(new SpeedMarked($speed, $heat));


        if ($heat + 1 > DigitalJudge::getClientCompetition()->getMaxHeats()) {
            return redirect()->route('dj.speeds.times.index', $speed);
        }

        return redirect()->route('dj.speeds.times.judge', [$speed, $heat + 1])->with('success', 'Successfully marked Heat ' . $heat);
    }

    // ##########################################################################
    // ########################### ORDER OF FINISH ##############################
    // ##########################################################################

    public function oofIndex(CompetitionSpeedEvent $speed)
    {

        return view('digitaljudge.speeds.oof.index', ['speed' => $speed, 'comp' => DigitalJudge::getClientCompetition(), 'head' => DigitalJudge::isClientHeadJudge()]);
    }

    public function oofJudge(CompetitionSpeedEvent $speed, int $heat)
    {


        $comp = DigitalJudge::getClientCompetition();


        if ($heat > $comp->getMaxHeats()) {
            return redirect()->route('dj.speeds.oof.index', $speed)->with('alert-error', 'Heat ' . $heat . ' does not exist');
        }

        $heatlanes = $comp
            ->getHeatEntries()
            ->where('heat', $heat)
            ->where('event', $comp->scoring_type == 'rlss-nationals' ? $speed->id : null)
            ->get();

        $missingResult = false;

        foreach ($heatlanes as $lane) {
            if ($lane->getOOF($speed->id) == null) {
                $missingResult = true;
                break;
            }
        }

        $isHead = $isHead = DigitalJudge::isClientHeadJudge();;

        if (!$missingResult && !$isHead) {
            return redirect()->route('dj.speeds.oof.index', $speed)->with('alert-error', 'All teams have a result for Heat ' . $heat);
        }


        return view('digitaljudge.speeds.oof.judge', ['speed' => $speed, 'comp' => DigitalJudge::getClientCompetition(), 'heat' => $heat]);
    }

    public function saveOofTimes(CompetitionSpeedEvent $speed, int $heat, Request $request)
    {



        $json = $request->all();

        foreach ($json as $res) {

            if (!array_key_exists('place', $res)) continue;




            // Pull the heat for the team
            $heatlane = Heat::where('lane', $res['lane'])->where('heat', $heat)->where('competition', DigitalJudge::getClientCompetition()->id)->first();

            if ($res['place'] == null) {
                EventOOF::where(['heat_lane' => $heatlane->id, 'event' => $speed->id])->delete();
                continue;
            };




            $eOof = EventOOF::firstOrNew(['heat_lane' => $heatlane->id, 'event' => $speed->id]);

            $eOof->oof = $res['place'];



            $eOof->save();
        }

        return response()->json(['success' => true]);
    }
}
