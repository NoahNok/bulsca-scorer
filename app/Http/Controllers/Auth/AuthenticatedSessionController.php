<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\RouteHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AuthenticatedSessionController extends Controller
{
    /**
     * The Management app's sign in page (Svelte).
     */
    public function create(): Response
    {
        return Inertia::render('Manage/Auth/SignIn');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): SymfonyResponse
    {
        $request->authenticate();

        // self-registered officials only have access to Digital Judge
        if ($request->user()->isJudgeOnly()) {
            Auth::guard('web')->logout();

            throw ValidationException::withMessages([
                'auth' => 'This account can only sign in to Digital Judge.',
            ]);
        }

        $request->session()->regenerate();

        // a full visit: the intended page may still be Blade, which an Inertia visit can't load
        return Inertia::location(redirect()->intended(RouteServiceProvider::HOME)->getTargetUrl());
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // back to the public site: / on app. is the Management home, which would ask them to sign in again
        return redirect()->away(RouteHelpers::mainRoute('home'));
    }
}
