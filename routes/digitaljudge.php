<?php

use App\Http\Controllers\DigitalJudge\DigitalJudgeController;
use App\Http\Controllers\DigitalJudge\DJJudgingController;
use App\Http\Controllers\DigitalJudge\SpeedJudgingController;
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
        });

        Route::prefix('judging')->group(function () {
            Route::get('home', [DJJudgingController::class, 'home'])->name('dj.judging.home');

            Route::get('add-judge', [DJJudgingController::class, 'addJudge'])->name('dj.judging.add-judge');
            Route::post('add-judge', [DJJudgingController::class, 'addJudgePost'])->name('dj.judging.add-judge.post');

            Route::get('remove-judge', [DJJudgingController::class, 'removeJudge'])->name('dj.judging.remove-judge');
            Route::post('remove-judge', [DJJudgingController::class, 'removeJudgePost'])->name('dj.judging.remove-judge.post');


            Route::get('team/next', [DJJudgingController::class, 'nextTeamForJudge'])->name('dj.judging.next-team');

            Route::get('team/{team}', [DJJudgingController::class, 'judgeTeam'])->name('dj.judging.judge-team');
            Route::post('team/{team}', [DJJudgingController::class, 'saveTeamScores'])->name('dj.judging.save-team-scores');
        });
        Route::get('change-judge', [DJJudgingController::class, 'changeJudge'])->name('dj.changeJudge');

        Route::get('{serc}/confirm-results', [DigitalJudgeController::class, 'confirmResults'])->name('dj.confirm-results');
        Route::post('{serc}/confirm-results', [DigitalJudgeController::class, 'confirmResultsPost'])->name('dj.confirm-results.post');


        Route::prefix('speeds/{speed}')->group(function () {
            Route::get('times', [SpeedJudgingController::class, 'timesIndex'])->name('dj.speeds.times.index');
            Route::get('times/h/{heat}', [SpeedJudgingController::class, 'timesJudge'])->name('dj.speeds.times.judge');
            Route::post('times/h/{heat}', [SpeedJudgingController::class, 'saveHeatTimes'])->name('dj.speeds.times.judgePost');
        });
    });
});
