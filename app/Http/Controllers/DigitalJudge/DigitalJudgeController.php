<?php

namespace App\Http\Controllers\DigitalJudge;

use App\DigitalJudge\DigitalJudge;
use App\Http\Controllers\Controller;
use App\Http\Requests\DigitalJudge\LoginRequest;
use App\Models\Competition;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DigitalJudgeController extends Controller
{
    function index()
    {
        return view('digitaljudge.index');
    }

    function login(LoginRequest $request)
    {
        $validated = $request->validated();
        $pin = $validated['pin'];

        $comp = Competition::where('digitalJudgePin', $pin)->where('digitalJudgeEnabled', true)->first();

        if ($comp == null) {


            Session::put('_old_input.pin', $pin);
            return redirect()->route('dj.index')->withErrors(['pin' => 'Invalid pin']);
        }

        DigitalJudge::allowClientToJudge($comp);

        return redirect()->route('dj.home');
    }

    function home()
    {
        return view('digitaljudge.home', ['comp' => DigitalJudge::getClientCompetition()]);
    }
}
