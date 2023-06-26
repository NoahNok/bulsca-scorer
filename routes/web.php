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
use App\Http\Controllers\DigitalJudge\DigitalJudgeController;
use App\Http\Controllers\HeatController;
use App\Http\Controllers\OverallResultsController;
use App\Http\Controllers\PublicResultsController;
use App\Http\Controllers\SpeedsEventController;
use App\Http\Controllers\TeamsController;
use App\Http\Controllers\SERCController;
use App\Models\Competition;
use App\Models\DQCode;

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
        // @php-ignore
        if (!auth()->user()->isAdmin() && auth()->user()->getCompetition) {
            return redirect()->route('comps.view', auth()->user()->getCompetition);
        }

        return view('welcome');
    })->name('home');

    Route::get('/comps', [CompetitionController::class, 'index'])->name('comps');

    Route::middleware('onlyViewOwnComp')->group(function () {

        Route::prefix('/comps/{comp}')->group(function () {
            Route::get('', [CompetitionController::class, 'view'])->name('comps.view');
            Route::get('/digital-judge-toggle', [DigitalJudgeController::class, 'toggle'])->name('dj.toggle');
            Route::get('/judge-log', [DigitalJudgeController::class, 'judgeLog'])->name('dj.judgeLog');


            // EVENTS
            Route::prefix('/events')->group(function () {

                Route::get('', [CompetitionController::class, 'events'])->name('comps.view.events');

                // SPEEDS
                Route::prefix('/speeds')->group(function () {
                    Route::get('/add', [SpeedsEventController::class, 'add'])->name('comps.view.events.speeds.add');
                    Route::post('/add', [SpeedsEventController::class, 'addPost'])->name('comps.view.events.speeds.addPost');
                    Route::delete('/{event}/delete', [SpeedsEventController::class, 'delete'])->name('comps.view.events.speeds.delete');

                    Route::get('/{event}', [SpeedsEventController::class, 'view'])->name('comps.view.events.speeds.view');
                    Route::get('/{event}/edit', [SpeedsEventController::class, 'edit'])->name('comps.view.events.speeds.edit');
                    Route::post('/{event}/edit', [SpeedsEventController::class, 'updateResults'])->name('comps.view.events.speeds.editPost');
                });

                // SERCS
                Route::get('/sercs/add', [SERCController::class, 'add'])->name('comps.view.events.sercs.add');
                Route::post('/sercs/add', [SERCController::class, 'addPost'])->name('comps.view.events.sercs.addPost');
                Route::prefix('/sercs/{serc}')->group(function () {



                    Route::get('', [SERCController::class, 'view'])->name('comps.view.events.sercs.view');

                    Route::get('/edit', [SERCController::class, 'edit'])->name('comps.view.events.sercs.edit');
                    Route::post('/edit', [SERCController::class, 'editPost'])->name('comps.view.events.sercs.editPost');
                    Route::delete('', [SERCController::class, 'delete'])->name('comps.view.events.sercs.delete');

                    Route::get('/results/{team}/edit', [SERCController::class, 'editResultsView'])->name('comps.view.events.sercs.editResults');
                    Route::post('/results/{team}/edit', [SERCController::class, 'updateTeamResults'])->name('comps.view.events.sercs.editResultsPost');

                    Route::get('/digital-judge-toggle', [DigitalJudgeController::class, 'sercToggle'])->name('dj.sercToggle');
                });
            });


            // TEAMS
            Route::prefix('/teams')->group(function () {
                Route::get('', [CompetitionController::class, 'teams'])->name('comps.view.teams');
                Route::get('/edit', [TeamsController::class, 'edit'])->name('comps.view.teams.edit');
                Route::post('/edit', [TeamsController::class, 'editPost'])->name('comps.view.teams.editPost');
                Route::delete('/delete', [TeamsController::class, 'delete'])->name('comps.view.teams.delete');
            });

            // RESULTS
            Route::prefix('/results')->group(function () {
                Route::get('', [OverallResultsController::class, 'view'])->name('comps.view.results');
                Route::get('/add', [OverallResultsController::class, 'add'])->name('comps.view.results.add');
                Route::get('/qg', [OverallResultsController::class, 'quickGen'])->name('comps.view.results.quickGen');
                Route::get('/pt', [OverallResultsController::class, 'publishToggle'])->name('comps.view.results.publishToggle');
                Route::get('/prt', [OverallResultsController::class, 'provToggle'])->name('comps.view.results.provToggle');
                Route::post('', [OverallResultsController::class, 'addPost'])->name('comps.view.results.addPost');
                Route::delete('/{schema}', [OverallResultsController::class, 'delete'])->name('comps.view.results.delete');
                Route::get('/{schema}/hide', [OverallResultsController::class, 'hide'])->name('comps.view.results.hide');
            });

            // HEATS AND SERC ORDER
            Route::prefix('/heats-and-orders')->group(function () {

                Route::get('', [HeatController::class, 'index'])->name('comps.view.heats');

                Route::prefix('/heats')->group(function () {
                    Route::get('/edit', [HeatController::class, 'edit'])->name('comps.view.heats.edit');
                    Route::post('/edit', [HeatController::class, 'editPost'])->name('comps.view.heats.editPost');
                    Route::get('/gen', [HeatController::class, 'createDefaultHeatsForComp'])->name('comps.view.heats.gen');
                });
                Route::prefix('/serc-order')->group(function () {
                    Route::get('/edit', [HeatController::class, 'editSERCOrder'])->name('comps.view.serc-order.edit');
                    Route::post('/edit', [HeatController::class, 'editSERCOrderPost'])->name('comps.view.serc-order.editPost');
                    Route::get('/regen', [HeatController::class, 'regenSERCOrder'])->name('comps.view.serc-order.regen');
                });
            });
        });
    });

    Route::get('/comp/results/view-schema/{schema}', [OverallResultsController::class, 'computeResults'])->name("comps.results.view-schema");
    Route::get('/comp/results/view-schema/{schema}/print', [OverallResultsController::class, 'viewForPrint'])->name("comps.results.view-schema-print");
    Route::get('/comp/results/view-schema/{schema}/print-basic', [OverallResultsController::class, 'viewForPrintBasic'])->name("comps.results.view-schema-print-basic");
});


