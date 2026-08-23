<?php

namespace App\Http\Middleware\DigitalJudge\SERC;

use App\DigitalJudge\DigitalJudge;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MustHaveJudgeSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $judges = DigitalJudge::getClientJudgeIds();

        if (!$judges) {
            $competition = $request->route('competition');

            return redirect()->route('judge.competition', ['competition' => $competition]);
        }

        return $next($request);
    }
}
