<?php

namespace App\Http\Controllers\DigitalJudge;

use App\DigitalJudge\DigitalJudge;
use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\DigitalJudge\JudgeNote;
use App\Models\SERCJudge;
use App\Models\SERCResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DJJudgingController extends Controller
{

    public function confirmJudge(SERCJudge $judge)
    {

        // if (DigitalJudge::isClientHeadJudge()) {
        //     DigitalJudge::setClientJudge($judge);
        //     return redirect()->route('dj.judging.home');
        // }

        $serc = $judge->getSERC;
        $comp = $serc->getCompetition;


        return view('digitaljudge.judging.confirm-judge', ['serc' => $serc, 'comp' => $comp, 'judge' => $judge]);
    }

    public function confirmJudgePost(SERCJudge $judge)
    {
        DigitalJudge::setClientJudge($judge);

        if (DigitalJudge::getClientCompetition()->scoring_type == 'rlss-nationals') {
            return redirect()->route('dj.judging.tank');
        }
        DigitalJudge::setTank(null);

        return redirect()->route('dj.judging.home');
    }

    public function home()
    {
        return view('digitaljudge.judging.home', array_merge(DigitalJudge::getBladeProps(), ['head' => DigitalJudge::isClientHeadJudge()]));
    }

    public function changeJudge()
    {

        return view('digitaljudge.judging.change-judge');
    }


    public function selectTank()
    {
        $tanks = DB::select("SELECT DISTINCT serc_tank FROM competition_teams WHERE competition=? AND serc_tank > 0 ORDER BY serc_tank ASC", [DigitalJudge::getClientCompetition()->id]);

        return view('digitaljudge.judging.select-tank', array_merge(DigitalJudge::getBladeProps(), ['head' => DigitalJudge::isClientHeadJudge(), 'tanks' => $tanks]));
    }

    public function setTank(int $tank)
    {

        if ($tank < 1) $tank = 1;

        $max = DB::select("SELECT MAX(serc_tank) AS max FROM competition_teams WHERE competition=?;", [DigitalJudge::getClientCompetition()->id])[0]->max;


        if ($tank > $max) $tank = $max;

        DigitalJudge::setTank($tank);

        return redirect()->route('dj.judging.home');
    }



    public function nextTeamForJudge(SERCJudge $judge)
    {
        // For each team, determine if any marking points for the judge have been filled, get the first team with 0 filled
        // SELECT id FROM (SELECT id, (SELECT COUNT(*) FROM serc_results WHERE team=competition_teams.id AND marking_point IN (SELECT id FROM serc_marking_points WHERE judge=1)) AS markedPoints FROM competition_teams WHERE competition=3) AS b WHERE b.markedPoints = 0 LIMIT 1;

        $j = DigitalJudge::getClientJudges()[0]->id;
        $c = DigitalJudge::getClientCompetition()->id;

        $nextTeamIdRow = null;

        if (DigitalJudge::getTank()) {
            $nextTeamIdRow = DB::select("SELECT id FROM (SELECT id, (SELECT COUNT(*) FROM serc_results WHERE team=competition_teams.id AND marking_point IN (SELECT id FROM serc_marking_points WHERE judge=?)) AS markedPoints FROM competition_teams WHERE competition=? AND serc_tank=? ORDER BY serc_order) AS b WHERE b.markedPoints = 0 LIMIT 1;", [$j, $c, DigitalJudge::getTank()]);
        } else {
            $nextTeamIdRow = DB::select("SELECT id FROM (SELECT id, (SELECT COUNT(*) FROM serc_results WHERE team=competition_teams.id AND marking_point IN (SELECT id FROM serc_marking_points WHERE judge=?)) AS markedPoints FROM competition_teams WHERE competition=? ORDER BY serc_order) AS b WHERE b.markedPoints = 0 LIMIT 1;", [$j, $c]);
        }




        $nextTeamId = $nextTeamIdRow ? $nextTeamIdRow[0]->id : null;

        if ($nextTeamId == null) return redirect()->route('dj.judging.home', [$judge])->with('alert-error', 'No more teams left to judge!');

        $nextTeam = CompetitionTeam::find($nextTeamId);

        $resp = redirect()->route('dj.judging.judge-team', [$nextTeam]);
        if (Session::has('success')) $resp = $resp->with('success', Session::get('success'));
        return $resp;
    }

    public function judgeTeam(CompetitionTeam $team, Request $request)
    {

        // Check team are part of this competition to avoid any dangerous behaviour
        if ($team->competition != DigitalJudge::getClientCompetition()->id) return redirect()->route('dj.judging.home');

        if (!DigitalJudge::isClientHeadJudge() && DigitalJudge::hasTeamBeenJudgedAlready($team)) return redirect()->route('dj.judging.next-team');

        $resp = view('digitaljudge.judging.judge-team', array_merge(DigitalJudge::getBladeProps(), ['team' => $team, 'head' => DigitalJudge::isClientHeadJudge()]));

        if (Session::has('success')) $resp = $resp->with('success', Session::get('success'));

        return $resp;
    }

    public function saveTeamScores(Request $request, CompetitionTeam $team)
    {

        $teamAlreadyJudged = DigitalJudge::hasTeamBeenJudgedAlready($team);

        if ($team->competition != DigitalJudge::getClientCompetition()->id) return redirect()->route('dj.judging.home');

        $from = "";
        $to = "";

        foreach ($request->all() as $key => $value) {

            if (!str_starts_with($key, 'mp-')) continue;

            $markingPointId = explode("-", $key)[1];

            $sercResult = SERCResult::firstOrNew(['marking_point' => $markingPointId, 'team' => $team->id]);
            $from .= $sercResult->getMarkingPointName() . ": " . ($sercResult->result ?: "-") . ", ";
            $sercResult->result = $value;
            $to .= $sercResult->getMarkingPointName() . ": " . $sercResult->result . ", ";


            $sercResult->save();
            Cache::forget('mp.' . $markingPointId . '.team.' . $team->id);
        }


        if ($request->input('team-notes', "") != "" && !DigitalJudge::isClientHeadJudge()) {
            $judgeNote = new JudgeNote();
            $judgeNote->team = $team->id;
            $judgeNote->judge = DigitalJudge::getClientJudges()[0]->id;

            $judgeNote->note = $request->input('team-notes');

            $judgeNote->save();
        }




        if (DigitalJudge::isClientHeadJudge() && $teamAlreadyJudged) return redirect()->route('dj.judging.home')->with('success', 'Team ' . $team->getFullname() . ' has been re-marked!');

        return redirect()->route('dj.judging.next-team')->with('success', 'Team ' . $team->getFullname() . ' has been marked!');
    }

    public function addJudge()
    {
        return view('digitaljudge.judging.add-judge', DigitalJudge::getBladeProps());
    }

    public function addJudgePost(Request $request)
    {
        $judgeId = $request->input('addJudgeId');

        DigitalJudge::addClientJudge($judgeId);

        return redirect()->route('dj.judging.home', $judgeId);
    }

    public function removeJudge()
    {
        return view('digitaljudge.judging.remove-judge', array_merge(DigitalJudge::getBladeProps()));
    }

    public function removeJudgePost(Request $request)
    {
        $judgeId = $request->input('removeJudgeId');

        DigitalJudge::removeClientJudge($judgeId);

        return redirect()->route('dj.judging.home', $judgeId);
    }

    public function previousMarks()
    {


        $data = [];

        foreach (DigitalJudge::getClientJudges() as $judge) {

            $head = DigitalJudge::isClientHeadJudge();

            $judgeData = [];
            $judgeData['name'] = $judge->name;

            foreach ($judge->getMarkingPoints as $mp) {
                $mpData = [];
                $mpData['name'] = $mp->name;
                $mpData['marks'] = [];

                foreach (DigitalJudge::getClientCompetition()->getCompetitionTeams as $team) {
                    $mpData['marks'][] = [
                        'team' => DigitalJudge::getClientCompetition()->show_teams_to_judges || $head ? $team->getFullname() : $team->getPositionInDraw(),
                        'mark' => $mp->getScoreForTeam($team->id)
                    ];
                }

                $judgeData['mp'][] = $mpData;
            }

            $data[] = $judgeData;
        }



        return response()->json($data, 200);
    }


    public function tutorial()
    {
        return view('digitaljudge.judging.judge-tutorial', ['comp' => DigitalJudge::getClientCompetition()]);
    }

    public function tutorialPost()
    {
        return redirect()->route('dj.judging.home')->with('success', 'Tutorial completed!');
    }
}
