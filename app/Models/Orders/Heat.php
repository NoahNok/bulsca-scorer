<?php

namespace App\Models\Orders;

use App\Models\CompetitionSpeedEvent;
use App\Models\EventOOF;
use App\Models\SpeedResult;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Heat extends Model
{
    use HasFactory;

    public function entity()
    {
        return $this->morphTo();
    }

    public function speedEvent()
    {
        return $this->belongsTo(CompetitionSpeedEvent::class, 'speed_event');
    }

    public function oofs()
    {
        return $this->hasMany(EventOOF::class, 'heat_lane', 'id');
    }

    public function getOOF($speedId)
    {
        return $this->oofs?->where('event', $speedId)->first();
    }

    /**
     * DO NOT USE THIS METHOD
     * @deprecated
     */
    public function getResult(): ?SpeedResult
    {
        return $this->speedEvent->results()->whereMorphedTo('entity', $this->entity)->first();
    }
}
