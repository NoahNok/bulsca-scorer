<?php

use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\OverallResultsController;
use App\Http\Controllers\SpeedsEventController;
use App\Http\Controllers\TeamsController;
use App\Http\Controllers\SERCController;
use App\Models\Competition;
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

Route::get('/', function () {

    return view('welcome');
})->name('home');

Route::get('/comps', [CompetitionController::class, 'index'])->name('comps');
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
Route::post('/comp/{comp}/results/add', [OverallResultsController::class, 'addPost'])->name('comps.view.results.addPost');
Route::delete('/comp/{comp}/results/{schema}', [OverallResultsController::class, 'delete'])->name('comps.view.results.delete');
Route::get('/results/view-schema/{schema}', [OverallResultsController::class, 'computeResults'])->name("comps.results.view-schema");
