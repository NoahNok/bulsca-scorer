<?php

namespace App\Models\Orders;

use App\Models\CompetitionSpeedEvent;
use App\Models\SpeedResult;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntityEventSeed extends Model
{
    use HasFactory;

    protected $fillable = ['seed', 'entity_type', 'entity_id', 'speed_event'];

    public function entity()
    {
        return $this->morphTo();
    }

    public function event()
    {
        return $this->belongsTo(CompetitionSpeedEvent::class, 'id', 'event');
    }

    public function prettySeed()
    {
        return SpeedResult::prettyTime($this->seed);
    }
}
