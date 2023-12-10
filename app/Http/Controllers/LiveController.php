<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\SERC;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LiveController extends Controller
{
    public function index(Request $request)
    {


        $comp = Competition::where(DB::raw('DATEDIFF(competitions.when, NOW())'), '>', -2)->orderBy(DB::raw('DATEDIFF(competitions.when, NOW())'), 'asc')->first();

        if (auth()->user() && auth()->user()->isAdmin() && $request->has('comp')) {
            $comp = Competition::find($request->input('comp'));
            if (!$comp) return view('live.unavailable', ['message' => 'No competitions with that ID exists!']);
            $comp->can_be_live = true;
        }

        if (!$comp) return view('live.unavailable', ['message' => 'No competitions are currently available to view live.']);

        if ($comp->can_be_live == false) return view('live.unavailable', ['message' => $comp->name . ' is not currently available to view live.']);

        return view('live.index', ['comp' => $comp]);
    }

    public function liveData(Competition $comp)
    {

        $serc = Cache::remember('live.' . $comp->id . '.drySerc', 60 * 60, function () use ($comp) {
            return SERC::where('competition', $comp->id)->where('name', 'LIKE', '%Dry%')->first();
        });


        $sercsFinished = Cache::remember('live.' . $comp->id . '.howManySercsHasEachTeamFinished', 10, function () use ($comp) {
            return $comp->howManySercsHasEachTeamFinished();
        });
        $avgTime = Cache::remember('live.' . $comp->id . '.getAverageSercTime', 10, function () use ($serc) {
            // This is not ideal, should make comp org select which serc is dry



            return $serc->getAverageTimeBetweenTeams();
        });


        $startTime = $comp->serc_start_time ? $comp->serc_start_time->timestamp * 1000 : null;
        if (!empty($sercsFinished)) {
            $startTime = Cache::remember('live.' . $comp->id . '.getStartTime', 10, function () use ($serc) {
                $t =  new Carbon(DB::select(' SELECT MAX(sr.created_at) AS start_time FROM serc_results sr INNER JOIN serc_marking_points smp on smp.id=sr.marking_point WHERE serc=?;', [$serc->id])[0]->start_time);
                $t->addMinutes(2); // Assume max 2m reset time
                return $t->timestamp * 1000;
            });
        }

        return response()->json(['sercsFinished' => $sercsFinished, 'avgTime' => (float) $avgTime, 'sercStartTime' => (int) $startTime, 'heatsFinished' => $comp->whichSpeedEventHeatsHaveFinished()]);
    }
}
