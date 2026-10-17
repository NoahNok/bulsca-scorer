<?php

namespace App\Models\DigitalJudge;

use App\Models\Competition;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A message from a referee to every official on a competition. Never edited,
 * only withdrawn.
 */
class Announcement extends Model
{
    protected $guarded = [];

    protected $casts = [
        'withdrawn_at' => 'datetime',
    ];

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function withdrawnBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * What the judge app gets, from the list and the live events.
     */
    public function payload(): array
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'sender' => $this->sender ? [
                'id' => $this->sender->id,
                'name' => $this->sender->name,
                'photo' => $this->sender->profilePhotoUrls(),
            ] : null,
            'at' => $this->created_at->toIso8601String(),
            'withdrawn' => $this->withdrawn_at ? [
                'by' => $this->withdrawnBy ? ['id' => $this->withdrawnBy->id, 'name' => $this->withdrawnBy->name] : null,
                'at' => $this->withdrawn_at->toIso8601String(),
            ] : null,
        ];
    }
}
