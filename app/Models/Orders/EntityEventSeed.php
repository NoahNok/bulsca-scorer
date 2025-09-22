<?php

namespace App\Models\Orders;

use App\Models\CompetitionSpeedEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntityEventSeed extends Model
{
    use HasFactory;

    public function entity()
    {
        return $this->morphTo();
    }

    public function event()
    {
        return $this->belongsTo(CompetitionSpeedEvent::class, 'id', 'event');
    }
}
