<?php

namespace App\Traits;

use App\Models\Activity\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

trait RecordActivity
{
    public function recordActivity(string $activity, ?string $description = null, ?User $user = null, ?array $context = [], ?Model $related = null)
    {

        // Use the authenticated user if no user is provided, but allow for null if there is no authenticated user (e.g., during a background job)
        if (!$user && auth()->check()) {
            $user = auth()->user();
        }

        $model = $related ?: $this;

        $model->activities()->create([
            'activity' => $activity,
            'description' => $description,
            'user_id' => $user ? $user->id : null,
            'context' => $context,
        ]);
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'related');
    }
}
