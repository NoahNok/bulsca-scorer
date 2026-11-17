<?php

namespace App\Models\Event;

use App\Scoring\Input\Formula;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * An Event's formula: how its results become points and places. The settings are in `schema`;
 * `App\Scoring\Engine\EventScorer` scores an Event with them.
 */
class ScoringSchema extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'schema', 'organisation', 'default'];

    protected $casts = [
        'schema' => 'array'
    ];

    public function editFromRequest($request)
    {
        $validated = $request->validated();


        if (array_key_exists('name', $validated) && $validated['name']) {
            $this->name = $validated['name'];
        }

        $ss = ['equation' => $validated['equation'], 'global_variables' => $validated['global_variables']];

        if (array_key_exists('local_variables', $validated)) {
            $ss['local_variables'] = $validated['local_variables'];
        }

        if (array_key_exists('penalty_func', $validated)) {
            $ss['penalty_func'] = $validated['penalty_func'];
        }

        if (array_key_exists('auto_penalties', $validated)) {
            $ss['auto_penalties'] = $validated['auto_penalties'];
        }

        if (array_key_exists('auto_disqualifications', $validated)) {
            $ss['auto_disqualifications'] = $validated['auto_disqualifications'];
        }

        //rank_higher rank_equation allow_disqualified_to_rank

        foreach (['rank_higher', 'rank_equation', 'allow_disqualified_to_rank', 'group_by', 'force_penalty'] as $key) {
            if (array_key_exists($key, $validated)) {
                $ss[$key] = $validated[$key];
            }
        }

        $this->schema = $ss;



        $this->save();
    }

    /**
     * The settings as the scoring engine reads them, with each one's default filled in
     */
    public function formula(): Formula
    {
        return Formula::fromSchema($this->schema);
    }
}
