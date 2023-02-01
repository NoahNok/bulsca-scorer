<?php

namespace App\Http\Middleware;

use App\Models\Competition;
use Closure;
use Illuminate\Http\Request;

class CanOnlyViewOwnComp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $compIdAttempted = $request->route('comp');



        $targetId = "";

        if ($compIdAttempted instanceof Competition) {
            $targetId = $compIdAttempted->id;
        } else {
            $targetId = $compIdAttempted;
        }

        if (auth()->user()->getCompetition->id == $targetId || auth()->user()->isAdmin()) return $next($request);

        return redirect('/');
    }
}
