<?php

namespace App\Http\Middleware\DigitalJudge;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\DigitalJudge\Violation\ViolationSubmission;
use App\Models\SERC;
use App\Models\SERCJudge;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OfficiatesCompetition
{
    /**
     * Ensure the logged in user officiates the {competition} in the route, and that any
     * other bound models in the route belong to that competition.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $competition = $request->route('competition');

        if (!$competition instanceof Competition) {
            abort(404);
        }

        $user = Auth::user();

        if (!$user->isAdmin() && !$competition->officials()->whereKey($user->id)->exists()) {
            abort(403);
        }

        $serc = $request->route('serc');
        if ($serc instanceof SERC && $serc->competition != $competition->id) {
            abort(404);
        }

        $event = $request->route('event');
        if ($event instanceof CompetitionSpeedEvent && $event->competition != $competition->id) {
            abort(404);
        }

        $judge = $request->route('judge');
        if ($judge instanceof SERCJudge && (!$serc instanceof SERC || $judge->serc != $serc->id)) {
            abort(404);
        }

        $submission = $request->route('submission');
        if ($submission instanceof ViolationSubmission && $submission->competition_id != $competition->id) {
            abort(404);
        }

        return $next($request);
    }
}
