<?php

namespace App\Http\Controllers\DigitalJudge;

use App\DigitalJudge\DigitalJudge;
use App\Http\Controllers\Controller;
use App\Jobs\WebPush;
use App\Models\AbstractClasses\Entity;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\DigitalJudge\JudgeNote;
use App\Models\DigitalJudge\OverallJudgeNote;
use App\Models\Orders\Draw;
use App\Models\SERC;
use App\Models\SERCJudge;
use App\Models\SERCResult;
use App\Notifications\General\DigitalJudge\SercMarked;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
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

        if (DigitalJudge::getClientCompetition()->getScoringSettings->use_tanks) {
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



        $tanks = SERC::where('competition', DigitalJudge::getClientCompetition()->id)->first()->draw()->orderBy('tank')->distinct('tank')->get('tank')->pluck('tank')->toArray();
        //$tanks = DB::select("SELECT DISTINCT serc_tank FROM competition_teams WHERE competition=? AND serc_tank > 0 ORDER BY serc_tank ASC", [DigitalJudge::getClientCompetition()->id]);

        return view('digitaljudge.judging.select-tank', array_merge(DigitalJudge::getBladeProps(), ['head' => DigitalJudge::isClientHeadJudge(), 'tanks' => $tanks]));
    }

    public function setTank(int $tank)
    {

        if ($tank < 1) $tank = 1;

        $max = SERC::where('competition', DigitalJudge::getClientCompetition()->id)->first()->draw()->orderBy('tank')->distinct('tank')->get('tank')->pluck('tank')->max();


        if ($tank > $max) $tank = $max;

        DigitalJudge::setTank($tank);

        return redirect()->route('dj.judging.home');
    }



    public function nextTeamForJudge(SERCJudge $judge)
    {
        // For each team, determine if any marking points for the judge have been filled, get the first team with 0 filled
        // SELECT id FROM (SELECT id, (SELECT COUNT(*) FROM serc_results WHERE team=competition_teams.id AND marking_point IN (SELECT id FROM serc_marking_points WHERE judge=1)) AS markedPoints FROM competition_teams WHERE competition=3) AS b WHERE b.markedPoints = 0 LIMIT 1;

        $j = DigitalJudge::getClientJudges()[0];

        $nextTeamId = null;

        $serc = $j->getSERC;
        $draw = $serc->getDraw;

        // Check if we are marking a tank
        $tank = DigitalJudge::getTank();
        if ($tank != null) {
            $draw = $draw->where('tank', $tank);
        }

        $draw = $draw->sortBy('draw');

        foreach ($draw as $allocation) {
            $marked = DigitalJudge::hasTeamBeenJudgedAlready($allocation->entity);
            if (!$marked) {

                $nextTeamId = $allocation->entity->id;
                break;
            }
        }


        if ($nextTeamId == null) return redirect()->route('dj.judging.overall-comments', [$judge])->with('alert-error', 'No more teams left to judge!');

        $nextTeam = CompetitionTeam::find($nextTeamId);

        $resp = redirect()->route('dj.judging.judge-team', [$nextTeam]);
        if (Session::has('success')) $resp = $resp->with('success', Session::get('success'));
        return $resp;
    }

    public function judgeTeam(int $entity_id, Request $request)
    {


        $team = DigitalJudge::getClientJudges()[0]->getSERC->getScorableEntity()->findOrFail($entity_id);

        // Check team are part of this competition to avoid any dangerous behaviour
        if ($team->competition != DigitalJudge::getClientCompetition()->id) return redirect()->route('dj.judging.home');

        if (!DigitalJudge::isClientHeadJudge() && DigitalJudge::hasTeamBeenJudgedAlready($team)) return redirect()->route('dj.judging.next-team');

        $resp = view('digitaljudge.judging.judge-team', array_merge(DigitalJudge::getBladeProps(), ['team' => $team, 'head' => DigitalJudge::isClientHeadJudge()]));

        if (Session::has('success')) $resp = $resp->with('success', Session::get('success'));

        return $resp;
    }

    public function saveTeamScores(int $entity_id, Request $request)
    {

        $team = DigitalJudge::getClientJudges()[0]->getSERC->getScorableEntity()->findOrFail($entity_id);

        if ($team->competition != DigitalJudge::getClientCompetition()->id) return redirect()->route('dj.judging.home');

        $from = "";
        $to = "";

        $serc = SERC::find($request->input('serc'));

        foreach ($request->all() as $key => $value) {

            if (!str_starts_with($key, 'mp-')) continue;

            $markingPointId = explode("-", $key)[1];

            $sercResult = SERCResult::firstOrNew(['marking_point' => $markingPointId, 'entity_type' => $team->getMorphClass(), 'entity_id' => $team->id]);
            $from .= $sercResult->getMarkingPointName() . ": " . ($sercResult->result ?: "-") . ", ";
            $sercResult->result = $value;
            $to .= $sercResult->getMarkingPointName() . ": " . $sercResult->result . ", ";


            $sercResult->save();
            Cache::forget('mp.' . $markingPointId . '.entity.' . $team->id);
        }

        foreach ($request->all() as $key => $value) {
            if (!str_starts_with($key, 'team-notes-')) continue;



            $judgeNote = JudgeNote::firstOrNew(['judge' => substr($key, 11), 'entity_type' => $team->getMorphClass(), 'entity_id' => $team->id]);

            $judgeNote->note = $value;

            if ($value == "") {
                if ($judgeNote->id) $judgeNote->delete();
                continue;
            }

            $judgeNote->save();
        }




        $this->dispatchTeamMarkedNotification($serc, $team);


        if ($request->input('a', 'next') == 'back') {
            return redirect()->route('dj.judging.home')->with('success', 'Team ' . $team->getFullname() . ' has been re-marked!');
        }


        // if (DigitalJudge::isClientHeadJudge() && $teamAlreadyJudged) return redirect()->route('dj.judging.home')->with('success', 'Team ' . $team->getFullname() . ' has been re-marked!');

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

                foreach ($judge->getSERC->getDraw as $draw) {
                    $mpData['marks'][] = [
                        'team' => DigitalJudge::getClientCompetition()->show_teams_to_judges || $head ? $draw->entity->getName() : $draw->draw,
                        'mark' => $mp->getScoreForTeam($draw->entity)
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

    private function dispatchTeamMarkedNotification(SERC $serc, Entity $entity)
    {

        $key = "webpush_notif_{$serc->id}_{$entity->id}";

        if (Redis::exists($key)) return;

        $wasSet = Redis::setnx($key, true);

        if (!$wasSet) return;

        Redis::expire($key, 86400);

        WebPush::dispatch(new SercMarked($serc, $entity));
    }

    public function overallComments()
    {
        return view('digitaljudge.judging.overall-comments', array_merge(DigitalJudge::getBladeProps()));
    }

    public function overallCommentsPost(Request $request)
    {

        foreach ($request->all() as $key => $value) {
            if (!str_starts_with($key, 'judge-overall-')) continue;



            $judgeId = substr($key, 14);

            $overallJudgeNote = OverallJudgeNote::firstOrNew(['judge' => $judgeId]);


            if ($value == "") {

                if ($overallJudgeNote->id) {
                    $overallJudgeNote->delete();
                }

                continue;
            }


            $overallJudgeNote->judge = $judgeId;
            $overallJudgeNote->note = $value;

            $overallJudgeNote->save();
        }


        return redirect()->route('dj.judging.home')->with('success', 'Overall feedback submitted.');
    }
}
