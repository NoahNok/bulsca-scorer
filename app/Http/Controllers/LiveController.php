<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LiveController extends Controller
{
    public function index()
    {

        $comp = Competition::orderBy(DB::raw('ABS(DATEDIFF(competitions.when, NOW()))'), 'asc')->first();

        return view('live.index', ['comp' => $comp]);
    }

    public function howManySercsHasEachTeamFinished(Competition $comp)
    {


        $data = Cache::remember('live.howManySercsHasEachTeamFinished', 60 * 5, function () use ($comp) {
            return $comp->howManySercsHasEachTeamFinished();
        });

        return response()->json($data);
    }
}
