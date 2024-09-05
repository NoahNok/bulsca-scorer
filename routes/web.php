<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Brands\BrandController;
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
use App\Http\Controllers\CompetitorController;
use App\Http\Controllers\DigitalJudge\DigitalJudgeController;
use App\Http\Controllers\HeatController;
use App\Http\Controllers\OverallResultsController;
use App\Http\Controllers\PrintableController;
use App\Http\Controllers\PublicResultsController;
use App\Http\Controllers\SpeedsEventController;
use App\Http\Controllers\TeamsController;
use App\Http\Controllers\SERCController;
use App\Models\Competition;
use App\Models\DQCode;
use Illuminate\Support\Facades\Auth;



// Import judge routes first so judge. overrides
require __DIR__ . '/digitaljudge.php';

// Import judge routes first so results. overrides
require __DIR__ . '/results.php';

// Import LIVE routes first so results. overrides
require __DIR__ . '/live.php';

// Import WHATIF routes first so results. overrides
require __DIR__ . '/whatif.php';

// Import STATS routes first so results. overrides
require __DIR__ . '/stats.php';

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

Route::get('/', function () {

    if (Auth::guest()) {
        return view('welcome');
    }



    if (!auth()->user()->isAdmin() && auth()->user()->getCompetition) {
        return redirect()->route('comps.view', auth()->user()->getCompetition);
    }

    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.index');
    }
})->name('home');




Route::middleware('auth')->group(function () {


    // Route::get('/comps', [CompetitionController::class, 'index'])->name('comps');

    Route::redirect('/comps', '/')->name('comps');

    Route::middleware('onlyViewOwnComp')->group(function () {

        Route::prefix('/comps/{comp}')->group(function () {
            Route::get('', [CompetitionController::class, 'view'])->name('comps.view');
            Route::get('/digital-judge-toggle', [DigitalJudgeController::class, 'toggle'])->name('dj.toggle');
            Route::get('/digital-judge-settings', [DigitalJudgeController::class, 'settings'])->name('dj.settings');
            Route::post('/digital-judge-settings', [DigitalJudgeController::class, 'settingsPost'])->name('dj.settings.post');
            Route::get('/digital-judge-qrs', [DigitalJudgeController::class, 'qrs'])->name('dj.qrs');
            Route::get('/judge-log/v1', [DigitalJudgeController::class, 'judgeLog'])->name('dj.judgeLog');
            Route::get('/judge-log/v2', [DigitalJudgeController::class, 'betterJudgeLog'])->name('dj.betterJudgeLog');

            Route::get('/create-stats', [CompetitionController::class, 'createCompetitionStats'])->name('comps.createStats');

            Route::get('/settings', [CompetitionController::class, 'settings'])->name('comps.settings');
            Route::post('/settings', [CompetitionController::class, 'updateCompetitionSettings'])->name('comps.settings.post');

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


                    Route::get('/{event}/digital-judge-toggle', [DigitalJudgeController::class, 'speedToggle'])->name('dj.speedToggle');
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

            // COMPETITORS - Only shows if socring type is set to use it instead of teams
            Route::prefix('/competitors')->group(function () {
                Route::get('', [CompetitionController::class, 'competitors'])->name('comps.view.competitors');
                Route::get('/edit', [CompetitorController::class, 'edit'])->name('comps.view.competitors.edit');
                Route::post('/edit', [CompetitorController::class, 'save'])->name('comps.view.competitors.save');
                // Route::delete('/delete', [TeamsController::class, 'delete'])->name('comps.view.competitors.delete');
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
                    Route::post('/edit-tanks', [HeatController::class, 'editTanksPost'])->name('comps.view.serc-order.editTanksPost');
                    Route::get('/regen', [HeatController::class, 'regenSERCOrder'])->name('comps.view.serc-order.regen');
                });
            });

            // PRINTABLES
            Route::prefix('printables')->group(function () {

                Route::get('serc-sheets/{serc}', [PrintableController::class, 'sercSheets'])->name('comps.view.printables.serc-sheets');
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

    Route::get('seasons', [AdminController::class, 'seasons'])->name('admin.seasons');
    Route::get('season/create', [AdminController::class, 'seasonCreate'])->name('admin.seasons.create');
    Route::post('season/create', [AdminController::class, 'seasonCreatePost'])->name('admin.seasons.create.post');
    Route::get('season/edit/{season}', [AdminController::class, 'seasonEdit'])->name('admin.seasons.edit');
    Route::post('season/edit/{season}', [AdminController::class, 'seasonEditPost'])->name('admin.seasons.edit.post');

    Route::delete('/competition/{comp}/delete', [AdminController::class, 'deleteCompPost'])->name('admin.comp.delete');

    Route::prefix('/brands')->group(function () {
        Route::get('', [BrandController::class, 'index'])->name('admin.brands');
        Route::get('create', [BrandController::class, 'create'])->name('admin.brands.create');
        Route::post('create', [BrandController::class, 'store'])->name('admin.brands.store');

        Route::post('edit/{brand}', [BrandController::class, 'update'])->name('admin.brands.update');

        Route::get('{brand}', [BrandController::class, 'show'])->name('admin.brands.show');

        Route::delete('{brand}', [BrandController::class, 'destroy'])->name('admin.brands.delete');
    });
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('dashboard', function () {

    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.index');
    }

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



Route::get('dq', function () {
    $ret = [];
    foreach (DQCode::all() as $code) {
        $d = "DQ" . str_pad($code->id, 3, '0', STR_PAD_LEFT);
        array_push($ret, ['value' => $d, 'text' => $d]);
    }
    return response()->json($ret);
});


require __DIR__ . '/auth.php';
