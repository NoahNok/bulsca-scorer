<?php

namespace App\Models;

use App\Models\Orders\Heat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pool extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'lanes'];

    public function getCompetition()
    {
        return $this->belongsTo(Competition::class, 'competition');
    }

    /**
     * Leagues swimming in this pool, in the order their heats run.
     */
    public function leagues()
    {
        return $this->belongsToMany(League::class, 'pool_league', 'pool', 'league')
            ->withPivot('is_pair', 'order')
            ->withTimestamps()
            ->orderByPivot('order');
    }

    public function heats()
    {
        return $this->hasMany(Heat::class, 'pool');
    }

    public function jsonable(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'lanes' => $this->lanes,
        ];
    }

    /**
     * How a heat is named wherever it's shown: "Main Pool · Heat 3", or just "Heat 3" without pools.
     */
    public static function heatLabel(?Pool $pool, int $heat): string
    {
        return $pool ? "{$pool->name} · Heat {$heat}" : "Heat {$heat}";
    }

    /**
     * The short heat and lane form for result tables: "Main Pool · H3 L2", or just "H3 L2" without pools.
     */
    public static function heatLaneLabel(?Pool $pool, int $heat, int $lane): string
    {
        return ($pool ? "{$pool->name} · " : '') . "H{$heat} L{$lane}";
    }
}
