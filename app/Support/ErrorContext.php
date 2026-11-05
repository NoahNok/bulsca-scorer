<?php

namespace App\Support;

use App\Helpers\RouteHelpers;
use App\Models\Competition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sentry\State\Scope;
use Throwable;

/**
 * Where an error happened and who hit it, as Sentry tags. IDs only: no names or emails leave the app.
 * See docs/error-tracking.md.
 */
class ErrorContext
{
    private const SUBDOMAINS = ['judge', 'app', 'live', 'results', 'stats', 'whatif'];

    /** @param Request|null $request null when not handling a request (a command or queued job) */
    public static function applyTo(Scope $scope, ?Request $request): void
    {
        // Runs while reporting another error, perhaps with the database or session down: tags aren't worth losing
        // the report over
        try {
            $scope->setTag('subdomain', $request ? self::subdomain($request) : 'console');

            if ($competition = $request ? self::competitionId($request) : null) {
                $scope->setTag('competition', (string) $competition);
            }

            // hasUser, not user: this must not query the database, which may be why we're here
            $user = Auth::hasUser() ? Auth::user() : null;
            $scope->setTag('user_role', self::role($user, $request));

            if ($user) {
                $scope->setUser(['id' => $user->getAuthIdentifier()]);
            }
        } catch (Throwable) {
            //
        }
    }

    private static function subdomain(Request $request): string
    {
        foreach (self::SUBDOMAINS as $subdomain) {
            if ($request->getHost() === RouteHelpers::domainRemap($subdomain . '.')) {
                return $subdomain;
            }
        }

        return 'main';
    }

    private static function competitionId(Request $request): int|string|null
    {
        // Management names the route parameter comp, the Digital Judge competition
        foreach (['comp', 'competition'] as $parameter) {
            $competition = $request->route($parameter);

            if ($competition instanceof Competition) {
                return $competition->id;
            }
        }

        return $request->hasSession() ? $request->session()->get('digitalJudgeClientComp') : null;
    }

    private static function role($user, ?Request $request): string
    {
        if ($user) {
            return $user->isAdmin() ? 'admin' : 'user';
        }

        return $request?->hasSession() && $request->session()->get('digitalJudgeClientAllowed') ? 'judge' : 'guest';
    }
}
