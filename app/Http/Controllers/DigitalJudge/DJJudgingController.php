<?php

namespace App\Http\Controllers\DigitalJudge;

use App\DigitalJudge\DigitalJudge;
use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\SERCJudge;
use App\Models\SERCResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DJJudgingController extends Controller
{

    public function confirmJudge(SERCJudge $judge)
    {

        if (DigitalJudge::isClientHeadJudge()) {
            DigitalJudge::setClientJudge($judge);
            return redirect()->route('dj.judging.home', $judge);
        }

        return view('digitaljudge.judging.confirm-judge', DigitalJudge::getBladeProps($judge));
    }

    public function confirmJudgePost(SERCJudge $judge)
    {
        DigitalJudge::setClientJudge($judge);
        return redirect()->route('dj.judging.home', $judge);
    }

    public function home(SERCJudge $judge)
    {
        return view('digitaljudge.judging.home', array_merge(DigitalJudge::getBladeProps($judge), ['head' => DigitalJudge::isClientHeadJudge()]));
    }

    public function changeJudge()
    {

        return view('digitaljudge.judging.change-judge');
    }



    public function nextTeamForJudge(SERCJudge $judge)
    {
        // For each team, determine if any marking points for the judge have been filled, get the first team with 0 filled
        // SELECT id FROM (SELECT id, (SELECT COUNT(*) FROM serc_results WHERE team=competition_teams.id AND marking_point IN (SELECT id FROM serc_marking_points WHERE judge=1)) AS markedPoints FROM competition_teams WHERE competition=3) AS b WHERE b.markedPoints = 0 LIMIT 1;

        $j = DigitalJudge::getClientJudge()->id;
        $c = DigitalJudge::getClientCompetition()->id;
        $nextTeamIdRow = DB::select("SELECT id FROM (SELECT id, (SELECT COUNT(*) FROM serc_results WHERE team=competition_teams.id AND marking_point IN (SELECT id FROM serc_marking_points WHERE judge=?)) AS markedPoints FROM competition_teams WHERE competition=?) AS b WHERE b.markedPoints = 0 LIMIT 1;", [$j, $c]);

        $nextTeamId = $nextTeamIdRow ? $nextTeamIdRow[0]->id : null;

        if ($nextTeamId == null) return redirect()->route('dj.judging.home', [$judge])->with('alert-error', 'No more teams left to judge!');

        $nextTeam = CompetitionTeam::find($nextTeamId);

        return redirect()->route('dj.judging.judge-team', [$judge, $nextTeam]);
    }

    public function judgeTeam(SERCJudge $judge, CompetitionTeam $team)
    {


        if (!DigitalJudge::isClientHeadJudge() && DigitalJudge::hasTeamBeenJudgedAlready($judge, $team)) return redirect()->route('dj.judging.next-team', [$judge]);

        return view('digitaljudge.judging.judge-team', array_merge(DigitalJudge::getBladeProps($judge), ['team' => $team, 'head' => DigitalJudge::isClientHeadJudge()]));
    }

    public function saveTeamScores(Request $request, SERCJudge $judge, CompetitionTeam $team)
    {


        foreach ($request->all() as $key => $value) {

            if (!str_starts_with($key, 'mp-')) continue;

            $markingPointId = explode("-", $key)[1];

            $sercResult = SERCResult::firstOrNew(['marking_point' => $markingPointId, 'team' => $team->id]);
            $sercResult->result = $value;


            $sercResult->save();
            Cache::forget('mp.' . $markingPointId . '.team.' . $team->id);
        }


        if (DigitalJudge::isClientHeadJudge()) return redirect()->route('dj.judging.home', [$judge]);

        return redirect()->route('dj.judging.next-team', [$judge]);
    }
}
