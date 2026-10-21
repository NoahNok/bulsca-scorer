<?php

namespace App\Http\Middleware;

use App\Models\Competition;
use App\Models\CompetitionVisit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordCompetitionVisit
{
    /**
     * Remembers when the user last opened one of the competition's pages, for "Recent" on the
     * Management home. Only full page loads they were allowed to see count, not forms or fetches.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $comp = $request->route('comp');

        // Inertia visits are XHR but are page loads all the same; prefetches, partial reloads and downloads aren't
        $inertia = $request->hasHeader('X-Inertia') && !$request->hasHeader('X-Inertia-Partial-Data');
        $html = !$request->expectsJson() && str_contains((string) $response->headers->get('Content-Type'), 'text/html');
        $pageLoad = $request->isMethod('GET') && $request->header('Purpose') !== 'prefetch' && ($inertia || $html);

        if ($pageLoad && $response->isSuccessful()
            && $comp instanceof Competition && $request->user()) {
            CompetitionVisit::upsert(
                [['user_id' => $request->user()->id, 'competition_id' => $comp->id, 'visited_at' => now()]],
                ['user_id', 'competition_id'],
                ['visited_at'],
            );
        }

        return $response;
    }
}
