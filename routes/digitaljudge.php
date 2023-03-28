<?php

use App\Http\Controllers\DigitalJudge\DigitalJudgeController;
use Illuminate\Support\Facades\Route;

Route::prefix('dj')->group(function () {
    Route::get('', [DigitalJudgeController::class, 'index'])->name('dj.index');
    Route::post('login', [DigitalJudgeController::class, 'login'])->name('dj.login');

    Route::middleware('canJudge')->group(function () {
        Route::get('home', [DigitalJudgeController::class, 'home'])->name('dj.home');
    });
});
