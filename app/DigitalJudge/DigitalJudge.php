<?php

namespace App\DigitalJudge;

use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\SERCJudge;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class DigitalJudge
{

    public static function allowClientToJudge(Competition $competition)
    {
        Session::put('digitalJudgeClientAllowed', true);
        Session::put('digitalJudgeClientComp', $competition->id);
    }

    public static function stopClientFromJudging()
    {
        Session::forget(['digitalJudgeClientAllowed', 'digitalJudgeClientComp', 'digitalJudgeJudgeId', 'digitalJudgeClientHeadJudge']);
    }

    public static function canClientJudge()
    {
        return Session::get('digitalJudgeClientAllowed', false);
    }

    public static function getClientCompetition(): Competition
    {
        return Competition::find(Session::get('digitalJudgeClientComp', null));
    }

    public static function getBladeProps()
    {
        $serc = DigitalJudge::getClientJudges()[0]->getSERC;
        $comp = $serc->getCompetition;
        return  ['serc' => $serc, 'comp' => $comp, 'judges' => DigitalJudge::getClientJudges()];
    }

    public static function setClientJudge(SERCJudge $judge)
    {
        Session::put('digitalJudgeJudgeId', [$judge->id]);
    }

    public static function addClientJudge($judgeId)
    {
        Session::put('digitalJudgeJudgeId', array_merge(Session::get('digitalJudgeJudgeId', []), [$judgeId]));
    }

    public static function removeClientJudge($judgeId)
    {
        Session::put('digitalJudgeJudgeId', array_diff(Session::get('digitalJudgeJudgeId', []), [$judgeId]));
    }

    public static function getClientJudges()
    {
        return SERCJudge::find(Session::get('digitalJudgeJudgeId', []));
    }

    public static function clientUrlAndSessionJudgeMatch(): bool
    {

        return Request::route('judge')->id == Session::get('digitalJudgeJudgeId', -1);
    }

    public static function setClientHeadJudge(bool $isHeadJudge)
    {
        Session::put('digitalJudgeClientHeadJudge', $isHeadJudge);
    }

    public static function isClientHeadJudge(): bool
    {
        return Session::get('digitalJudgeClientHeadJudge', false);
    }

    public static function hasTeamBeenJudgedAlready(CompetitionTeam $team)
    {
        // SELECT COUNT(*) FROM serc_results WHERE team=? AND marking_point IN (SELECT id FROM serc_marking_points WHERE judge=?)

        $judge = DigitalJudge::getClientJudges()[0];

        $result = DB::select('SELECT COUNT(*) AS c FROM serc_results WHERE team=? AND marking_point IN (SELECT id FROM serc_marking_points WHERE judge=?);', [$team->id, $judge->id]);

        $count = $result[0]->c;

        return $count > 0;
    }
}
