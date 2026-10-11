<?php

namespace App\Http\Controllers\Concerns;

use ALajusticia\Logins\Models\Login;
use App\Models\User;

/**
 * Listing and signing out the devices a user is signed in on (one Login per device).
 * Shared by the main site account page and the Digital Judge one.
 */
trait ManagesDevices
{
    /**
     * The user's signed-in devices, this one first, then most recently used.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function devicesFor(User $user): array
    {
        return $user->logins()
            ->orderByDesc('last_activity_at')
            ->get()
            ->sortByDesc(fn (Login $login) => $login->is_current)
            ->values()
            ->map(fn (Login $login) => [
                'id'         => $login->id,
                'type'       => $login->device_type ?: 'desktop',
                'name'       => $login->device ?: ($login->platform ?: 'Unknown device'),
                'browser'    => collect([$login->browser, $login->platform])->filter()->join(' on '),
                'ip'         => $login->ip_address,
                'lastActive' => $login->last_active,
                'signedIn'   => $login->created_at?->format('j M Y'),
                'current'    => $login->is_current,
                'remembered' => $login->remember_token !== null,
            ])
            ->all();
    }

    /**
     * Sign out one of the user's other devices. Returns false for this device, which uses a normal sign out.
     */
    protected function revokeDevice(User $user, int $loginId): bool
    {
        /** @var Login|null $login */
        $login = $user->logins()->withExpired()->find($loginId);

        // already signed out, e.g. from another tab
        if (!$login) {
            return true;
        }

        if ($login->is_current) {
            return false;
        }

        // revoke() only deletes logins that still have a session, a remembered one may not
        $login->revoke() || $login->delete();

        return true;
    }
}
