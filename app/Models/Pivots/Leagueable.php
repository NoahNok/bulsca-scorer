<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\MorphPivot;

/**
 * An Entry's membership of a League. A model, so that `attach()`, `detach()` and `sync()` raise
 * model events: a League's members decide its results (`App\Scoring\ResultsVersion`).
 */
class Leagueable extends MorphPivot
{
    protected $table = 'leagueables';

    public $incrementing = true;
}
