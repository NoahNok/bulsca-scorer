<?php

namespace App\Http\Middleware;

use App\Helpers\RouteHelpers;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotJudgeOnly
{
    /**
     * Keep self-registered officials (judge_only) out of the main site by
     * sending them to Digital Judge instead.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isJudgeOnly()) {
            return redirect()->away(RouteHelpers::externalRoute('judge', 'judge.index'));
        }

        return $next($request);
    }
}
