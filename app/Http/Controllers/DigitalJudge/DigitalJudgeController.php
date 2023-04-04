<?php

namespace App\Http\Controllers\DigitalJudge;

use App\DigitalJudge\DigitalJudge;
use App\Http\Controllers\Controller;
use App\Http\Requests\DigitalJudge\LoginRequest;
use App\Models\Competition;
use App\Models\SERC;
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


    function toggle(Competition $comp)
    {

        $wasState = $comp->digitalJudgeEnabled;
        $comp->digitalJudgeEnabled = !$wasState;
        $comp->save();
        if ($wasState) return redirect()->back(); // Stop here as we have turned DJ off

        $comp->digitalJudgePin = $comp->digitalJudgePin ?: sprintf("%06d", mt_rand(1, 999999));
        $comp->digitalJudgeHeadPin = $comp->digitalJudgeHeadPin ?: sprintf("%06d", mt_rand(1, 999999));

        $comp->save();

        return redirect()->back();
    }

    function sercToggle(Competition $comp, SERC $serc)
    {
        $serc->digitalJudgeEnabled = !$serc->digitalJudgeEnabled;
        $serc->Save();
        return redirect()->back();
    }

    function confirmResults(SERC $serc)
    {
        if ($serc->digitalJudgeConfirmed) return redirect()->route('dj.home')->with('alert-error', 'Results already confirmed!');;
        return view('digitaljudge.confirm-results', ['serc' => $serc]);
    }

    function confirmResultsPost(SERC $serc)
    {

        if ($serc->digitalJudgeConfirmed) return redirect()->route('dj.home')->with('success', 'Results Confirmed');

        $serc->digitalJudgeConfirmed = true;
        $serc->save();
        return redirect()->route('dj.home')->with('success', 'Results Confirmed');
    }
}
