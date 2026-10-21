<?php

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

use App\Http\Controllers\Landing\LandingController;
use App\Http\Controllers\Landing\ResultsController;
use App\Models\Competition;
use App\Models\DQCode;
use Carbon\Carbon;
use App\Helpers\RouteHelpers;
use Illuminate\Http\Request;



// Import judge routes first so judge. overrides
require __DIR__ . '/digitaljudge.php';

// Import the Management app first so app. overrides
require __DIR__ . '/app.php';

// Signed-in pages moved from the main domain to app. Send old links and bookmarks there
Route::domain(config('app.subdomain_base'))->group(function () {
    Route::get('{path}', fn (Request $request, string $path) => redirect()->away(
        $request->getScheme() . '://' . RouteHelpers::domainRemap('app.') . '/' . $path . ($request->getQueryString() ? '?' . $request->getQueryString() : ''),
        301
    ))->where('path', '(comps|create|organisation|accounts?|admin|invite|comp/results|login|register|forgot-password|reset-password|verify-email|confirm-password|logout|dashboard)(/.*)?');
});

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

// The public home page. Signed-in pages, the Management home included, are on app. (routes/app.php)
Route::get('/', function () {
    $ongoing = Competition::where('show_competition', true)->where('when', Carbon::today())->first();
    $upcoming = Competition::where('show_competition', true)->where('when', '>=', Carbon::today())->orderBy('when')->limit(10)->get();

    if ($upcoming->count() < 10) {
        $extra = Competition::where('show_competition', true)->where('when', '<', Carbon::today())->orderByDesc('when')->limit(10 - $upcoming->count())->get();
        $upcoming = $upcoming->merge($extra);
    }

    return view('welcome', compact('ongoing', 'upcoming'));
})->name('home');






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




Route::get('explore', [LandingController::class, 'explore'])->name('explore');
Route::get('v2', [LandingController::class, 'v2'])->name('landing.v2');
Route::get('search/{search}', [LandingController::class, 'search'])->name('search');

Route::prefix('competition/{comp}')->group(function () {
    Route::get('', [LandingController::class, 'showCompetition'])->name('landing.competition');
    Route::get('heats-and-draws', [LandingController::class, 'showHeatsAndDraws'])->name('landing.competition.heats-draws');


    Route::prefix('results')->group(function () {
        Route::get('', [LandingController::class, 'showResults'])->name('landing.competition.results');
        Route::get('breakdown/serc/{serc}', [ResultsController::class, 'showSercBreakdown'])->name('landing.competition.results.breakdown.serc');
        Route::get('notes/serc/{serc}/{entity_id}', [ResultsController::class, 'getSercNote'])->name('landing.competition.results.notes.serc');
        Route::get('sheet/master/{schema}', [ResultsController::class, 'getMasterSheetResults'])->name('landing.competition.results.get.master.sheet');
        Route::get('sheet/{schema}', [ResultsController::class, 'getSheetResults'])->name('landing.competition.results.get.sheet');
        Route::get('violation/{violation_id}/{violation_type}', [ResultsController::class, 'getViolation'])->name('landing.competition.results.get.violation');

        Route::get('{league}/{event}-{type}', [ResultsController::class, 'getEventResults'])->name('landing.competition.results.get');
    });
});


Route::get('/championship/{championship}', [LandingController::class, 'showChampionship'])->name('landing.championship');

// on the main domain, so links to them work from the subdomains (whose catch-all routes would take these paths)
Route::domain(config('app.subdomain_base'))->group(function () {
    Route::view('/terms', 'legal.terms')->name('legal.terms');
    Route::view('/privacy', 'legal.privacy')->name('legal.privacy');
    Route::view('/cookies', 'legal.cookies')->name('legal.cookies');
});

Route::get('/{organisation}', [LandingController::class, 'showOrganisation'])->name('landing.organisation');
