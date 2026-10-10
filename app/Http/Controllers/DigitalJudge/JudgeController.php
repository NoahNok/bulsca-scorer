<?php

namespace App\Http\Controllers\DigitalJudge;

use App\DigitalJudge\DigitalJudge;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
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
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Spatie\OneTimePasswords\Enums\ConsumeOneTimePasswordResult;
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

        if ($error = $this->throttlePinSend($request)) {
            return back()->withErrors(['email' => $error])->withInput();
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


                return redirect()->intended(route('judge.index'));
            } else {
                // attempts are rate limited per user by the one-time-passwords package
                // (rate_limit_attempts in its config)
                return Inertia::render("Judge/Auth/Login", [
                    'stage'  => 'pin',
                    'email'  => $request->email,
                    'errors' => ['pin' => match ($result) {
                        ConsumeOneTimePasswordResult::RateLimitExceeded => 'Too many attempts. Wait a minute and try again.',
                        ConsumeOneTimePasswordResult::OneTimePasswordExpired => 'This PIN has expired. Send a new one below.',
                        default => 'Invalid PIN. Please try again.',
                    }],
                ]);
            }
        }

        if ($error = $this->throttlePinSend($request)) {
            // resending from the PIN stage keeps the judge on that stage
            return Inertia::render("Judge/Auth/Login", [
                'stage'  => $request->boolean('resend') ? 'pin' : 'email',
                'email'  => $request->email,
                'errors' => [$request->boolean('resend') ? 'pin' : 'email' => $error],
            ]);
        }

        $judge->sendOneTimePassword();

        return Inertia::render("Judge/Auth/Login", [

            'stage'  => 'pin',
            'email'  => $request->email,
        ]);
    }

    /**
     * Limit how often PINs are emailed for an email address (per IP), so the
     * login form can't be used to spam inboxes or cycle through fresh PINs.
     * Returns an error message when the limit is hit, null otherwise.
     */
    private function throttlePinSend(Request $request): ?string
    {
        $key = 'judge-pin-send:' . Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            return 'Too many PINs requested. Try again in ' . ceil($seconds / 60) . ' minute(s).';
        }

        RateLimiter::hit($key, 10 * 60);

        return null;
    }

    public function loginPassword(LoginRequest $request)
    {
        // validation, rate limiting and the failed message all come from LoginRequest
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('judge.index'));
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('judge.login');
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
                    'type' => 'speed',
                    'completed' => $serc->completed || $serc->isComplete(),
                    'confirmed' => $serc->digitalJudgeConfirmed,
                ];
            })->toArray(),
        ]);
    }

    public function toggleReferee(Competition $competition)
    {

        $isReferee = DigitalJudge::isClientHeadJudge($competition);



        // Lets also set the value in hte pivot role
        $competition->setOfficialRole(Auth::user(), $isReferee ? 'official' : 'referee');


        Inertia::flash('toast', ['variant' => 'success', 'title' => $isReferee ? "You are nolonger a referee" : "You are now a referee"]);
    }
}
