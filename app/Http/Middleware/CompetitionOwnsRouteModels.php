<?php

namespace App\Http\Middleware;

use App\Models\Competition;
use App\Models\SERC;
use App\Models\SERCJudge;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompetitionOwnsRouteModels
{
    /**
     * The access checks only look at the {comp} in the url, so make sure every other model bound in
     * the route (SERCs, speed events, leagues, result schemas, ...) belongs to that competition.
     * Otherwise an organiser could change another competition's events through their own url.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $comp = $request->route('comp');

        if (!$comp instanceof Competition) {
            return $next($request);
        }

        foreach ($request->route()->parameters() as $model) {
            if (!$model instanceof Model || $model instanceof Competition) {
                continue;
            }

            // users ({account}, {official}) are checked against the competition in their controllers
            if (array_key_exists('competition', $model->getAttributes()) && $model->competition != $comp->id) {
                abort(404);
            }
        }

        $judge = $request->route('judge');
        $serc = $request->route('serc');

        if ($judge instanceof SERCJudge && (!$serc instanceof SERC || $judge->serc != $serc->id)) {
            abort(404);
        }

        return $next($request);
    }
}
