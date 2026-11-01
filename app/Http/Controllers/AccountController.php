<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ManagesDevices;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class AccountController extends Controller
{
    use ManagesDevices;

    public function search(string $email)
    {



        if (strlen($email) < 3) {
            return response()->json([]);
        }

        $users = User::where('email', 'like', "{$email}%")
            ->limit(3)
            ->get(['email', 'name']);

        return response()->json($users);
    }

    public function edit(Request $request)
    {
        return Inertia::render('Manage/Account', [
            'user'    => $request->user()->only(['name', 'email']),
            'devices' => $this->devicesFor($request->user()),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $request->user()->update(['name' => $validated['name']]);

        return back()->with('success', 'Details saved');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $request->user()->logoutOtherDevices();

        return back()->with('success', 'Password changed');
    }

    public function signOutDevice(Request $request, int $login)
    {
        if (!$this->revokeDevice($request->user(), $login)) {
            return back()->with('alert-error', 'Use Sign out to sign out of this device');
        }

        return back()->with('success', 'Device signed out');
    }

    public function signOutOtherDevices(Request $request)
    {
        $request->user()->logoutOtherDevices();

        return back()->with('success', 'Other devices signed out');
    }
}
