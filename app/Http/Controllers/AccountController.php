<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ManagesDevices;
use App\Models\User;
use Illuminate\Http\Request;

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
        return view('account', [
            'user'    => $request->user(),
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
