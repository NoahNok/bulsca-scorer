<?php

namespace App\Http\Middleware;

use App\Helpers\RouteHelpers;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class SetAppUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (RouteHelpers::isCustomHost()) {
            Config::set('app.url', $request->getScheme() . '://' . $request->getHost());
        }

        return $next($request);
    }
}
