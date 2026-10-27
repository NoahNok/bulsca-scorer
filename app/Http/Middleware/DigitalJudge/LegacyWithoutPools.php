<?php

namespace App\Http\Middleware\DigitalJudge;

use App\DigitalJudge\DigitalJudge;
use Closure;
use Illuminate\Http\Request;

/**
 * The legacy Digital Judge picks speed heats by number alone, but heat numbers restart in
 * each pool, so competitions using pools are judged in the new Digital Judge only.
 */
class LegacyWithoutPools
{
    public function handle(Request $request, Closure $next)
    {
        if (DigitalJudge::getClientCompetition()?->use_pools) {
            return redirect()->route('dj.home')->with('alert-error', 'This competition uses pools, so speed heats and DQs can only be judged in the new Digital Judge.');
        }

        return $next($request);
    }
}
