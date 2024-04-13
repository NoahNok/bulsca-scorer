<?php

namespace App\Models\Stats;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultStat extends Model
{
    use HasFactory;

    protected $table = 'stats_results';

    protected $fillable = ['competition', 'team', 'event', 'score', 'points', 'place'];
}
