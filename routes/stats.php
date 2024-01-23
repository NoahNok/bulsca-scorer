<?php

use App\Http\Controllers\PublicResultsController;
use App\Http\Controllers\PublicStatsController;
use Illuminate\Support\Facades\Route;



function rrstats()
{
    /* PUBLIC RESULTS VIEWING */




    Route::get('', [PublicStatsController::class, 'clubs'])->name('public.results.stats.clubs');
    Route::get('/clubs/{clubName}', [PublicStatsController::class, 'club'])->name('public.results.stats.club');
    Route::get('/clubs/{clubName}/{teamName}', [PublicStatsController::class, 'team'])->name('public.results.stats.club.team');
    Route::get('/compare/{team1}/{team2}', [PublicStatsController::class, 'compare'])->name('public.results.stats.compare');
}

if (env('APP_ENV') == 'local') {
    Route::prefix('stats')->group(function () {
        rrstats();
    });
} else {

    Route::domain('stats.bulsca.co.uk')->group(function () {
        rrstats();
    });
}
