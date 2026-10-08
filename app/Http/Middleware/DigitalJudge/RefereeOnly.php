<?php

namespace App\Http\Middleware\DigitalJudge;

use App\DigitalJudge\DigitalJudge;
use App\Models\Competition;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RefereeOnly
{
    /**
     * Only allow the head referee of the {competition} in the route through. Run after
     * judge.officiates, which has already checked the competition is bound.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $competition = $request->route('competition');

        if (!$competition instanceof Competition || !DigitalJudge::isClientHeadJudge($competition)) {
            abort(403);
        }

        return $next($request);
    }
}
