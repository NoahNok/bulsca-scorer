<?php

namespace App\Http\Controllers\DigitalJudge\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Spatie\OneTimePasswords\Enums\ConsumeOneTimePasswordResult;

/**
 * Shared by the Digital Judge auth controllers that email officials a PIN.
 */
trait SendsLoginPins
{
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

    private function pinErrorMessage(ConsumeOneTimePasswordResult $result): string
    {
        return match ($result) {
            ConsumeOneTimePasswordResult::RateLimitExceeded => 'Too many attempts. Wait a minute and try again.',
            ConsumeOneTimePasswordResult::OneTimePasswordExpired => 'This PIN has expired. Send a new one below.',
            default => 'Invalid PIN. Please try again.',
        };
    }
}
