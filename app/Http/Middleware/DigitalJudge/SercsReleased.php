<?php

namespace App\Http\Middleware\DigitalJudge;

use App\Models\Competition;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class SercsReleased
{
    /**
     * Keep officials out of SERC pages until the {competition}'s SERCs have been released, so
     * the criteria can't leak early. Referees are always let through. Run after judge.officiates.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $competition = $request->route('competition');

        if (!$competition instanceof Competition) {
            abort(404);
        }

        if ($competition->canSeeSercs(Auth::user())) {
            return $next($request);
        }

        if (!$request->isMethod('GET') || $request->expectsJson()) {
            abort(403, "SERCs haven't been released yet.");
        }

        Inertia::flash('toast', ['variant' => 'error', 'title' => "SERCs haven't been released yet"]);

        return to_route('judge.competition', ['competition' => $competition->getSlug()]);
    }
}
