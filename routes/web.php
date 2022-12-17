<?php

use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\SpeedsEventController;
use App\Http\Controllers\TeamsController;
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

Route::get('/comps/{comp}/events/speeds/{event}', [SpeedsEventController::class, 'view'])->name('comps.view.events.speeds.view');
Route::get('/comps/{comp}/events/speeds/{event}/edit', [SpeedsEventController::class, 'edit'])->name('comps.view.events.speeds.edit');
Route::post('/comps/{comp}/events/speeds/{event}/edit', [SpeedsEventController::class, 'updateResults'])->name('comps.view.events.speeds.editPost');

Route::get('/comps/{comp}/teams', [CompetitionController::class, 'teams'])->name('comps.view.teams');
Route::get('/comps/{comp}/teams/edit', [TeamsController::class, 'edit'])->name('comps.view.teams.edit');
Route::post('/comps/{comp}/teams/edit', [TeamsController::class, 'editPost'])->name('comps.view.teams.editPost');
Route::delete('/comps/{comp}/teams/delete', [TeamsController::class, 'delete'])->name('comps.view.teams.delete');
