<?php

namespace App\Http\Requests\Competition;

use App\Models\Competition;
use App\Models\Orders\Heat;
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
                    $key = $this->has('use_pools') && !$comp->use_pools ? 'use_pools' : 'heats_per_event';
                    $message = $key == 'use_pools'
                        ? 'Pools can only be used when heats per event is off.'
                        : 'Heats per event cannot be turned on while pools are in use.';

                    $validator->errors()->add($key, $message);
                    return;
                }

                if (!$usePools && $comp->use_pools && $this->hasPooledHeats($comp)) {
                    $validator->errors()->add('use_pools', 'Pools cannot be turned off while heats are assigned to pools.');
                }
            },
        ];
    }

    private function hasPooledHeats(Competition $comp): bool
    {
        return Heat::whereNotNull('pool')
            ->whereIn('speed_event', $comp->getSpeedEvents()->select('id'))
            ->exists();
    }
}
