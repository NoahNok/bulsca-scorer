<?php

use App\Http\Controllers\LiveController;
use Illuminate\Support\Facades\Route;



Route::domain("live.".env('APP_SUBDOMAIN_BASE'))->group(function () {
    /* LIVE VIEWING */

    Route::get('', [LiveController::class, 'index'])->name('live');
    Route::get('dqs', [LiveController::class, 'dqs'])->name('live.dqs');
    Route::get('dqs/{event}', [LiveController::class, 'eventDqs'])->name('live.dqs.event');
    Route::get('{comp}', [LiveController::class, 'liveData'])->name('live.data')->middleware('throttle:30,1');
});