Route::prefix('/admin')->middleware('isAdmin')->group(function () {
    Route::get('', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/competition/create', [AdminController::class, 'createComp'])->name('admin.comp.create');
    Route::get('/competition/{comp}', [AdminController::class, 'viewComp'])->name('admin.comp.view');
    Route::post('/competition/create', [AdminController::class, 'createCompPost'])->name('admin.comp.create.post');
    Route::post('/competition/{comp}/update', [AdminController::class, 'updateCompPost'])->name('admin.comp.update.post');
    Route::post('/competition/{comp}/updateUser', [AdminController::class, 'updateCompUserPassword'])->name('admin.comp.update.userPassword');
    Route::get('/records', [AdminController::class, 'records'])->name('admin.records');
    Route::post('/records', [AdminController::class, 'updateRecords'])->name('admin.records.update');

    Route::delete('/competition/{comp}/delete', [AdminController::class, 'deleteCompPost'])->name('admin.comp.delete');
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

    $comp = Competition::findOrFail($id);
    if (!$comp->areResultsPublic()) abort(404);
    return $comp;
});

/* PUBLIC RESULTS VIEWING */
Route::prefix('results')->group(function () {
    Route::get('', [PublicResultsController::class, 'index'])->name('public.results');
    Route::get('/{comp_slug}', [PublicResultsController::class, 'viewComp'])->name("public.results.comp");
    Route::get('/{comp_slug}/speed/{event}', [PublicResultsController::class, 'viewSpeed'])->name("public.results.speed");
    Route::get('/{comp_slug}/serc/{event}', [PublicResultsController::class, 'viewSerc'])->name("public.results.serc");
    Route::get('/{comp_slug}/results/{schema}', [PublicResultsController::class, 'viewResults'])->name("public.results.results");
});

Route::get('dq', function () {
    $ret = [];
    foreach (DQCode::all() as $code) {
        $d = "DQ" . str_pad($code->id, 3, '0', STR_PAD_LEFT);
        array_push($ret, ['value' => $d, 'text' => $d]);
    }
    return response()->json($ret);
});


require __DIR__ . '/auth.php';
require __DIR__ . '/digitaljudge.php';
