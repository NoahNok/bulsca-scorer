<?php

namespace App\Http\Requests\Competition;

use App\Models\Competition;
use Illuminate\Validation\Validator;

/**
 * Pools only work with a single set of heats, and heat numbers restart per pool, so
 * use_pools can't be combined with heats_per_event or turned off while pooled heats exist.
 */
trait ValidatesPoolSettings
{
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $comp = $this->route('comp');

                if (!$comp instanceof Competition || $validator->errors()->isNotEmpty()) {
                    return;
                }

                $heatsPerEvent = (bool) $this->input('heats_per_event', $comp->heats_per_event);
                $usePools = (bool) $this->input('use_pools', $comp->use_pools);

                if ($usePools && $heatsPerEvent) {
                    $turningPoolsOn = !$comp->use_pools;

                    if ($turningPoolsOn) {
                        $validator->errors()->add('use_pools', 'Pools can only be used when heats per event is off.');
                    } else {
                        $validator->errors()->add('heats_per_event', 'Heats per event cannot be turned on while pools are in use.');
                    }
                    return;
                }

                if (!$usePools && $comp->use_pools && $comp->hasPooledHeats()) {
                    $validator->errors()->add('use_pools', 'Pools cannot be turned off while heats are assigned to pools.');
                }
            },
        ];
    }
}
