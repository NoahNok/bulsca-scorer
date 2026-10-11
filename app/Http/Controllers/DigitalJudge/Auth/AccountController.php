<?php

namespace App\Http\Controllers\DigitalJudge\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class AccountController extends Controller
{
    public function account()
    {
        $judge = Auth::user();

        return Inertia::render("Judge/Account", [
            'name'  => $judge->name,
            'email' => $judge->email,
        ]);
    }

    public function updateAccount(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $request->user()->update(['name' => $validated['name']]);

        Inertia::flash('toast', ['variant' => 'success', 'title' => 'Details saved']);

        return back();
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        Inertia::flash('toast', ['variant' => 'success', 'title' => 'Password changed']);

        return back();
    }
}
