<?php

use App\Http\Controllers\PublicResultsController;
use App\Http\Controllers\PublicStatsController;
use Illuminate\Support\Facades\Route;



Route::domain("stats.".env('APP_SUBDOMAIN_BASE'))->group(function () {
    /* PUBLIC RESULTS VIEWING */




    Route::get('', [PublicStatsController::class, 'clubs'])->name('public.results.stats.clubs');
    Route::get('/clubs/{clubName}', [PublicStatsController::class, 'club'])->name('public.results.stats.club');
    Route::get('/clubs/{clubName}/{teamName}', [PublicStatsController::class, 'team'])->name('public.results.stats.club.team');
    Route::get('/compare/{team1}/{team2}', [PublicStatsController::class, 'compare'])->name('public.results.stats.compare');
});
