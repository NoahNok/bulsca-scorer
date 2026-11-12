<?php

namespace App\Http\Controllers\DigitalJudge\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Signs a device in from a sign-in QR code an Admin showed (judge.qr-login).
 */
class QrLoginController extends Controller
{
    public function __invoke(Request $request, string $token)
    {
        $user = LoginToken::consume($token);

        if (!$user) {
            // the same answer whether it never existed, expired or was used, so it gives nothing away
            return redirect()->route('judge.login')
                ->withErrors(['email' => 'That sign-in code has expired or has already been used. Ask for a new one.']);
        }

        // a device already signed in as someone else swaps over
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
        }

        Auth::guard('web')->login($user, remember: true);

        $request->session()->regenerate();

        return redirect()->route('judge.index');
    }
}
