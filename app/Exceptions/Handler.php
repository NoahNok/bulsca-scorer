<?php

namespace App\Exceptions;

use App\Helpers\CompetitionHeader;
use App\Helpers\OrganisationHeader;
use App\Models\Competition;
use App\Models\Organisation\Organisation;
use App\Support\ErrorContext;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Inertia\Inertia;
use Sentry\Laravel\Integration;
use Sentry\State\Scope;
use Throwable;

use function Sentry\withScope;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            withScope(function (Scope $scope) use ($e) {
                ErrorContext::applyTo($scope, app()->runningInConsole() ? null : request());

                Integration::captureUnhandledException($e);
            });
        });
    }

    /**
     * A broken scoring formula fails on every view of its results, so report each distinct failure once an hour
     * instead of using up the error quota. See docs/error-tracking.md.
     */
    protected function throttle(Throwable $e)
    {
        if ($e instanceof ScoringException) {
            return Limit::perHour(1)->by('scoring:' . $e->getMessage());
        }

        return parent::throttle($e);
    }

    public function render($request, Throwable $exception)
    {



        if ($exception instanceof AuthorizationException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => "You don't have permission to do that",
                ], 403);
            }

            if ($request->is('comps/*') && $request->route('comp') instanceof Competition) {
                return Inertia::render('Manage/Competition/Forbidden', ['competition' => CompetitionHeader::for($request->route('comp'))])
                    ->toResponse($request)
                    ->setStatusCode(403);
            }

            if ($request->is('organisation/*')) {

                $org = $request->route('organisation');

                if (is_string($org)) {
                    $org = Organisation::where('name', $org)->firstOrFail();
                }

                return Inertia::render('Manage/Organisation/Forbidden', ['organisation' => OrganisationHeader::for($org)])
                    ->toResponse($request)
                    ->setStatusCode(403);
            }
        }

        $response = parent::render($request, $exception);

        // An Inertia visit would show the Blade 404 in a modal over the page it came from, so load it as a full page
        if ($response->getStatusCode() === 404 && $request->isMethod('GET') && $request->header('X-Inertia')) {
            return Inertia::location($request->fullUrl());
        }

        return $response;
    }
}
