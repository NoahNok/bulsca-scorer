<?php

namespace App\Models\Stats;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpeedEventStat extends Model
{
    use HasFactory;

    protected $table = 'stats_times';

    protected $fillable = ['competition', 'team', 'event', 'time', 'points', 'place'];
}
