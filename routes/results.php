<?php

use App\Helpers\RouteHelpers;
use App\Http\Controllers\PublicResultsController;
use App\Http\Controllers\PublicStatsController;
use App\Models\Competition;
use Illuminate\Support\Facades\Route;


Route::domain(RouteHelpers::domainRemap("results."))->group(function () {
    Route::get('', [PublicResultsController::class, 'index'])->name('public.results');
    Route::get('/{comp}/unavailable', function (Competition $comp) {
        return view('public-results.unavailable', ['message' => session('message'), 'comp' => $comp]);
    })->name('public.results.unavailable');
    Route::get('resolve/{date}/{name}', [PublicResultsController::class, 'resolve'])->name('public.results.resolve');


    Route::middleware('allowPublicResults')->group(function () {

        Route::get('/{comp}', [PublicResultsController::class, 'viewComp'])->name("public.results.comp");
        Route::get('/{comp}/speed/{event}', [PublicResultsController::class, 'viewSpeed'])->name("public.results.speed");
        Route::get('/{comp}/serc/{event}', [PublicResultsController::class, 'viewSerc'])->name("public.results.serc");
        Route::get('/{comp}/serc/{event}/notes/{entity_id}', [PublicResultsController::class, 'viewEntitySercNotes'])->name("public.results.serc.entity-notes");
        Route::get('/{comp}/results/{schema}', [PublicResultsController::class, 'viewResults'])->name("public.results.results");
        Route::get('/{comp}/dq-pen/{team}/{code}', [PublicResultsController::class, 'viewDqPen'])->name("public.results.dq-pen");
    });
});
