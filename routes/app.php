<?php

use App\Helpers\RouteHelpers;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountInviteController;
use App\Http\Controllers\Activity\ActivityController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUsersController;
use App\Http\Controllers\ChampionshipController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\CompetitionOfficialsController;
use App\Http\Controllers\DigitalJudge\DigitalJudgeController;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\Manage\CompetitionCreateController;
use App\Http\Controllers\Manage\CompetitionDashboardController;
use App\Http\Controllers\Manage\ManageController;
use App\Http\Controllers\Manage\OrganisationCreateController;
use App\Http\Controllers\Orders\DrawController;
use App\Http\Controllers\Orders\HeatController;
use App\Http\Controllers\Organisation\OrganisationController;
use App\Http\Controllers\OverallResultsController;
use App\Http\Controllers\PrintableController;
use App\Http\Controllers\Push\PushController;
use App\Http\Controllers\Result\MasterSchemaController;
use App\Http\Controllers\SERCController;
use App\Http\Controllers\SERC\MarkingPointTemplateController;
use App\Http\Controllers\SpeedsEventController;
use App\Http\Controllers\TeamsController;
use App\Http\Middleware\CompetitionOwnsRouteModels;
use App\Http\Middleware\RecordCompetitionVisit;
use App\Models\SERC;
use Illuminate\Support\Facades\Route;

/*
| The app. subdomain: every signed-in page (Management, Admin, account, sign in), like the
| Digital Judge on judge. The main domain is public only, and sends old links here (web.php).
|
| Pages are Blade until each is redesigned into the Management app (Svelte, resources/js/manage.ts).
| HandleInertiaRequests gives every Inertia page on this domain the Management root view and
| shared props. Name new routes manage.* (Admin pages manage.admin.*).
| See docs/adr/0002-public-blade-management-svelte.md.
*/

