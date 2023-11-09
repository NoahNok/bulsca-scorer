<?php

namespace App\Http\Middleware\DigitalJudge;

use Closure;
use App\DigitalJudge\DigitalJudge;
use Illuminate\Http\Request;

class HeadJudgeOnly
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
        if (!DigitalJudge::isClientHeadJudge()) return redirect()->route('dj.index');



        return $next($request);
    }
}
