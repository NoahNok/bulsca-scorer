<?php

use App\Http\Controllers\DigitalJudge\DigitalJudgeController;
use App\Http\Controllers\DigitalJudge\DJJudgingController;
use Illuminate\Support\Facades\Route;

Route::prefix('dj')->group(function () {
    Route::get('', [DigitalJudgeController::class, 'index'])->name('dj.index');
    Route::post('login', [DigitalJudgeController::class, 'login'])->name('dj.login');
    Route::get('logout', [DigitalJudgeController::class, 'logout'])->name('dj.logout');

    Route::middleware('canJudge')->group(function () {
        Route::get('home', [DigitalJudgeController::class, 'home'])->name('dj.home');

        Route::prefix('judging/{judge}')->group(function () {
            Route::get('confirm-judge', [DJJudgingController::class, 'confirmJudge'])->name('dj.judging.confirm-judge');
            Route::post('confirm-judge', [DJJudgingController::class, 'confirmJudgePost'])->name('dj.judging.confirm');
            Route::get('home', [DJJudgingController::class, 'home'])->name('dj.judging.home');

            Route::get('team/next', [DJJudgingController::class, 'nextTeamForJudge'])->name('dj.judging.next-team');

            Route::get('team/{team}', [DJJudgingController::class, 'judgeTeam'])->name('dj.judging.judge-team');
            Route::post('team/{team}', [DJJudgingController::class, 'saveTeamScores'])->name('dj.judging.save-team-scores');
        });
        Route::get('change-judge', [DJJudgingController::class, 'changeJudge'])->name('dj.changeJudge');
    });
});
