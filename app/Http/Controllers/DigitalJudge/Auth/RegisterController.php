<?php

namespace App\Http\Controllers\DigitalJudge\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class RegisterController extends Controller
{
    use SendsLoginPins;

    public function register()
    {
        return Inertia::render("Judge/Auth/Register");
    }

    public function registerPost(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($error = $this->throttlePinSend($request)) {
            return back()->withErrors(['email' => $error])->withInput();
        }

        $judge = new User();
        $judge->name = $request->name;
        $judge->email = $request->email;
        $judge->password = Hash::make($request->password);
        // self-registered officials can only use Digital Judge, not the main site
        $judge->judge_only = true;
        $judge->save();

        // they finish signing up by entering this PIN on the login page
        $judge->sendOneTimePassword();

        return Inertia::render("Judge/Auth/Login", [
            'stage'      => 'pin',
            'email'      => $judge->email,
            'registered' => true,
        ]);
    }
}
