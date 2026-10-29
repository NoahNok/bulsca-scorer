<?php

use App\Helpers\RouteHelpers;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountInviteController;
use App\Http\Controllers\Activity\ActivityController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUsersController;
use App\Http\Controllers\ChampionshipController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\DigitalJudge\DigitalJudgeController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\Manage\CompetitionCreateController;
use App\Http\Controllers\Manage\CompetitionDashboardController;
use App\Http\Controllers\Manage\CompetitionEntriesController;
use App\Http\Controllers\Manage\CompetitionEventsController;
use App\Http\Controllers\Manage\CompetitionOfficialsController;
use App\Http\Controllers\Manage\HeatsAndDrawsController;
use App\Http\Controllers\Manage\HeatsController;
use App\Http\Controllers\Manage\DrawsController;
use App\Http\Controllers\Manage\MasterSheetsController;
use App\Http\Controllers\Manage\ResultSheetsController;
use App\Http\Controllers\Manage\SercEventController;
use App\Http\Controllers\Manage\SpeedEventController;
use App\Http\Controllers\Manage\ManageController;
use App\Http\Controllers\Manage\OrganisationCreateController;
use App\Http\Controllers\Organisation\OrganisationController;
use App\Http\Controllers\OverallResultsController;
use App\Http\Controllers\PrintableController;
use App\Http\Controllers\Push\PushController;
use App\Http\Controllers\SERCController;
use App\Http\Controllers\SERC\MarkingPointTemplateController;
use App\Http\Controllers\SpeedsEventController;
use App\Http\Middleware\CompetitionOwnsRouteModels;
use App\Http\Middleware\RecordCompetitionVisit;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
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

                Route::get('/officials', [CompetitionOfficialsController::class, 'index'])->name('manage.competitions.officials');
                Route::post('/officials', [CompetitionOfficialsController::class, 'store'])->name('manage.competitions.officials.store');
                Route::get('/officials/search', [CompetitionOfficialsController::class, 'search'])->name('manage.competitions.officials.search');
                Route::post('/officials/copy', [CompetitionOfficialsController::class, 'copy'])->name('manage.competitions.officials.copy');
                Route::post('/officials/{official}/resend', [CompetitionOfficialsController::class, 'resend'])->whereNumber('official')->name('manage.competitions.officials.resend');
                Route::post('/officials/{official}', [CompetitionOfficialsController::class, 'update'])->whereNumber('official')->name('manage.competitions.officials.update');
                Route::delete('/officials/{official}', [CompetitionOfficialsController::class, 'destroy'])->whereNumber('official')->name('manage.competitions.officials.destroy');
            });

            // ENTRIES & LEAGUES: one Svelte page, each entry and league saved on its own from a dialog
            Route::middleware('can:access,comp,"teams"')->group(function () {
                Route::get('/entries', [CompetitionEntriesController::class, 'index'])->name('manage.competitions.entries');
                Route::get('/entities', fn (Competition $comp) => redirect()->route('manage.competitions.entries', $comp, 301));

                Route::prefix('/entries')->name('manage.competitions.entries.')->group(function () {
                    Route::post('/clubs', [CompetitionEntriesController::class, 'storeClub'])->name('clubs.store');
                    Route::post('/clubs/{club}', [CompetitionEntriesController::class, 'updateClub'])->name('clubs.update');
                    Route::delete('/clubs/{club}', [CompetitionEntriesController::class, 'destroyClub'])->name('clubs.destroy');

                    Route::post('/teams', [CompetitionEntriesController::class, 'storeTeam'])->name('teams.store');
                    Route::post('/teams/{team}', [CompetitionEntriesController::class, 'updateTeam'])->name('teams.update');
                    Route::delete('/teams/{team}', [CompetitionEntriesController::class, 'destroyTeam'])->name('teams.destroy');

                    Route::post('/competitors', [CompetitionEntriesController::class, 'storeCompetitor'])->name('competitors.store');
                    Route::post('/competitors/{competitor}', [CompetitionEntriesController::class, 'updateCompetitor'])->name('competitors.update');
                    Route::delete('/competitors/{competitor}', [CompetitionEntriesController::class, 'destroyCompetitor'])->name('competitors.destroy');
                });

                Route::prefix('/leagues')->name('manage.competitions.leagues.')->group(function () {
                    Route::post('', [LeagueController::class, 'store'])->name('store');
                    Route::post('/{league}', [LeagueController::class, 'update'])->name('update');
                    Route::delete('/{league}', [LeagueController::class, 'delete'])->name('destroy');
                });
            });


            // EVENTS
            Route::prefix('/events')->middleware('can:access,comp,"speed|serc|serc_writer"')->group(function () {

                Route::get('', [CompetitionEventsController::class, 'index'])->name('manage.competitions.events');

                // SPEEDS: one Svelte page per event, each part of it saved on its own
                Route::prefix('/speeds')->middleware('can:access,comp,"speed"')->group(function () {
                    Route::post('', [CompetitionEventsController::class, 'storeSpeed'])->name('manage.competitions.events.speeds.store');

                    Route::prefix('/{event}')->name('manage.competitions.events.speeds.')->group(function () {
                        Route::get('', [SpeedEventController::class, 'show'])->name('show');
                        Route::get('/results', [SpeedEventController::class, 'editResults'])->name('results.edit');
                        Route::get('/settings', [SpeedEventController::class, 'settings'])->name('settings');
                        Route::post('/results', [SpeedEventController::class, 'saveResults'])->name('results');
                        Route::post('/setup', [SpeedEventController::class, 'saveSetup'])->name('setup');
                        Route::post('/visibility', [SpeedEventController::class, 'visibility'])->name('visibility');
                        Route::post('/scoring', [SpeedEventController::class, 'saveScoring'])->name('scoring');
                        Route::delete('', [SpeedEventController::class, 'destroy'])->name('destroy');

                        // The old pages, now the results page and parts of the settings page
                        foreach (['edit' => ['settings', ''], 'edit-result' => ['results.edit', ''], 'scoring-settings' => ['settings', '#scoring']] as $old => [$page, $part]) {
                            Route::get("/$old", fn (Competition $comp, CompetitionSpeedEvent $event) => redirect(route("manage.competitions.events.speeds.$page", [$comp, $event]) . $part, 301))->name("old.$old");
                        }
                    });

                    Route::get('/{event}/print-results', [SpeedsEventController::class, 'printResults'])->name('comps.view.events.speeds.printResults');
                    Route::get('/{event}/digital-judge-toggle', [DigitalJudgeController::class, 'speedToggle'])->name('dj.speedToggle');
                });

                // SERCS: a Svelte page per SERC, with its setup, settings and marking on their own pages

                Route::prefix('/sercs')->middleware('can:access,comp,"serc|serc_writer"')->group(function () {
                    Route::get('/create', [SercEventController::class, 'create'])->name('manage.competitions.events.sercs.create');
                    Route::post('', [SercEventController::class, 'store'])->name('manage.competitions.events.sercs.store');
                    Route::get('/add', fn (Competition $comp) => redirect(route('manage.competitions.events.sercs.create', $comp), 301))->name('manage.competitions.events.sercs.old.add');

                    Route::prefix('/{serc}')->group(function () {
                        Route::name('manage.competitions.events.sercs.')->group(function () {
                            Route::get('', [SercEventController::class, 'show'])->name('show');
                            Route::get('/setup', [SercEventController::class, 'setup'])->name('setup');
                            Route::post('/setup', [SercEventController::class, 'saveSetup'])->name('setup.save');

                            Route::middleware('can:access,comp,"serc"')->group(function () {
                                Route::get('/settings', [SercEventController::class, 'settings'])->name('settings');
                                Route::post('/visibility', [SercEventController::class, 'visibility'])->name('visibility');
                                Route::post('/scoring', [SercEventController::class, 'saveScoring'])->name('scoring');
                                Route::delete('', [SercEventController::class, 'destroy'])->name('destroy');

                                Route::get('/results/{entity}', [SercEventController::class, 'marks'])->whereNumber('entity')->name('marks');
                                Route::post('/results/{entity}', [SercEventController::class, 'saveMarks'])->whereNumber('entity')->name('marks.save');
                                Route::get('/mark-splits', [SercEventController::class, 'markSplits'])->name('mark-splits');
                                Route::get('/league-objectives', [SercEventController::class, 'leagueObjectives'])->name('league-objectives');

                                Route::get('/results/{entity}/edit', fn (Competition $comp, SERC $serc, int $entity) => redirect(route('manage.competitions.events.sercs.marks', [$comp, $serc, $entity]), 301))->whereNumber('entity')->name('old.marks');
                            });

                            // The old pages, now the setup page, a part of the settings page and League Objectives
                            foreach (['edit' => ['setup', ''], 'scoring-settings' => ['settings', '#scoring'], 'restriction-map' => ['league-objectives', '']] as $old => [$page, $part]) {
                                Route::get("/$old", fn (Competition $comp, SERC $serc) => redirect(route("manage.competitions.events.sercs.$page", [$comp, $serc]) . $part, 301))->name("old.$old");
                            }
                        });

                        Route::get('/print-results', [SERCController::class, 'printResults'])->name('comps.view.events.sercs.printResults');

                        Route::middleware('can:access,comp,"serc"')->group(function () {
                            Route::get('/digital-judge-toggle', [DigitalJudgeController::class, 'sercToggle'])->name('dj.sercToggle');

                            Route::post('/image', [SERCController::class, 'addSercImage'])->name('comps.view.sercs.image');
                            Route::get('/image/remove', [SERCController::class, 'removeSercImage'])->name('comps.view.sercs.image.remove');
                        });
                    });
                });
            });


            // RESULTS: Svelte pages for the overview, each Results sheet and Master sheet, and their New/Edit
            // forms. The print pages stay Blade.
            Route::prefix('/results')->middleware('can:access,comp,"results"')->group(function () {
                Route::name('manage.competitions.results')->group(function () {
                    Route::get('', [ResultSheetsController::class, 'index']);
                    Route::post('/publish', [ResultSheetsController::class, 'publish'])->name('.publish');
                    Route::post('/finalise', [ResultSheetsController::class, 'finalise'])->name('.finalise');

                    Route::get('/create', [ResultSheetsController::class, 'create'])->name('.create');
                    Route::post('', [ResultSheetsController::class, 'store'])->name('.store');
                    Route::prefix('/{sheet}')->whereNumber('sheet')->group(function () {
                        Route::get('', [ResultSheetsController::class, 'show'])->name('.show');
                        Route::get('/edit', [ResultSheetsController::class, 'edit'])->name('.edit');
                        Route::post('', [ResultSheetsController::class, 'update'])->name('.update');
                        Route::post('/visibility', [ResultSheetsController::class, 'visibility'])->name('.visibility');
                        Route::delete('', [ResultSheetsController::class, 'destroy'])->name('.destroy');
                    });
                });

                Route::prefix('/master')->name('manage.competitions.results.master.')->group(function () {
                    Route::get('/create', [MasterSheetsController::class, 'create'])->name('create');
                    Route::post('', [MasterSheetsController::class, 'store'])->name('store');
                    Route::get('/add', fn (Competition $comp) => redirect(route('manage.competitions.results.master.create', $comp), 301))->name('old.add');
                    Route::prefix('/{master}')->whereNumber('master')->group(function () {
                        Route::get('', [MasterSheetsController::class, 'show'])->name('show');
                        Route::get('/edit', [MasterSheetsController::class, 'edit'])->name('edit');
                        Route::post('', [MasterSheetsController::class, 'update'])->name('update');
                        Route::post('/visibility', [MasterSheetsController::class, 'visibility'])->name('visibility');
                        Route::delete('', [MasterSheetsController::class, 'destroy'])->name('destroy');
                    });
                });

                Route::get('/add', fn (Competition $comp) => redirect(route('manage.competitions.results.create', $comp), 301))->name('manage.competitions.results.old.add');

                Route::get('/print-all', [OverallResultsController::class, 'printAll'])->name('comps.results.print-all');
            });

            // HEATS AND SERC ORDER
            Route::prefix('/heats-and-draws')->middleware('can:access,comp,"heats_and_draws"')->group(function () {


                Route::get('', [HeatsAndDrawsController::class, 'index'])->name('manage.competitions.heats_and_draws');
                Route::post('visibility', [HeatsAndDrawsController::class, 'visibility'])->name('manage.competitions.heats_and_draws.visibility');

                Route::prefix('heats')->name('manage.competitions.heats_and_draws.heats.')->group(function () {
                    Route::post('generate', [HeatsController::class, 'generate'])->name('generate');

                    Route::get('{event}/edit', [HeatsController::class, 'edit'])->name('edit');
                    Route::post('{event}/reset', [HeatsController::class, 'reset'])->name('reset');
                    Route::post('{event}/swap', [HeatsController::class, 'swap'])->name('swap');
                    Route::post('{event}/swapHeats', [HeatsController::class, 'swapHeats'])->name('swapHeats');
                    Route::post('{event}/assign', [HeatsController::class, 'assign'])->name('assign');
                    Route::post('{event}/unassign', [HeatsController::class, 'unassign'])->name('unassign');
                    Route::post('{event}/insertHeat', [HeatsController::class, 'insertHeat'])->name('insertHeat');
                    Route::post('{event}/deleteHeat', [HeatsController::class, 'deleteHeat'])->name('deleteHeat');
                });

                Route::prefix('draws')->name('manage.competitions.heats_and_draws.draws.')->group(function () {
                    Route::get('edit', [DrawsController::class, 'edit'])->name('edit');
                    Route::get('{serc}/edit', fn (Competition $comp) => redirect()->route('manage.competitions.heats_and_draws.draws.edit', $comp, 301))->whereNumber('serc')->name('old.edit');
                    Route::get('tank-setup', [DrawsController::class, 'tankSetup'])->name('tank_setup');
                    Route::post('tank-setup', [DrawsController::class, 'saveTankSetup'])->name('tank_setup.save');

                    Route::post('generate', [DrawsController::class, 'generate'])->name('generate');
                    Route::post('regenerate', [DrawsController::class, 'regenerate'])->name('regenerate');
                    Route::post('swap', [DrawsController::class, 'swap'])->name('swap');
                    Route::post('move', [DrawsController::class, 'move'])->name('move');
                    Route::post('assign', [DrawsController::class, 'assign'])->name('assign');
                    Route::post('remove', [DrawsController::class, 'remove'])->name('remove');
                    Route::post('add-tank', [DrawsController::class, 'addTank'])->name('addTank');
                    Route::post('remove-tank', [DrawsController::class, 'removeTank'])->name('removeTank');
                    Route::post('compact-tanks', [DrawsController::class, 'compactTanks'])->name('compactTanks');
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



        Route::get('/comp/results/view-schema/{sheet}', [ResultSheetsController::class, 'old'])->name('manage.competitions.results.old.view-schema');
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
