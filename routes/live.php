<?php

use App\Http\Controllers\LiveController;
use Illuminate\Support\Facades\Route;



function rrr()
{
    /* LIVE VIEWING */

    Route::get('', [LiveController::class, 'index'])->name('live');
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
