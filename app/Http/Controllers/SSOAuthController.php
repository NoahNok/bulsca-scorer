<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SSOAuthController extends Controller
{
    public function redirectToSSO()
    {
        if (!config('sso.enabled')) {
            return redirect('/login')->withErrors(['error' => 'SSO is not enabled']);
        }

        $state = Str::random(40);
        session(['sso_state' => $state]);

        $query = http_build_query([
            'client_id' => config('sso.client_id'),
            'redirect_uri' => config('sso.redirect_uri'),
            'state' => $state,
        ]);

        return redirect(config('sso.auth_server') . '/sso/authorize?' . $query);
    }

    public function handleCallback(Request $request)
    {
        // Verify state to prevent CSRF
        if ($request->state !== session('sso_state')) {
            Log::error('SSO state mismatch', [
                'expected' => session('sso_state'),
                'received' => $request->state
            ]);
            return redirect('/login')->withErrors(['error' => 'Invalid state parameter']);
        }

        if ($request->has('error')) {
            Log::error('SSO authorization error', ['error' => $request->error]);
            return redirect('/login')->withErrors(['error' => $request->error]);
        }

        try {
            // Exchange code for access token
            $tokenUrl = config('sso.auth_server') . '/sso/token';
            
            Log::info('Requesting SSO token', [
                'url' => $tokenUrl,
                'client_id' => config('sso.client_id'),
                'code' => $request->code,
            ]);

            $response = Http::timeout(10)->post($tokenUrl, [
                'grant_type' => 'authorization_code',
                'client_id' => config('sso.client_id'),
                'client_secret' => config('sso.client_secret'),
                'code' => $request->code,
            ]);

            Log::info('SSO token response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if (!$response->successful()) {
                Log::error('SSO token request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return redirect('/login')->withErrors(['error' => 'Failed to authenticate with SSO']);
            }

            $tokens = $response->json();

            // Validate token and get user info
            $userResponse = Http::timeout(10)->post(config('sso.auth_server') . '/sso/validate', [
                'access_token' => $tokens['access_token'],
                'client_id' => config('sso.client_id'),
                'client_secret' => config('sso.client_secret'),
            ]);

            if (!$userResponse->successful()) {
                Log::error('SSO validation failed', [
                    'status' => $userResponse->status(),
                    'body' => $userResponse->body(),
                ]);
                return redirect('/login')->withErrors(['error' => 'Failed to get user information']);
            }

            $userData = $userResponse->json()['user'];

            // Create or update user
            $user = User::updateOrCreate(
                ['sso_user_id' => $userData['id']],
                [
                    'email' => $userData['email'],
                    'name' => $userData['name'],
                    'auth_type' => 'sso',
                ]
            );

            // Store tokens in session
            session([
                'sso_access_token' => $tokens['access_token'],
                'sso_refresh_token' => $tokens['refresh_token'],
            ]);

            // Log user in
            Auth::login($user);

            Log::info('SSO login successful', ['user_id' => $user->id]);

            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            Log::error('SSO callback exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect('/login')->withErrors(['error' => 'Authentication failed. Please try again.']);
        }
    }
}