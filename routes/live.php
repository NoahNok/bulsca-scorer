<?php

use App\Http\Controllers\LiveController;
use Illuminate\Support\Facades\Route;



function rrr()
{
    /* LIVE VIEWING */

    Route::get('', [LiveController::class, 'index'])->name('live');
    Route::get('{comp}/howManySercsHasEachTeamFinished', [LiveController::class, 'howManySercsHasEachTeamFinished'])->name('live.howManySercsHasEachTeamFinished')->middleware('throttle:30,1');
}

if (env('APP_ENV') == 'local') {
    Route::prefix('live')->group(function () {
        rrr();
    });
} else {

    Route::domain('live.bulsca.co.uk')->group(function () {
        rrr();
    });
}
