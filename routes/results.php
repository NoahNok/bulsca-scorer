<?php

use App\Helpers\RouteHelpers;
use App\Http\Controllers\PublicResultsController;
use App\Models\Competition;
use Illuminate\Support\Facades\Route;

// The old results site. Its pages now live on the main site (a competition's Results and SERC
// breakdown), so old links and bookmarks are sent there. What's left here is the DQ and penalty
// details page, which the SERC breakdown links to, and the page shown while results aren't public.
Route::domain(RouteHelpers::domainRemap("results."))->group(function () {
    Route::get('', fn () => redirect()->away(RouteHelpers::mainRoute('explore'), 301))->name('public.results');
    Route::get('/{comp}/unavailable', function (Competition $comp) {
        return view('public-results.unavailable', ['message' => session('message'), 'comp' => $comp]);
    })->name('public.results.unavailable');
    Route::get('resolve/{date}/{name}', [PublicResultsController::class, 'resolve'])->name('public.results.resolve');

    $results = fn (Competition $comp) => redirect()->away(RouteHelpers::mainRoute('landing.competition.results', $comp->resultsSlug()), 301);

    Route::get('/{comp}', $results)->name("public.results.comp");
    Route::get('/{comp}/speed/{event}', $results)->name("public.results.speed");
    Route::get('/{comp}/results/{schema}', $results)->name("public.results.results");
    Route::get('/{comp}/serc/{event}', fn (Competition $comp, string $event) => redirect()->away(
        RouteHelpers::mainRoute('landing.competition.results.breakdown.serc', [$comp->resultsSlug(), $event]),
        301
    ))->name("public.results.serc");
    Route::get('/{comp}/serc/{event}/notes/{entity_id}', fn (Competition $comp, string $event, string $entity_id) => redirect()->away(
        RouteHelpers::mainRoute('landing.competition.results.notes.serc', [$comp->resultsSlug(), $event, $entity_id]),
        301
    ))->name("public.results.serc.entity-notes");

    Route::middleware('allowPublicResults')->group(function () {
        Route::get('/{comp}/dq-pen/{team}/{code}', [PublicResultsController::class, 'viewDqPen'])->name("public.results.dq-pen");
    });
});
