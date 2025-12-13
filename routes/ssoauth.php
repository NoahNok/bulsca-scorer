<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SSOAuthController;

// SSO routes
Route::get('/auth/sso', [SSOAuthController::class, 'redirectToSSO'])->name('auth.sso');
Route::get('/auth/sso/callback', [SSOAuthController::class, 'handleCallback'])->name('auth.sso.callback');