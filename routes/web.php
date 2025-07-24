<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountInviteController;
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
use App\Http\Controllers\CompetitorController;
use App\Http\Controllers\DigitalJudge\DigitalJudgeController;
use App\Http\Controllers\HeatController;
use App\Http\Controllers\Landing\LandingController;
use App\Http\Controllers\Organisation\OrganisationController;
use App\Http\Controllers\OverallResultsController;
use App\Http\Controllers\Pdf\PdfController;
use App\Http\Controllers\PrintableController;
use App\Http\Controllers\PublicResultsController;
use App\Http\Controllers\Push\PushController;
use App\Http\Controllers\SpeedsEventController;
use App\Http\Controllers\TeamsController;
use App\Http\Controllers\SERCController;
use App\Models\Competition;
use App\Models\DQCode;
use App\Models\Organisation\Organisation;
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

    /** @var User $user */
    $user = Auth::user();



    $comps = [];
    $comps['owner'] = $user->getCompetitionsByAccess('owner');
    $comps['invited'] = $user->getCompetitionsWithAccess;

    // Show default user compeitions lsit page based on competition access
    return view('dashboard', ['comps' => $comps, 'orgs' => $user->getOrganisations]);
})->name('home');




Route::middleware('auth')->group(function () {


    // Route::get('/comps', [CompetitionController::class, 'index'])->name('comps');


    //Route::get('push-test', [PushController::class, 'push'])->name('push.test');

    Route::redirect('/comps', '/')->name('comps');

    Route::get('/create', [CompetitionController::class, 'create'])->name('comps.create');
    Route::post('/create', [CompetitionController::class, 'createPost'])->name('comps.create.post');




    Route::prefix('/comps/{comp}')->group(function () {
        Route::get('', [CompetitionController::class, 'view'])->middleware('can:access,comp,"view"')->name('comps.view');

        Route::middleware('can:access,comp,"admin"')->group(function () {
            Route::get('/digital-judge-toggle', [DigitalJudgeController::class, 'toggle'])->name('dj.toggle');

            Route::post('/digital-judge-settings', [DigitalJudgeController::class, 'settingsPost'])->name('dj.settings');
            Route::get('/digital-judge-qrs', [DigitalJudgeController::class, 'qrs'])->name('dj.qrs');
            Route::get('/judge-log/v1', [DigitalJudgeController::class, 'judgeLog'])->name('dj.judgeLog');
            Route::get('/judge-log/v2', [DigitalJudgeController::class, 'betterJudgeLog'])->name('dj.betterJudgeLog');

            Route::get('/create-stats', [CompetitionController::class, 'createCompetitionStats'])->name('comps.createStats');


            Route::post('/settings', [CompetitionController::class, 'updateCompetitionSettings'])->name('comps.settings');
            Route::post('/delete', [CompetitionController::class, 'deleteComp'])->name('comps.delete');


            Route::get('/accounts', [CompetitionController::class, 'getCompetitionAccounts'])->name('comps.accounts');
            Route::get('/account/{account}', [CompetitionController::class, 'getCompetitionAccount'])->name('comps.accounts.view');
            Route::post('/account/{account}', [CompetitionController::class, 'editCompetitionAccount'])->name('comps.accounts.edit');
            Route::delete('/account/{account}', [CompetitionController::class, 'deleteCompetitionAccount'])->name('comps.accounts.delete');
            Route::post('/accounts/create', [CompetitionController::class, 'createCompetitionAccount'])->name('comps.accounts.create');
            Route::post('/account/serc-writer/new-password', [CompetitionController::class, 'resetSercWriterAccountPassword'])->name('comps.accounts.serc-writer.new-password');
        });



        // EVENTS
        Route::prefix('/events')->middleware('can:access,comp,"speed|serc|serc_writer"')->group(function () {

            Route::get('', [CompetitionController::class, 'events'])->name('comps.events');

            // SPEEDS
            Route::prefix('/speeds')->middleware('can:access,comp,"speed"')->group(function () {
                Route::get('/add', [SpeedsEventController::class, 'add'])->name('comps.events.speeds.add');
                Route::post('/add', [SpeedsEventController::class, 'addPost'])->name('comps.view.events.speeds.addPost');
                Route::delete('/{event}/delete', [SpeedsEventController::class, 'delete'])->name('comps.view.events.speeds.delete');

                Route::get('/{event}', [SpeedsEventController::class, 'view'])->name('comps.events.speeds.view');
                Route::get('/{event}/edit', [SpeedsEventController::class, 'edit'])->name('comps.events.speeds.edit');
                Route::post('/{event}/edit', [SpeedsEventController::class, 'updateResults'])->name('comps.view.events.speeds.editPost');


                Route::get('/{event}/digital-judge-toggle', [DigitalJudgeController::class, 'speedToggle'])->name('dj.speedToggle');

                Route::get('/{event}/hide', [SpeedsEventController::class, 'hide'])->name('comps.view.speeds.hide');
            });

            // SERCS

            Route::prefix('/sercs')->middleware('can:access,comp,"serc|serc_writer"')->group(function () {
                Route::get('/add', [SERCController::class, 'add'])->name('comps.events.sercs.add');
                Route::post('/add', [SERCController::class, 'addPost'])->name('comps.events.sercs.addPost');
                Route::prefix('/{serc}')->group(function () {


                    Route::get('', [SERCController::class, 'view'])->name('comps.events.sercs.view');
                    Route::get('/edit', [SERCController::class, 'edit'])->name('comps.events.sercs.edit');
                    Route::post('/edit', [SERCController::class, 'editPost'])->name('comps.view.events.sercs.editPost');


                    Route::middleware('can:access,comp,"serc"')->group(function () {
                        Route::delete('', [SERCController::class, 'delete'])->name('comps.view.events.sercs.delete');

                        Route::get('results/{team}/next', [SERCController::class, 'next'])->name('comps.view.events.sercs.next');

                        Route::get('/results/{team}/edit', [SERCController::class, 'editResultsView'])->name('comps.events.sercs.editResults');
                        Route::post('/results/{team}/edit', [SERCController::class, 'updateTeamResults'])->name('comps.view.events.sercs.editResultsPost');

                        Route::get('/digital-judge-toggle', [DigitalJudgeController::class, 'sercToggle'])->name('dj.sercToggle');
                        Route::get('/hide', [SERCController::class, 'hide'])->name('comps.view.sercs.hide');

                        Route::post('/image', [SERCController::class, 'addSercImage'])->name('comps.view.sercs.image');
                        Route::get('/image/remove', [SERCController::class, 'removeSercImage'])->name('comps.view.sercs.image.remove');
                    });
                });
            });
        });


        // TEAMS
        Route::prefix('/teams')->middleware('can:access,comp,"teams"')->group(function () {
            Route::get('', [CompetitionController::class, 'teams'])->name('comps.teams');
            Route::get('/edit', [TeamsController::class, 'edit'])->name('comps.teams.edit');
            Route::post('/edit', [TeamsController::class, 'editPost'])->name('comps.view.teams.editPost');
            Route::delete('/delete', [TeamsController::class, 'delete'])->name('comps.view.teams.delete');
        });

        // COMPETITORS - Only shows if socring type is set to use it instead of teams
        Route::prefix('/competitors')->middleware('can:access,comp,"teams"')->group(function () {
            Route::get('', [CompetitionController::class, 'competitors'])->name('comps.competitors');
            Route::get('/edit', [CompetitorController::class, 'edit'])->name('comps.competitors.edit');
            Route::post('/edit', [CompetitorController::class, 'save'])->name('comps.competitors.save');
            // Route::delete('/delete', [TeamsController::class, 'delete'])->name('comps.view.competitors.delete');
        });

        // RESULTS
        Route::prefix('/results')->middleware('can:access,comp,"results"')->group(function () {
            Route::get('', [OverallResultsController::class, 'view'])->name('comps.results');
            Route::get('/add', [OverallResultsController::class, 'add'])->name('comps.results.add');
            Route::get('/qg', [OverallResultsController::class, 'quickGen'])->name('comps.results.quickGen');
            Route::get('/pt', [OverallResultsController::class, 'publishToggle'])->name('comps.results.publishToggle');
            Route::get('/prt', [OverallResultsController::class, 'provToggle'])->name('comps.results.provToggle');
            Route::post('', [OverallResultsController::class, 'addPost'])->name('comps.results.addPost');
            Route::delete('/{schema}', [OverallResultsController::class, 'delete'])->name('comps.results.delete');
            Route::get('/{schema}/hide', [OverallResultsController::class, 'hide'])->name('comps.results.hide');
            Route::get('/print-all', [OverallResultsController::class, 'printAll'])->name('comps.results.print-all');
        });

        // HEATS AND SERC ORDER
        Route::prefix('/heats-and-draws')->middleware('can:access,comp,"heats_and_draws"')->group(function () {


            Route::get('', [HeatController::class, 'index'])->name('comps.heats');

            Route::prefix('/heats')->group(function () {
                Route::get('/edit', [HeatController::class, 'edit'])->name('comps.heats.edit');
                Route::post('/edit', [HeatController::class, 'editPost'])->name('comps.view.heats.editPost');
                Route::get('/gen', [HeatController::class, 'createDefaultHeatsForComp'])->name('comps.heats.gen');
                Route::post('/delete-heat', [HeatController::class, 'remHeat'])->name('comps.view.heats.delete-heat');
                Route::post('/swap-heats', [HeatController::class, 'swapHeats'])->name('comps.view.heats.swap');
            });
            Route::prefix('/serc-order')->group(function () {
                Route::get('/edit', [HeatController::class, 'editSERCOrder'])->name('comps.heats.serc-order.edit');
                Route::post('/edit', [HeatController::class, 'editSERCOrderPost'])->name('comps.view.serc-order.editPost');
                Route::post('/edit-tanks', [HeatController::class, 'editTanksPost'])->name('comps.view.serc-order.editTanksPost');
                Route::get('/regen', [HeatController::class, 'regenSERCOrder'])->name('comps.view.serc-order.regen');
            });
        });

        // PRINTABLES
        Route::prefix('printables')->middleware('can:access,comp,"printables"')->group(function () {

            Route::get('', [PrintableController::class, 'index'])->name('comps.printables');

            Route::get('serc-sheets/{serc}', [PrintableController::class, 'sercSheets'])->name('comps.printables.serc-sheets');

            Route::get('serc-marking-pack', [PrintableController::class, 'printSMS'])->name('comps.printables.serc-marking-pack');
            Route::get('chief-timekeeper-pack', [PrintableController::class, 'printCTP'])->name('comps.printables.chief-timekeeper-pack');
            Route::get('marshalling', [PrintableController::class, 'printMarshalling'])->name('comps.printables.marshalling');
        });

        // NOTIFICATIONS
        Route::prefix('notifications')->middleware('can:access,comp')->group(function () {
            Route::get('', [PushController::class, 'userSettingsPage'])->name('comps.notifications.user-settings');

            Route::post('push', [PushController::class, 'store'])->name('comps.notifications.push-store');
        });
    });


    Route::resource('organisation', OrganisationController::class)->names('orgs');
    Route::prefix('organisation/{organisation}')->group(function () {
        Route::get('/accounts', [OrganisationController::class, 'accounts'])->name('orgs.accounts');
        Route::post('/accounts', [OrganisationController::class, 'accountsPost'])->name('orgs.accounts.post');

        Route::get('/accounts/{account}', [OrganisationController::class, 'account'])->name('orgs.accounts.view');
        Route::post('/accounts/{account}', [OrganisationController::class, 'accountEditPost'])->name('orgs.accounts.edit');

        Route::get('/invite/{inviteId}/cancel', [OrganisationController::class, 'cancelInvite'])->name('orgs.invite.cancel');

        Route::delete('/accounts/remove', [OrganisationController::class, 'accountRemove'])->name('orgs.account.remove');
    });

    Route::prefix('accounts')->group(function () {
        Route::get('search/{email}', [AccountController::class, 'search'])->name('accounts.search');
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
});







Route::get('dashboard', function () {
    return redirect()->route('home');
});

Route::bind('comp_slug', function ($value) {

    $parts = explode(".", $value);


    if (count($parts) < 2) {
        $c = Competition::findOrFail($parts[0]);

        return $c;
    }

    $id = $parts[1];


    $comp = Competition::findOrFail($id);

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

Route::prefix('invite/{invite}/{email}')->group(function () {
    Route::get('', [AccountInviteController::class, 'show'])->name('invite.show');
    Route::get('resolve/{resolution}', [AccountInviteController::class, 'resolve'])->name('invite.resolve');
    Route::post('resolve/{resolution}/new-acc', [AccountInviteController::class, 'resolveNewAccount'])->name('invite.resolve.new-account');
});

Route::prefix('competition/{comp}')->group(function () {
    Route::get('', [LandingController::class, 'showCompetition'])->name('landing.competition');
    Route::get('heats-and-draws', [LandingController::class, 'showHeatsAndDraws'])->name('landing.competition.heats-draws');
    Route::get('results', [LandingController::class, 'showResults'])->name('landing.competition.results');
});

require __DIR__ . '/auth.php';

Route::get('/{organisation}', [LandingController::class, 'showOrganisation'])->name('landing.organisation');
