<?php

namespace App\DigitalJudge;

use App\Models\Competition;
use Illuminate\Support\Facades\Session;

class DigitalJudge
{

    public static function allowClientToJudge(Competition $competition)
    {
        Session::put('digitalJudgeClientAllowed', true);
        Session::put('digitalJudgeClientComp', $competition->id);
    }

    public static function canClientJudge()
    {
        return Session::get('digitalJudgeClientAllowed', false);
    }

    public static function getClientCompetition(): Competition
    {
        return Competition::find(Session::get('digitalJudgeClientComp', null));
    }
}
