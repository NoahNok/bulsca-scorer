<?php

namespace App\Support;

use App\Helpers\RouteHelpers;
use App\Models\Competition;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Sentry\Logs\Log;
use Sentry\State\Scope;
use Throwable;

/**
 * Where an error or log happened and who hit it, for Sentry. IDs only: no names or emails leave the app.
 * See docs/error-tracking.md.
 */
class ErrorContext
{
    private const SUBDOMAINS = ['judge', 'app', 'live', 'results', 'stats', 'whatif'];

    /** @param Request|null $request null when not handling a request (a command or queued job) */
    public static function applyTo(Scope $scope, ?Request $request): void
    {
        $context = self::read($request);
        $userId = Arr::pull($context, 'user.id');

        $scope->setTags($context);

        if ($userId) {
            $scope->setUser(['id' => $userId]);
        }
    }

    /**
     * The same context as attributes on a log, so logs and errors filter the same way. Sentry's before_send_log.
     */
    public static function applyToLog(Log $log): Log
    {
        foreach (self::read(app()->runningInConsole() ? null : request()) as $key => $value) {
            $log->setAttribute($key, $value);
        }

        return $log;
    }

    /** @return array<string, string> */
    private static function read(?Request $request): array
    {
        $context = [];

        // Runs while reporting another error, perhaps with the database or session down: this context isn't worth
        // losing the report over
        try {
            $context['subdomain'] = $request ? self::subdomain($request) : 'console';

            if ($competition = $request ? self::competitionId($request) : null) {
                $context['competition'] = (string) $competition;
            }

            // hasUser, not user: this must not query the database, which may be why we're here
            $user = Auth::hasUser() ? Auth::user() : null;
            $context['user_role'] = self::role($user, $request);

            if ($user) {
                $context['user.id'] = (string) $user->getAuthIdentifier();
            }
        } catch (Throwable) {
            //
        }

        return $context;
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