Route::domain(RouteHelpers::domainRemap("app."))->group(function () {
    require __DIR__ . '/auth.php';

    Route::middleware(['auth', 'not.judge-only'])->group(function () {
        // The Management app's home (Svelte). Guests are sent to sign in by the auth middleware
        Route::get('/', [ManageController::class, 'home'])->name('manage.home');
        Route::redirect('/v2', '/');
        Route::permanentRedirect('/dashboard', '/');
    });

    Route::middleware(['auth', 'not.judge-only'])->group(function () {


        // Route::get('/comps', [CompetitionController::class, 'index'])->name('comps');


        //Route::get('push-test', [PushController::class, 'push'])->name('push.test');

        Route::redirect('/comps', '/')->name('comps');

        Route::get('/create', [CompetitionCreateController::class, 'create'])->name('manage.competitions.create');
        Route::post('/create', [CompetitionCreateController::class, 'store'])->name('manage.competitions.store');

        Route::prefix('/comps/{comp}')->middleware([CompetitionOwnsRouteModels::class, RecordCompetitionVisit::class])->group(function () {
            Route::get('', [CompetitionDashboardController::class, 'overview'])->middleware('can:access,comp,"view"')->name('comps.view');

            Route::get('/activity-log', [CompetitionController::class, 'activityLog'])->middleware('can:access,comp,"admin"')->name('comps.activityLog');

            Route::middleware('can:access,comp,"admin"')->group(function () {
                Route::post('/digital-judge-toggle', [DigitalJudgeController::class, 'toggle'])->name('dj.toggle');

                Route::post('/digital-judge-settings', [DigitalJudgeController::class, 'settingsPost'])->name('dj.settings');
                Route::post('/digital-judge-sercs-release', [DigitalJudgeController::class, 'sercsReleasePost'])->name('dj.sercs-release');
                Route::get('/digital-judge-qrs', [DigitalJudgeController::class, 'qrs'])->name('dj.qrs');


                Route::get('/create-stats', [CompetitionController::class, 'createCompetitionStats'])->name('comps.createStats');


                Route::get('/settings', [CompetitionDashboardController::class, 'settings'])->name('manage.competitions.settings');
                Route::post('/settings', [CompetitionController::class, 'updateCompetitionSettings'])->name('comps.settings');
                Route::post('/settings/scoring', [CompetitionController::class, 'updateCompetitionScoringSettings'])->name('comps.settings.scoring');
                Route::post('/delete', [CompetitionController::class, 'deleteComp'])->name('comps.delete');


                Route::post('/account/{account}', [CompetitionController::class, 'editCompetitionAccount'])->name('comps.accounts.edit');
                Route::delete('/account/{account}', [CompetitionController::class, 'deleteCompetitionAccount'])->name('comps.accounts.delete');
                Route::post('/accounts/invitee', [CompetitionController::class, 'inviteCompetitionAccount'])->name('comps.accounts.invite');
                Route::post('/account/serc-writer/new-password', [CompetitionController::class, 'resetSercWriterAccountPassword'])->name('comps.accounts.serc-writer.new-password');

                Route::get('/officials', [CompetitionOfficialsController::class, 'index'])->name('comps.officials');
                Route::post('/officials', [CompetitionOfficialsController::class, 'store'])->name('comps.officials.store');
                Route::get('/officials/search', [CompetitionOfficialsController::class, 'search'])->name('comps.officials.search');
                Route::post('/officials/copy', [CompetitionOfficialsController::class, 'copy'])->name('comps.officials.copy');
                Route::post('/officials/{official}/resend', [CompetitionOfficialsController::class, 'resend'])->whereNumber('official')->name('comps.officials.resend');
                Route::post('/officials/{official}', [CompetitionOfficialsController::class, 'update'])->whereNumber('official')->name('comps.officials.update');
                Route::delete('/officials/{official}', [CompetitionOfficialsController::class, 'destroy'])->whereNumber('official')->name('comps.officials.destroy');
            });

            Route::prefix('/leagues')->middleware('can:access,comp,"teams"')->group(function () {
                Route::get('create', [LeagueController::class, 'create'])->name('comps.leagues.create');
                Route::post('create', [LeagueController::class, 'store'])->name('comps.leagues.store');

                Route::get('{league}', [LeagueController::class, 'view'])->name('comps.leagues.view');
                Route::post('{league}', [LeagueController::class, 'update'])->name('comps.leagues.update');
                Route::delete('{league}', [LeagueController::class, 'delete'])->name('comps.leagues.delete');
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
                    Route::get('/{event}/edit-result', [SpeedsEventController::class, 'editResult'])->name('comps.events.speeds.editResult');
                    Route::get('/{event}/edit', [SpeedsEventController::class, 'edit'])->name('comps.events.speeds.edit');
                    Route::post('/{event}/edit', [SpeedsEventController::class, 'editPost'])->name('comps.events.speeds.editPost');
                    Route::post('/{event}/edit-result', [SpeedsEventController::class, 'updateResults'])->name('comps.view.events.speeds.editResultPost');

                    Route::get('/{event}/print-results', [SpeedsEventController::class, 'printResults'])->name('comps.view.events.speeds.printResults');


                    Route::get('/{event}/digital-judge-toggle', [DigitalJudgeController::class, 'speedToggle'])->name('dj.speedToggle');

                    Route::get('/{event}/hide', [SpeedsEventController::class, 'hide'])->name('comps.view.speeds.hide');


                    Route::get('/{event}/scoring-settings', [SpeedsEventController::class, 'scoringSettings'])->name('comps.events.speeds.scoring-settings');
                    Route::post('/{event}/scoring-settings', [SpeedsEventController::class, 'saveScoringSettings'])->name('comps.events.speeds.scoring-settings.save');
                });

                // SERCS

                Route::prefix('/sercs')->middleware('can:access,comp,"serc|serc_writer"')->group(function () {
                    Route::get('/add', [SERCController::class, 'add'])->name('comps.events.sercs.add');
                    Route::post('/add', [SERCController::class, 'addPost'])->name('comps.events.sercs.addPost');
                    Route::prefix('/{serc}')->group(function () {


                        Route::get('', [SERCController::class, 'view'])->name('comps.events.sercs.view');
                        Route::get('/edit', [SERCController::class, 'edit'])->name('comps.events.sercs.edit');
                        Route::post('/edit', [SERCController::class, 'editPost'])->name('comps.events.sercs.editPost');
                        Route::get('/print-results', [SERCController::class, 'printResults'])->name('comps.view.events.sercs.printResults');


                        Route::middleware('can:access,comp,"serc"')->group(function () {
                            Route::delete('', [SERCController::class, 'delete'])->name('comps.view.events.sercs.delete');

                            Route::get('results/{entity_id}/next', [SERCController::class, 'next'])->name('comps.view.events.sercs.next');

                            Route::get('/results/{entity_id}/edit', [SERCController::class, 'editResultsView'])->name('comps.events.sercs.editResults');
                            Route::post('/results/{entity_id}/edit', [SERCController::class, 'updateTeamResults'])->name('comps.view.events.sercs.editResultsPost');

                            Route::get('/digital-judge-toggle', [DigitalJudgeController::class, 'sercToggle'])->name('dj.sercToggle');
                            Route::get('/hide', [SERCController::class, 'hide'])->name('comps.view.sercs.hide');

                            Route::post('/image', [SERCController::class, 'addSercImage'])->name('comps.view.sercs.image');
                            Route::get('/image/remove', [SERCController::class, 'removeSercImage'])->name('comps.view.sercs.image.remove');

                            Route::get('/scoring-settings', [SERCController::class, 'scoringSettings'])->name('comps.events.sercs.scoring-settings');
                            Route::post('/scoring-settings', [SERCController::class, 'saveScoringSettings'])->name('comps.events.sercs.scoring-settings.save');

                            Route::get('/mark-splits', [SERCController::class, 'markSplits'])->name('comps.events.sercs.mark-splits');
                            Route::get('/mark-splits/{judge}', [SERCController::class, 'loadMarkSplit'])->name('comps.events.sercs.mark-splits.judge');

                            Route::get('/restriction-map', [SERCController::class, 'restrictionMap'])->name('comps.events.sercs.restriction-map');
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

            // ENTITIES - Only shows if socring type is set to use it instead of teams
            Route::prefix('/entities')->middleware('can:access,comp,"teams"')->group(function () {
                Route::get('', [EntityController::class, 'view'])->name('comps.entities');
                Route::get('/edit', [EntityController::class, 'edit'])->name('comps.entities.edit');
                Route::post('/edit', [EntityController::class, 'save'])->name('comps.entities.save');
                // Route::delete('/delete', [TeamsController::class, 'delete'])->name('comps.view.competitors.delete');
            });

            // RESULTS
            Route::prefix('/results')->middleware('can:access,comp,"results"')->group(function () {

                Route::prefix('/master')->group(function () {
                    Route::get('add', [MasterSchemaController::class, 'add'])->name('comps.results.master.add');
                    Route::post('add', [MasterSchemaController::class, 'addPost'])->name('comps.results.master.addPost');

                    Route::get('{schema}', [MasterSchemaController::class, 'view'])->name('comps.results.master.view');

                    Route::delete('/{schema}', [MasterSchemaController::class, 'delete'])->name('comps.results.master.delete');
                    Route::get('/{schema}/hide', [MasterSchemaController::class, 'hide'])->name('comps.results.master.hide');
                });

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


                Route::get('', [HeatController::class, 'index'])->name('comps.heats_and_draws');

                Route::prefix('heats')->group(function () {
                    Route::get('generate', [HeatController::class, 'generate'])->name('comps.heats_and_draws.heats.generate');

                    Route::get('hide', [HeatController::class, 'hide'])->name('comps.heats_and_draws.heats.hide');

                    Route::get('{event}/edit', [HeatController::class, 'edit'])->name('comps.heats_and_draws.heats.edit');
                    Route::get('{event}/reset', [HeatController::class, 'reset'])->name('comps.heats_and_draws.heats.reset');
                    Route::post('{event}/swap', [HeatController::class, 'swap'])->name('comps.heats_and_draws.heats.swap');
                    Route::post('{event}/swapHeats', [HeatController::class, 'swapHeats'])->name('comps.heats_and_draws.heats.swapHeats');
                    Route::post('{event}/assign', [HeatController::class, 'assign'])->name('comps.heats_and_draws.heats.assign');
                    Route::post('{event}/unassign', [HeatController::class, 'unassign'])->name('comps.heats_and_draws.heats.unassign');
                    Route::post('{event}/insertHeat', [HeatController::class, 'insertHeat'])->name('comps.heats_and_draws.heats.insertHeat');
                    Route::post('{event}/deleteHeat', [HeatController::class, 'deleteHeat'])->name('comps.heats_and_draws.heats.deleteHeats');
                });

                Route::prefix('draws')->group(function () {
                    Route::get('generate', [DrawController::class, 'generate'])->name('comps.heats_and_draws.draws.generate');
                    Route::get('hide', [DrawController::class, 'hide'])->name('comps.heats_and_draws.draws.hide');
                    Route::get('tank-setup', [DrawController::class, 'tankSetup'])->name('comps.heats_and_draws.draws.tank_setup');
                    Route::post('tank-setup', [DrawController::class, 'tankSetupPost'])->name('comps.heats_and_draws.draws.tank_setup.post');
                    Route::get('{serc}/edit', [DrawController::class, 'edit'])->name('comps.heats_and_draws.draws.edit');
                    Route::post('{serc}/edit', [DrawController::class, 'swap'])->name('comps.heats_and_draws.draws.swap');
                    Route::post('{serc}/remove', [DrawController::class, 'remove'])->name('comps.heats_and_draws.draws.remove');
                    Route::post('{serc}/move', [DrawController::class, 'move'])->name('comps.heats_and_draws.draws.move');
                    Route::post('{serc}/assign', [DrawController::class, 'assign'])->name('comps.heats_and_draws.draws.assign');
                    Route::post('{serc}/add-tank', [DrawController::class, 'addTank'])->name('comps.heats_and_draws.draws.addTank');
                    Route::post('{serc}/compact-tanks', [DrawController::class, 'compactTanks'])->name('comps.heats_and_draws.draws.compactTanks');
                    Route::post('{serc}/remove-tank', [DrawController::class, 'removeTank'])->name('comps.heats_and_draws.draws.removeTank');
                    Route::post('{serc}/reset', [DrawController::class, 'reset'])->name('comps.heats_and_draws.draws.reset');
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


        // Before the resource, whose show route would otherwise take /organisation/create
        Route::get('/organisation/create', [OrganisationCreateController::class, 'create'])->name('manage.organisations.create');
        Route::post('/organisation', [OrganisationCreateController::class, 'store'])->name('manage.organisations.store');
        Route::resource('organisation', OrganisationController::class)->except(['create', 'store'])->names('orgs');
        Route::prefix('organisation/{organisation}')->middleware('can:access,organisation,"admin"')->group(function () {
            Route::get('/accounts', [OrganisationController::class, 'accounts'])->name('orgs.accounts');
            Route::post('/accounts', [OrganisationController::class, 'accountsPost'])->name('orgs.accounts.post');
            Route::get('/officials', [OrganisationController::class, 'officials'])->name('orgs.officials');
            Route::get('/officials/export', [OrganisationController::class, 'officialsExport'])->name('orgs.officials.export');
            Route::post('/officials/add', [OrganisationController::class, 'officialsAddToCompetition'])->name('orgs.officials.add');

            Route::get('/accounts/{account}', [OrganisationController::class, 'account'])->name('orgs.accounts.view');
            Route::post('/accounts/{account}', [OrganisationController::class, 'accountEditPost'])->name('orgs.accounts.edit');

            Route::get('/invite/{inviteId}/cancel', [OrganisationController::class, 'cancelInvite'])->name('orgs.invite.cancel');

            Route::delete('/accounts/remove', [OrganisationController::class, 'accountRemove'])->name('orgs.account.remove');

            Route::prefix('infractions')->group(function () {
                Route::get('', [OrganisationController::class, 'infractions'])->name('orgs.infractions');
                Route::post('create', [OrganisationController::class, 'createInfraction'])->name('orgs.infractions.create');
                Route::get('{type}/{id}', [OrganisationController::class, 'getInfraction'])->name('orgs.infractions.view');
                Route::post('{type}/{id}', [OrganisationController::class, 'updateInfraction'])->name('orgs.infractions.update');
                Route::delete('{type}/{id}', [OrganisationController::class, 'deleteInfraction'])->name('orgs.infractions.delete');
            });

            Route::prefix('scoring')->group(function () {
                Route::get('', [OrganisationController::class, 'scoringSettings'])->name('orgs.scoring');


                Route::prefix('result-schemas')->group(function () {
                    Route::post('/create', [OrganisationController::class, 'createResultSchemaTemplatePost'])->name('orgs.scoring.result-schema.create.post');
                    Route::get('/create', [OrganisationController::class, 'createResultSchemaTemplate'])->name('orgs.scoring.result-schema.create');

                    Route::prefix('{schema}')->group(function () {
                        Route::get('/edit', [OrganisationController::class, 'editResultSchemaTemplate'])->name('orgs.scoring.result-schema.edit');
                        Route::post('/edit', [OrganisationController::class, 'editResultSchemaTemplatePost'])->name('orgs.scoring.result-schema.edit.post');
                        Route::delete('', [OrganisationController::class, 'deleteResultSchemaTemplate'])->name('orgs.scoring.result-schema.delete');
                    });
                });


                Route::post('/create', [OrganisationController::class, 'createScoringSchemaPost'])->name('orgs.scoring.create.post');
                Route::get('/create', [OrganisationController::class, 'createScoringSchema'])->name('orgs.scoring.create');

                Route::prefix('{schema}')->group(function () {
                    Route::get('/edit', [OrganisationController::class, 'editScoringSchema'])->name('orgs.scoring.edit');
                    Route::post('/edit', [OrganisationController::class, 'editScoringSchemaPost'])->name('orgs.scoring.edit.post');
                    Route::delete('', [OrganisationController::class, 'deleteScoringSchema'])->name('orgs.scoring.delete');
                });
            });

            Route::prefix('championships')->group(function () {

                Route::get('', [ChampionshipController::class, 'view'])->name('orgs.championships');

                Route::get('/create', [ChampionshipController::class, 'create'])->name('orgs.championship.create');
                Route::post('/create', [ChampionshipController::class, 'store'])->name('orgs.championship.store');

                Route::prefix('/{championship}')->group(function () {
                    Route::get('', [ChampionshipController::class, 'show'])->name('orgs.championship.view');

                    Route::get('/add-competition', [ChampionshipController::class, 'addCompetition'])->name('orgs.championship.add-competition');
                    Route::post('/associate-competition', [ChampionshipController::class, 'associateCompetition'])->name('orgs.championship.associate-competition');
                    Route::post('/deassociate-competition', [ChampionshipController::class, 'deassociateCompetition'])->name('orgs.championship.deassociate-competition');
                    // Route::get('/edit', [ChampionshipController::class, 'edit'])->name('championship.edit');
                    // Route::post('/edit', [ChampionshipController::class, 'editPost'])->name('championship.edit.post');
                    // Route::delete('', [ChampionshipController::class, 'delete'])->name('championship.delete');
                });
            });
        });

        Route::prefix('accounts')->group(function () {
            Route::get('search/{email}', [AccountController::class, 'search'])->name('accounts.search');
        });

        Route::prefix('account')->group(function () {
            Route::get('', [AccountController::class, 'edit'])->name('account');
            Route::post('', [AccountController::class, 'update'])->name('account.update');
            Route::delete('devices/{login}', [AccountController::class, 'signOutDevice'])->whereNumber('login')->name('account.devices.destroy');
            Route::delete('devices', [AccountController::class, 'signOutOtherDevices'])->name('account.devices.destroy-others');
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

        Route::get('/activity', [ActivityController::class, 'admin'])->name('admin.activity');

        Route::get('/users', [AdminUsersController::class, 'index'])->name('admin.users.index');
        Route::get('/users/{user}', [AdminUsersController::class, 'show'])->name('admin.users.show');

        Route::resource('serc/marking-points', MarkingPointTemplateController::class)->except(['show'])->names([
            'index' => 'admin.serc.marking-point-template.index',
            'create' => 'admin.serc.marking-point-template.create',
            'store' => 'admin.serc.marking-point-template.store',
            'edit' => 'admin.serc.marking-point-template.edit',
            'update' => 'admin.serc.marking-point-template.update',
            'destroy' => 'admin.serc.marking-point-template.destroy',
        ]);
    });

    Route::prefix('invite/{invite}/{email}')->group(function () {
        Route::get('', [AccountInviteController::class, 'show'])->name('invite.show');
        Route::get('resolve/{resolution}', [AccountInviteController::class, 'resolve'])->name('invite.resolve');
        Route::post('accept/new-acc', [AccountInviteController::class, 'acceptNewAccount'])->name('manage.invite.accept-new-account');
        Route::post('decline/new-acc', [AccountInviteController::class, 'declineNewAccount'])->name('manage.invite.decline-new-account');
    });
});
