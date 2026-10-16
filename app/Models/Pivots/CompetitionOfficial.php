<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CompetitionOfficial extends Pivot
{
    // seen this recently and they show as online on the Officials tab
    public const ONLINE_FOR_MINUTES = 5;

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    public function isOfficialRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isOnline(): bool
    {
        return $this->last_seen_at?->gt(now()->subMinutes(self::ONLINE_FOR_MINUTES)) ?? false;
    }
}
