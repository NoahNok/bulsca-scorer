<?php

namespace App\Traits;

use App\Models\Activity\Activity;
use App\Models\Activity\ActivityRelation;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

trait RecordActivity
{
    /**
     * @param Model|Model[]|null $related
     */
    public function recordActivity(string $activity, ?string $description = null, ?User $user = null, ?array $context = [], Model|array|null $related = null)
    {

        // Use the authenticated user if no user is provided, but allow for null if there is no authenticated user (e.g., during a background job)
        if (!$user && auth()->check()) {
            $user = auth()->user();
        }


        $activityRecord = Activity::create([
            'activity' => $activity,
            'description' => $description,
            'user_id' => $user ? $user->id : null,
            'context' => $context,
        ]);


        if ($related) {
            $relatedItems = is_array($related) ? $related : [$related];

            $activityRecord->relations()->createMany(
                array_map(function ($item) {
                    return [
                        'related_id' => $item->id,
                        'related_type' => get_class($item),
                    ];
                }, $relatedItems)
            );
        }
    }
}
