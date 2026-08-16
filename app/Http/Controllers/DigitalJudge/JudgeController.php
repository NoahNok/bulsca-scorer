<?php

namespace App\Http\Controllers\DigitalJudge;

use App\DigitalJudge\DigitalJudge;
use App\Http\Controllers\Controller;
use App\Http\Requests\DigitalJudge\ConfirmJudgeRequest;
use App\Http\Requests\DigitalJudge\JoinCompetition;
use App\Http\Requests\DigitalJudge\JoinCompetitionRequest;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\DigitalJudge\Judge;
use App\Models\SERC;
use App\Models\SERCJudge;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class JudgeController extends Controller
{
    public function index()
    {
        $judge = Auth::user();

        return Inertia::render("Judge/Dashboard", [
            "competitions" => $judge->officiating->map(function ($comp) {
                return ["id" => $comp->id, "name" => $comp->name, "where" => $comp->where, "when" => $comp->when->format('M jS Y')];
            }),
        ]);
    }

    public function login(Request $request)
    {
        return Inertia::render("Judge/Auth/Login", [
            'stage' => 'email',
            'email' => $request->old('email', ''),
        ]);
    }

    public function resendPin(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $judge = User::where('email', $request->email)->first();

        if (!$judge) {
            return back()->withInput();
        }

        $judge->sendOneTimePassword();

        return back()->with('success', 'A new one-time password has been sent to your email. Please check your inbox and log in using the OTP.');
    }

    public function loginPost(Request $request)
    {

        // pin only required if query string step=pin is present

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'pin'  => 'required_if:step,pin|digits:6',
        ]);

        $judge = User::where('email', $request->email)->first();

        if (!$judge) {
            return back()->with(['email' => 'Invalid Email'])->withInput();
        }

        // if pin is present, attempt to log in
        if ($request->has('pin')) {
            $result = $judge->attemptLoginUsingOneTimePassword($request->input('pin'), remember: true);

            if ($result->isOk()) {
                // it is best practice to regenerate the session id after a login   
                $request->session()->regenerate();


                return Inertia::render("Judge/Dashboard");
            } else {
                return Inertia::render("Judge/Auth/Login", [
                    'stage'  => 'pin',
                    'email'  => $request->email,
                    'errors' => ['pin' => 'Invalid PIN. Please try again.'],
                ]);
            }
        }

        $judge->sendOneTimePassword();

        return Inertia::render("Judge/Auth/Login", [

            'stage'  => 'pin',
            'email'  => $request->email,
        ]);
    }

    public function joinCompetition(JoinCompetitionRequest $request)
    {

        $competition = Competition::where('digitalJudgePin', $request->pin)->first();

        if (!$competition) {
            return back()->withInput()->withErrors(['pin' => 'No competition found.']);
        }

        $request->user()->officiating()->syncWithoutDetaching($competition);

        return to_route('judge.competition', ['competition' => $competition->getSlug()]);
    }

    public function home(Competition $competition)
    {
        DigitalJudge::clearClientJudges();
        DigitalJudge::setTank(null);

        return Inertia::render("Judge/Competition/Home", [
            'competition' => $competition,
            'sercs' => $competition->getSERCs()->where('digitalJudgeEnabled', true)->get()->map(function ($serc) {

                return [
                    'id' => $serc->id,
                    'name' => $serc->getName(),
                    'type' => 'serc',
                    'completed' => $serc->completed || $serc->isComplete(),
                    'confirmed' => $serc->digitalJudgeConfirmed,
                    'judges' => $serc->getJudges->map(function ($judge) {
                        return [
                            'id' => $judge->id,
                            'name' => $judge->name
                        ];
                    })
                ];
            })->toArray(),
            'speeds' => $competition->getSpeedEvents()->where('digitalJudgeEnabled', true)->get()->map(function ($serc) {
                return [
                    'id' => $serc->id,
                    'name' => $serc->getName(),
                    'type' => 'serc',
                    'completed' => $serc->completed || $serc->isComplete(),
                    'confirmed' => $serc->digitalJudgeConfirmed,
                ];
            })->toArray(),
        ]);
    }
}
