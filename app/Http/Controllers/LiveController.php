<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\SERC;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LiveController extends Controller
{
    public function index(Request $request)
    {


        $comp = Competition::orderBy(DB::raw('ABS(DATEDIFF(competitions.when, NOW()))'), 'asc')->first();

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


        $sercsFinished = Cache::remember('live.howManySercsHasEachTeamFinished', 10, function () use ($comp) {
            return $comp->howManySercsHasEachTeamFinished();
        });
        $avgTime = Cache::remember('live.getAverageSercTime', 10, function () use ($comp) {
            // This is not ideal, should make comp org select which serc is dry
            $serc = SERC::where('competition', $comp->id)->where('name', 'LIKE', '%Dry%')->first();


            return $serc->getAverageTimeBetweenTeams();
        });

        return response()->json(['sercsFinished' => $sercsFinished, 'avgTime' => $avgTime, 'sercStartTime' => $comp->serc_start_time ? $comp->serc_start_time->timestamp * 1000 : null, 'heatsFinished' => $comp->whichSpeedEventHeatsHaveFinished()]);
    }
}
