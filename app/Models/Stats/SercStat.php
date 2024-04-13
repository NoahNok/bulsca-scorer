<?php

namespace App\Models\Stats;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SercStat extends Model
{
    use HasFactory;

    protected $table = 'stats_serc';

    protected $fillable = ['competition', 'team', 'event', 'score', 'points', 'place'];
}
