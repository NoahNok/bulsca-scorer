<?php

namespace App\Http\Controllers\DigitalJudge\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Spatie\OneTimePasswords\Enums\ConsumeOneTimePasswordResult;
use Inertia\Inertia;

class ForgotPasswordController extends Controller
{
    use SendsLoginPins;

    public function forgotPassword()
    {
        return Inertia::render("Judge/Auth/ForgotPassword", [
            'stage' => 'email',
        ]);
    }

    public function forgotPasswordPost(Request $request)
    {
        // posting just the email sends a PIN, posting the PIN with a new password resets it
        if (!$request->has('pin')) {
            $request->validate([
                'email' => ['required', 'email'],
            ]);

            if ($error = $this->throttlePinSend($request)) {
                return Inertia::render("Judge/Auth/ForgotPassword", [
                    'stage'  => $request->boolean('resend') ? 'pin' : 'email',
                    'email'  => $request->email,
                    'errors' => [$request->boolean('resend') ? 'pin' : 'email' => $error],
                ]);
            }

            // always move on to the PIN stage so this doesn't reveal which emails have accounts
            User::where('email', $request->email)->first()?->sendOneTimePassword();

            return Inertia::render("Judge/Auth/ForgotPassword", [
                'stage' => 'pin',
                'email' => $request->email,
            ]);
        }

        $validator = Validator::make($request->all(), [
            'email'    => ['required', 'email'],
            'pin'      => ['required', 'digits:6'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // a redirect back would land on the email stage, so re-render the PIN stage
        if ($validator->fails()) {
            return Inertia::render("Judge/Auth/ForgotPassword", [
                'stage'  => 'pin',
                'email'  => $request->email,
                'errors' => collect($validator->errors()->messages())->map(fn ($messages) => $messages[0]),
            ]);
        }

        $judge = User::where('email', $request->email)->first();

        $result = $judge
            ? $judge->consumeOneTimePassword($request->pin)
            : ConsumeOneTimePasswordResult::IncorrectOneTimePassword;

        if (!$result->isOk()) {
            return Inertia::render("Judge/Auth/ForgotPassword", [
                'stage'  => 'pin',
                'email'  => $request->email,
                'errors' => ['pin' => $this->pinErrorMessage($result)],
            ]);
        }

        $judge->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        // whoever knew the old password shouldn't stay signed in anywhere
        $judge->logoutEverywhere();

        if (!$judge->hasVerifiedEmail()) {
            $judge->markEmailAsVerified();
        }

        event(new PasswordReset($judge));

        Auth::login($judge, remember: true);
        $request->session()->regenerate();

        Inertia::flash('toast', ['variant' => 'success', 'title' => 'Password changed']);

        return to_route('judge.index');
    }
}
