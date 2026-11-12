<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * A single-use link that signs a device in to Digital Judge as a user, which an Admin shows as a QR code
 * (manage.admin.users.login-qr). Only a hash of the token is stored, it lasts a few minutes, and the first
 * scan uses it up.
 */
class LoginToken extends Model
{
    public const TTL_MINUTES = 5;

    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Make a new token for $user, replacing any they have that haven't been used, and return it. It can't be
     * got back later.
     */
    public static function issueFor(User $user, User $by): string
    {
        // expired or used ones are no use to anyone
        static::where('expires_at', '<=', now())->orWhereNotNull('used_at')->delete();
        static::where('user_id', $user->id)->delete();

        $token = Str::random(64);

        static::create([
            'user_id' => $user->id,
            'created_by' => $by->id,
            'token_hash' => static::hash($token),
            'expires_at' => now()->addMinutes(static::TTL_MINUTES),
        ]);

        return $token;
    }

    /**
     * Use up $token, returning who it signs in, or null if it's unknown, expired or already used.
     */
    public static function consume(string $token): ?User
    {
        $hash = static::hash($token);

        // one conditional update, so two scans at once can't both get in
        $claimed = static::where('token_hash', $hash)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->update(['used_at' => now()]);

        if ($claimed !== 1) {
            return null;
        }

        return static::where('token_hash', $hash)->first()?->user;
    }

    private static function hash(string $token): string
    {
        return hash('sha256', $token);
    }
}
