<?php

namespace App\Http\Controllers\DigitalJudge;

use App\DigitalJudge\DigitalJudge;
use App\Http\Controllers\Controller;
use App\Http\Requests\DigitalJudge\LoginRequest;
use App\Models\Competition;
use App\Models\SERCJudge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DigitalJudgeController extends Controller
{
    function index()
    {
        if (DigitalJudge::canClientJudge()) return redirect()->route('dj.home');
        return view('digitaljudge.index');
    }

    function login(LoginRequest $request)
    {
        $validated = $request->validated();
        $pin = $validated['pin'];

        $comp = Competition::where('digitalJudgePin', $pin)->where('digitalJudgeEnabled', true)->first();

        if ($comp) $this->startJudging($comp, false);

        $headComp = Competition::where('digitalJudgeHeadPin', $pin)->where('digitalJudgeEnabled', true)->first();

        if ($headComp) $this->startJudging($headComp, true);

        Session::put('_old_input.pin', $pin);
        return redirect()->route('dj.index')->withErrors(['pin' => 'Invalid pin']);
    }

    function logout()
    {
        DigitalJudge::stopClientFromJudging();
        return redirect()->route('dj.index');
    }

    private function startJudging(Competition $comp, bool $isHead)
    {
        DigitalJudge::allowClientToJudge($comp);
        DigitalJudge::setClientHeadJudge($isHead);

        return redirect()->route('dj.home');
    }

    function home()
    {
        return view('digitaljudge.home', ['comp' => DigitalJudge::getClientCompetition(), 'head' => DigitalJudge::isClientHeadJudge()]);
    }
}
