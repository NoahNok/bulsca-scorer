<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CanEditBrandSettings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // Check if user is a global admin
        if (Auth::user() && Auth::user()->isAdmin()) {
            return $next($request);
        }

        // Check if the current user is the current brand admin
        $brand = $request->route('brand');
        if ($brand->isBrandRole(Auth::user(), 'admin')) {

            return $next($request);
        }

        return redirect('/');
    }
}
