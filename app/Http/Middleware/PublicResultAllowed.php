<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PublicResultAllowed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {


        $comp = $request->route('comp_slug');

        if (!$comp->areResultsPublic()) {
            return redirect()->route('public.results.unavailable', ['comp' => $comp])->with('message', "$comp->name results are not currently available");
        }


        return $next($request);
    }
}
