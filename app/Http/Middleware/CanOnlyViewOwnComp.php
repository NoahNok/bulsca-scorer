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


        /** @var User $user */
        $user = auth()->user();

        if ($user->isAdmin() || ($user->competition && $user->getCompetition->id == $targetId)) return $next($request);

        // Check if user can view the competition because they are a brand account
        $targetCompetition = Competition::find($targetId);
        if ($targetCompetition->brand && $targetCompetition->getBrand->isBrandRole($user, ['admin', 'welfare'])) return $next($request);

        return redirect('/');
    }
}
