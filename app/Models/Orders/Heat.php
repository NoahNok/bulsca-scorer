<?php

namespace App\Models\Orders;

use App\Models\CompetitionSpeedEvent;
use App\Models\DigitalJudge\JudgeDQSubmission;
use App\Models\EventOOF;
use App\Models\Pool;
use App\Models\SpeedResult;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Heat extends Model
{
    use HasFactory, CascadesDeletes;

    protected $fillable = [
        'speed_event',
        'entity_id',
        'entity_type',
        'heat',
        'lane',
        'pool',
    ];

    protected $cascadeDeletes = [
        'judgeDQSubmissions'
    ];

    public function entity()
    {
        return $this->morphTo();
    }

    public function speedEvent()
    {
        return $this->belongsTo(CompetitionSpeedEvent::class, 'speed_event');
    }

    public function getPool()
    {
        return $this->belongsTo(Pool::class, 'pool');
    }

    /**
     * Heat numbers restart per pool, so a heat is only identified by its number and pool together.
     */
    public function scopeInPool($query, ?int $pool)
    {
        return $pool ? $query->where('pool', $pool) : $query->whereNull('pool');
    }

    /**
     * Lanes grouped into heats, ordered by pool then heat number.
     *
     * @return Collection<int, Collection<int, Heat>>
     */
    public static function groupIntoHeats(Collection $lanes): Collection
    {
        return $lanes->groupBy(fn(Heat $lane) => ($lane->pool ?? 0) . ':' . $lane->heat)
            ->sortBy(fn($heat) => [$heat->first()->pool ?? 0, $heat->first()->heat])
            ->values();
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

    public function judgeDQSubmissions()
    {
        return $this->hasMany(JudgeDQSubmission::class, 'heat_lane');
    }
}
