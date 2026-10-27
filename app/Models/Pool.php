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
}
