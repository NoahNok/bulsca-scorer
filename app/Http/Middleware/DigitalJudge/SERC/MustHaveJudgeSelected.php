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

        $judges = DigitalJudge::getClientJudges();
        $serc = $request->route('serc');

        // the judges have to be for the SERC in the url, e.g. not left over from another SERC
        // opened from a QR code, so the marking points and draw shown match the SERC being marked
        if ($judges->isEmpty() || $judges->contains(fn($judge) => $judge->serc != $serc?->id)) {
            DigitalJudge::clearClientJudges();

            $competition = $request->route('competition');

            return redirect()->route('judge.competition', ['competition' => $competition->getSlug()]);
        }

        return $next($request);
    }
}
