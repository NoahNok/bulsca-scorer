<?php

namespace App\Http\Controllers\DigitalJudge\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class RegisterController extends Controller
{
    use SendsLoginPins;

    public const PENDING_SESSION_KEY = 'judge_pending_registration';

    public function register()
    {
        return Inertia::render("Judge/Auth/Register");
    }

    public function registerPost(Request $request)
    {
        // an account a competition admin made for this email (by adding them as an official) can be
        // claimed until it's verified
        $unclaimed = User::where('email', $request->email)
            ->where('judge_only', true)
            ->whereNull('email_verified_at')
            ->first();

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($unclaimed)],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($error = $this->throttlePinSend($request)) {
            return back()->withErrors(['email' => $error])->withInput();
        }

        $judge = $unclaimed;

        if (!$judge) {
            // a random password until the PIN below is entered
            $judge = new User();
            $judge->name = $request->name;
            $judge->email = $request->email;
            $judge->password = Hash::make(Str::random(40));
            // self-registered officials can only use Digital Judge, not the main site
            $judge->judge_only = true;
            $judge->save();
        }

        // the name and password are only saved once this session enters the PIN, which proves they
        // own the email. Writing them now would let anyone set the password on someone else's account.
        $request->session()->put(self::PENDING_SESSION_KEY, [
            'user_id'  => $judge->id,
            'name'     => $request->name,
            'password' => Hash::make($request->password),
        ]);

        // they finish signing up by entering this PIN on the login page
        $judge->sendOneTimePassword();

        return Inertia::render("Judge/Auth/Login", [
            'stage'      => 'pin',
            'email'      => $judge->email,
            'registered' => true,
        ]);
    }
}
