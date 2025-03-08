<?php

use App\DigitalJudge\DigitalJudge;
use App\Helpers\RouteHelpers;
use App\Http\Controllers\DigitalJudge\DigitalJudgeController;
use App\Http\Controllers\DigitalJudge\DJDQController;
use App\Http\Controllers\DigitalJudge\DJJudgingController;
use App\Http\Controllers\DigitalJudge\DJManageController;
use App\Http\Controllers\DigitalJudge\SpeedJudgingController;
use Illuminate\Support\Facades\Route;



Route::domain(RouteHelpers::domainRemap("judge."))->group(function () {
    Route::get('', [DigitalJudgeController::class, 'index'])->name('dj.index');
    Route::post('login', [DigitalJudgeController::class, 'login'])->name('dj.login');
    Route::get('logout', [DigitalJudgeController::class, 'logout'])->name('dj.logout');

    Route::middleware('canJudge')->group(function () {
        Route::get('home', [DigitalJudgeController::class, 'home'])->name('dj.home');
        Route::get('help', [DigitalJudgeController::class, 'help'])->name('dj.help');

        Route::prefix('judging/{judge}')->group(function () {
            Route::get('confirm-judge', [DJJudgingController::class, 'confirmJudge'])->name('dj.judging.confirm-judge');
            Route::post('confirm-judge', [DJJudgingController::class, 'confirmJudgePost'])->name('dj.judging.confirm');
        });

        Route::prefix('judging')->group(function () {

            Route::get('tank', [DJJudgingController::class, 'selectTank'])->name('dj.judging.tank');
            Route::get('tank/{tank}', [DJJudgingController::class, 'setTank'])->name('dj.judging.tank.set');

            Route::get('home', [DJJudgingController::class, 'home'])->name('dj.judging.home');

            Route::get('add-judge', [DJJudgingController::class, 'addJudge'])->name('dj.judging.add-judge');
            Route::post('add-judge', [DJJudgingController::class, 'addJudgePost'])->name('dj.judging.add-judge.post');

            Route::get('remove-judge', [DJJudgingController::class, 'removeJudge'])->name('dj.judging.remove-judge');
            Route::post('remove-judge', [DJJudgingController::class, 'removeJudgePost'])->name('dj.judging.remove-judge.post');


            Route::get('team/next', [DJJudgingController::class, 'nextTeamForJudge'])->name('dj.judging.next-team');

            Route::get('team/{team}', [DJJudgingController::class, 'judgeTeam'])->name('dj.judging.judge-team');
            Route::post('team/{team}', [DJJudgingController::class, 'saveTeamScores'])->name('dj.judging.save-team-scores');

            Route::get('tutorial', [DJJudgingController::class, 'tutorial'])->name('dj.judging.tutorial');
            Route::post('tutorial', [DJJudgingController::class, 'tutorialPost'])->name('dj.judging.tutorial.post');


            Route::get('previous-marks', [DJJudgingController::class, 'previousMarks'])->name('dj.judging.previous-marks');

            Route::get('overall-comments', [DJJudgingController::class, 'overallComments'])->name('dj.judging.overall-comments');
            Route::post('overall-comments', [DJJudgingController::class, 'overallCommentsPost'])->name('dj.judging.overall-comments.post');
        });
        Route::get('change-judge', [DJJudgingController::class, 'changeJudge'])->name('dj.changeJudge');



        Route::prefix('speeds/{speed}')->group(function () {

            Route::prefix('times')->group(function () {
                Route::get('', [SpeedJudgingController::class, 'timesIndex'])->name('dj.speeds.times.index');
                Route::get('/h/{heat}', [SpeedJudgingController::class, 'timesJudge'])->name('dj.speeds.times.judge');
                Route::post('/h/{heat}', [SpeedJudgingController::class, 'saveHeatTimes'])->name('dj.speeds.times.judgePost');
            });




            Route::prefix('oof')->group(function () {
                Route::get('', [SpeedJudgingController::class, 'oofIndex'])->name('dj.speeds.oof.index');
                Route::get('/h/{heat}', [SpeedJudgingController::class, 'oofJudge'])->name('dj.speeds.oof.judge');
                Route::post('/h/{heat}', [SpeedJudgingController::class, 'saveOofTimes'])->name('dj.speeds.oof.judgePost');
            });
        });
    });

    Route::prefix('dq')->group(function () {
        Route::get('issue', [DJDQController::class, 'issue'])->name('dj.dq.issue');
        Route::get('resolveCode/{code}', [DJDQController::class, 'resolveCode'])->name('dj.dq.resolveCode');

        Route::post('submission', [DJDQController::class, 'submission'])->name('dj.dq.submission');
        Route::get('submission/{submission}/info', [DJDQController::class, 'getSubmission'])->name('dj.dq.submission.info');
        Route::get('submission/{submission}/status', [DJDQController::class, 'submissionStatus'])->name('dj.dq.submission.status');

        Route::get('event-codes/{event}', [DJDQController::class, 'getEventRelatedCodes'])->name('dj.dq.event-codes');
    });

    Route::middleware('isHeadJudge')->group(function () {
        Route::prefix('manage')->group(function () {
            Route::get('', [DJManageController::class, 'index'])->name('dj.manage.index');
            Route::get('/serc/{serc}', [DJManageController::class, 'manageSerc'])->name('dj.manage.serc');
            Route::post('/serc/{serc}', [DJManageController::class, 'manageSercPost'])->name('dj.manage.serc.post');
            Route::get('/speed/{speed}', [DJManageController::class, 'manageSpeed'])->name('dj.manage.speed');
        });

        Route::prefix('dq')->group(function () {
            Route::get('', [DJDQController::class, 'index'])->name('dj.dq.index');
            Route::get('/current/{event}/{team}/{type}', [DJDQController::class, 'current'])->name('dj.dq.current');
            Route::post('', [DJDQController::class, 'submit'])->name('dj.dq.index.post');
            Route::Get('/resolve', [DJDQController::class, 'resolve'])->name('dj.dq.resolve');
            Route::Get('/resolve/list', [DJDQController::class, 'getNeedingResolving'])->name('dj.dq.resolve.list');
            Route::post('/resolve/{submission}', [DJDQController::class, 'resolveSubmission'])->name('dj.dq.submission.resolve');
            Route::get('/accepted', [DJDQController::class, 'getAccepted'])->name('dj.dq.accepted');
            Route::post('/remove/{submission}', [DJDQController::class, 'removeSubmission'])->name('dj.dq.remove');
            Route::post('/appeal/{submission}', [DJDQController::class, 'appealSubmission'])->name('dj.dq.appeal');
        });

        Route::prefix('confirm')->group(function () {
            Route::get('serc/{serc}', [DigitalJudgeController::class, 'confirmResults'])->name('dj.confirm-results');
            Route::post('serc/{serc}', [DigitalJudgeController::class, 'confirmResultsPost'])->name('dj.confirm-results.post');

            Route::get('speed/{speed}', [DigitalJudgeController::class, 'confirmSpeedResults'])->name('dj.confirm-results.speed');
            Route::post('speed/{speed}', [DigitalJudgeController::class, 'confirmSpeedResultsPost'])->name('dj.confirm-results.speed.post');
        });
    });



    if (env('APP_ENV') == 'local') {
        Route::get('toggle-head-ref', function () {
            DigitalJudge::setClientHeadJudge(!DigitalJudge::isClientHeadJudge());
            return redirect()->back();
        })->name('LOCAL.dj.toggle-head-ref');
    }
});
