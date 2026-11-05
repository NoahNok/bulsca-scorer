<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * The last time a user opened one of a competition's Management pages.
 */
class CompetitionVisit extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'competition_id', 'visited_at'];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }
}
