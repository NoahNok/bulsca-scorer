<?php

use App\Http\Controllers\PublicResultsController;
use App\Http\Controllers\PublicStatsController;
use Illuminate\Support\Facades\Route;




Route::domain("results.".env('APP_SUBDOMAIN_BASE'))->group(function () {
    Route::get('', [PublicResultsController::class, 'index'])->name('public.results');
    Route::get('resolve/{date}/{name}', [PublicResultsController::class, 'resolve'])->name('public.results.resolve');
    Route::get('/{comp_slug}', [PublicResultsController::class, 'viewComp'])->name("public.results.comp");
    Route::get('/{comp_slug}/speed/{event}', [PublicResultsController::class, 'viewSpeed'])->name("public.results.speed");
    Route::get('/{comp_slug}/serc/{event}', [PublicResultsController::class, 'viewSerc'])->name("public.results.serc");
    Route::get('/{comp_slug}/serc/{event}/notes/{team}', [PublicResultsController::class, 'viewTeamSercNotes'])->name("public.results.serc.team-notes");
    Route::get('/{comp_slug}/results/{schema}', [PublicResultsController::class, 'viewResults'])->name("public.results.results");
    Route::get('/{comp}/dq-pen/{team}/{code}', [PublicResultsController::class, 'viewDqPen'])->name("public.results.dq-pen");
});

