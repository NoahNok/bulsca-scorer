<?php

namespace App\Http\Middleware;

use App\DigitalJudge\DigitalJudge;
use App\Helpers\RouteHelpers;
use App\Models\Competition;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $shared = [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user()
            ],
            'env_local' => app()->environment('local'),
            // the policies are Blade pages on the main domain, so these are full urls to it
            'legal' => [
                'terms' => route('legal.terms'),
                'privacy' => route('legal.privacy'),
                'cookies' => route('legal.cookies'),
                'tradingAs' => config('legal.trading_as'),
                'contactEmail' => config('legal.contact_email'),
            ],
        ];

        if ($this->isManagementApp($request)) {
            return [
                ...$shared,
                'manage' => [
                    'isAdmin' => (bool) $request->user()?->isAdmin(),
                    // the public site is on the main domain, and route() would build these on app.
                    'site' => [
                        'home' => RouteHelpers::mainRoute('home'),
                        'explore' => RouteHelpers::mainRoute('explore'),
                    ],
                    // the Blade pages' flashes (password reset status, invite errors), shown as alerts
                    'flash' => [
                        'status' => $request->session()->get('status'),
                        'error' => $request->session()->get('alert-error'),
                    ],
                ],
            ];
        }

        $competition = $request->route('competition');

        return [
            ...$shared,
            'judge' => [
                'isHeadRef' => $competition instanceof Competition ? DigitalJudge::isClientHeadJudge($competition) : false
            ],
        ];
    }

    /**
     * The Management app (Management and Admin pages, resources/js/manage.ts) has its own root view
     * and shared props, separate from the Digital Judge. See docs/adr/0002-public-blade-management-svelte.md.
     */
    public function rootView(Request $request): string
    {
        return $this->isManagementApp($request) ? 'manage' : $this->rootView;
    }

    private function isManagementApp(Request $request): bool
    {
        return $request->getHost() === RouteHelpers::domainRemap('app.');
    }
}
