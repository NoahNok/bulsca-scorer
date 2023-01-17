<?php

namespace App\Http\Middleware;

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

        if (auth()->user()->getCompetition->id == $compIdAttempted->id) return $next($request);

        return redirect('/');
    }
}
