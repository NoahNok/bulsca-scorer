<?php

namespace App\Http\Controllers\DigitalJudge\Auth;

use App\Http\Controllers\Concerns\ManagesDevices;
use App\Http\Controllers\Controller;
use App\Services\ProfilePhotoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AccountController extends Controller
{
    use ManagesDevices;

    public function account()
    {
        $judge = Auth::user();

        return Inertia::render("Judge/Account", [
            'name'  => $judge->name,
            'email' => $judge->email,
            'photo' => $judge->profilePhotoUrls(),
            'deviceCount' => $judge->logins()->count(),
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

    public function updatePhoto(Request $request, ProfilePhotoService $photos)
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,png,webp', 'max:10240'],
        ]);

        try {
            $photos->store($request->user(), $request->file('photo'));
        } catch (\RuntimeException) {
            throw ValidationException::withMessages(['photo' => 'That image couldn\'t be read. Try a different photo.']);
        }

        Inertia::flash('toast', ['variant' => 'success', 'title' => 'Photo updated']);

        return back();
    }

    public function destroyPhoto(Request $request, ProfilePhotoService $photos)
    {
        $photos->delete($request->user());

        Inertia::flash('toast', ['variant' => 'success', 'title' => 'Photo removed']);

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

        $request->user()->logoutOtherDevices();

        Inertia::flash('toast', ['variant' => 'success', 'title' => 'Password changed']);

        return back();
    }

    public function devices(Request $request)
    {
        return Inertia::render("Judge/AccountDevices", [
            'devices' => $this->devicesFor($request->user()),
        ]);
    }

    public function signOutDevice(Request $request, int $login)
    {
        if (!$this->revokeDevice($request->user(), $login)) {
            Inertia::flash('toast', ['variant' => 'error', 'title' => 'Use Log out to sign out of this device']);

            return back();
        }

        Inertia::flash('toast', ['variant' => 'success', 'title' => 'Device signed out']);

        return back();
    }

    public function signOutOtherDevices(Request $request)
    {
        $request->user()->logoutOtherDevices();

        Inertia::flash('toast', ['variant' => 'success', 'title' => 'Other devices signed out']);

        return back();
    }

    public function signOutEverywhere(Request $request)
    {
        $user = $request->user();

        Auth::guard('web')->logout();
        $user->logoutEverywhere();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('judge.login');
    }
}
