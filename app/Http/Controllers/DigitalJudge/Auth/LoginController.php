<?php

namespace App\Http\Controllers\DigitalJudge\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoginController extends Controller
{
    use SendsLoginPins;

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
                // a correct PIN proves they own the email (this also finishes signing up)
                if (!$judge->hasVerifiedEmail()) {
                    $judge->markEmailAsVerified();
                }

                $this->completeRegistration($request, $judge);

                // it is best practice to regenerate the session id after a login
                $request->session()->regenerate();


                return redirect()->intended(route('judge.index'));
            } else {
                // attempts are rate limited per user by the one-time-passwords package
                // (rate_limit_attempts in its config)
                return Inertia::render("Judge/Auth/Login", [
                    'stage'  => 'pin',
                    'email'  => $request->email,
                    'registered' => $request->boolean('registered'),
                    'errors' => ['pin' => $this->pinErrorMessage($result)],
                ]);
            }
        }

        if ($error = $this->throttlePinSend($request)) {
            // resending from the PIN stage keeps the judge on that stage
            return Inertia::render("Judge/Auth/Login", [
                'stage'      => $request->boolean('resend') ? 'pin' : 'email',
                'email'      => $request->email,
                'registered' => $request->boolean('registered'),
                'errors'     => [$request->boolean('resend') ? 'pin' : 'email' => $error],
            ]);
        }

        $judge->sendOneTimePassword();

        return Inertia::render("Judge/Auth/Login", [

            'stage'      => 'pin',
            'email'      => $request->email,
            'registered' => $request->boolean('registered'),
        ]);
    }

    /**
     * Save the name and password from a registration made in this session, now the PIN has
     * proved they own the email.
     */
    private function completeRegistration(Request $request, User $judge): void
    {
        $pending = $request->session()->pull(RegisterController::PENDING_SESSION_KEY);

        if (!$pending || $pending['user_id'] !== $judge->id) {
            return;
        }

        $judge->name = $pending['name'];
        $judge->password = $pending['password'];
        $judge->save();
    }

    public function loginPassword(LoginRequest $request)
    {
        // validation, rate limiting and the failed message all come from LoginRequest
        $request->authenticate();

        /** @var User $judge */
        $judge = Auth::user();

        // self-registered officials have to confirm their email with a PIN before
        // a password works, so nobody can sign up with someone else's address
        if ($judge->isJudgeOnly() && !$judge->hasVerifiedEmail()) {
            Auth::guard('web')->logout();

            $props = [
                'stage'      => 'pin',
                'email'      => $judge->email,
                'registered' => true,
            ];

            if ($error = $this->throttlePinSend($request)) {
                return Inertia::render("Judge/Auth/Login", [...$props, 'errors' => ['pin' => $error]]);
            }

            $judge->sendOneTimePassword();

            return Inertia::render("Judge/Auth/Login", $props);
        }

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
}
