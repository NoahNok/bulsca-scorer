<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\OverallResultsController;
use App\Http\Controllers\PublicResultsController;
use App\Http\Controllers\SpeedsEventController;
use App\Http\Controllers\TeamsController;
use App\Http\Controllers\SERCController;
use App\Models\Competition;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('auth')->group(function () {
    Route::get('/', function () {

        if (!auth()->user()->isAdmin() && auth()->user()->getCompetition) {
            return redirect()->route('comps.view', auth()->user()->getCompetition);
        }

        return view('welcome');
    })->name('home');

    Route::get('/comps', [CompetitionController::class, 'index'])->name('comps');

    Route::middleware('onlyViewOwnComp')->group(function () {
        Route::get('/comps/{comp}', [CompetitionController::class, 'view'])->name('comps.view');

        Route::get('/comps/{comp}/events', [CompetitionController::class, 'events'])->name('comps.view.events');
        Route::get('/comps/{comp}/events/speeds/add', [SpeedsEventController::class, 'add'])->name('comps.view.events.speeds.add');
        Route::post('/comps/{comp}/events/speeds/add', [SpeedsEventController::class, 'addPost'])->name('comps.view.events.speeds.addPost');
        Route::delete('/comps/{comp}/events/speeds/{event}/delete', [SpeedsEventController::class, 'delete'])->name('comps.view.events.speeds.delete');

        Route::get('/comps/{comp}/events/speeds/{event}', [SpeedsEventController::class, 'view'])->name('comps.view.events.speeds.view');
        Route::get('/comps/{comp}/events/speeds/{event}/edit', [SpeedsEventController::class, 'edit'])->name('comps.view.events.speeds.edit');
        Route::post('/comps/{comp}/events/speeds/{event}/edit', [SpeedsEventController::class, 'updateResults'])->name('comps.view.events.speeds.editPost');

        Route::get('/comps/{comp}/teams', [CompetitionController::class, 'teams'])->name('comps.view.teams');
        Route::get('/comps/{comp}/teams/edit', [TeamsController::class, 'edit'])->name('comps.view.teams.edit');
        Route::post('/comps/{comp}/teams/edit', [TeamsController::class, 'editPost'])->name('comps.view.teams.editPost');
        Route::delete('/comps/{comp}/teams/delete', [TeamsController::class, 'delete'])->name('comps.view.teams.delete');


        Route::get('/comps/{comp}/events/sercs/add', [SERCController::class, 'add'])->name('comps.view.events.sercs.add');
        Route::post('/comps/{comp}/events/sercs/add', [SERCController::class, 'addPost'])->name('comps.view.events.sercs.addPost');

        Route::get('/comps/{comp}/events/sercs/{serc}', [SERCController::class, 'view'])->name('comps.view.events.sercs.view');

        Route::get('/comps/{comp}/events/sercs/{serc}/edit', [SERCController::class, 'edit'])->name('comps.view.events.sercs.edit');
        Route::post('/comps/{comp}/events/sercs/{serc}/edit', [SERCController::class, 'editPost'])->name('comps.view.events.sercs.editPost');
        Route::delete('/comps/{comp}/events/sercs/{serc}', [SERCController::class, 'delete'])->name('comps.view.events.sercs.delete');

        Route::get('/comps/{comp}/events/sercs/{serc}/results/{team}/edit', [SERCController::class, 'editResultsView'])->name('comps.view.events.sercs.editResults');
        Route::post('/comps/{comp}/events/sercs/{serc}/results/{team}/edit', [SERCController::class, 'updateTeamResults'])->name('comps.view.events.sercs.editResultsPost');

        Route::get('/comp/{comp}/results', [OverallResultsController::class, 'view'])->name('comps.view.results');
        Route::get('/comp/{comp}/results/add', [OverallResultsController::class, 'add'])->name('comps.view.results.add');
        Route::get('/comp/{comp}/results/qg', [OverallResultsController::class, 'quickGen'])->name('comps.view.results.quickGen');
        Route::get('/comp/{comp}/results/pt', [OverallResultsController::class, 'publishToggle'])->name('comps.view.results.publishToggle');
        Route::post('/comp/{comp}/results', [OverallResultsController::class, 'addPost'])->name('comps.view.results.addPost');
        Route::delete('/comp/{comp}/results/{schema}', [OverallResultsController::class, 'delete'])->name('comps.view.results.delete');
    });

    Route::get('/comp/results/view-schema/{schema}', [OverallResultsController::class, 'computeResults'])->name("comps.results.view-schema");
    Route::get('/comp/results/view-schema/{schema}/print', [OverallResultsController::class, 'viewForPrint'])->name("comps.results.view-schema-print");
    Route::get('/comp/results/view-schema/{schema}/print-basic', [OverallResultsController::class, 'viewForPrintBasic'])->name("comps.results.view-schema-print-basic");
});


Route::middleware('isAdmin')->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/competition/create', [AdminController::class, 'createComp'])->name('admin.comp.create');
    Route::get('/admin/competition/{comp}', [AdminController::class, 'viewComp'])->name('admin.comp.view');
    Route::post('/admin/competition/create', [AdminController::class, 'createCompPost'])->name('admin.comp.create.post');
    Route::post('/admin/competition/{comp}/update', [AdminController::class, 'updateCompPost'])->name('admin.comp.update.post');
    Route::post('/admin/competition/{comp}/updateUser', [AdminController::class, 'updateCompUserPassword'])->name('admin.comp.update.userPassword');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('dashboard', function () {
    return redirect()->route('comps.view', auth()->user()->getCompetition);
});

Route::bind('comp_slug', function ($value) {

    $parts = explode(".", $value);

    if (count($parts) < 2) abort(404);

    $id = $parts[1];

    return Competition::findOrFail($id);
});

/* PUBLIC RESULTS VIEWING */
Route::prefix('results')->group(function () {
    Route::get('', [PublicResultsController::class, 'index'])->name('public.results');
    Route::get('/{comp_slug}', [PublicResultsController::class, 'viewComp'])->name("public.results.comp");
    Route::get('/{comp_slug}/speed/{event}', [PublicResultsController::class, 'viewSpeed'])->name("public.results.speed");
});

require __DIR__ . '/auth.php';
