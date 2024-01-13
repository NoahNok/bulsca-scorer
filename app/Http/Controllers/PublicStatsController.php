<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use NumberFormatter;

class PublicStatsController extends Controller
{


    public function clubs()
    {
        return view('public-results.stats.clubs');
    }

    public function club(string $clubName)
    {

        $clubName = Str::lower($clubName);

        $data = Cache::remember('club-stats-' . $clubName, 60 * 60 * 24, function () use ($clubName) {
            $club = \App\Models\Club::where('name', 'LIKE', '%' . $clubName . '%')->firstOrFail();

            $allPlacings['Overall'] = $club->getPlacings();
            $allPlacings['A-League'] = $club->getPlacings('A');
            $allPlacings['B-League'] = $club->getPlacings('B');
            $speedRecords = $club->getClubRecords();
            $sercRecords = $club->getBestSercs();
            $distinctTeams = $club->getDistinctTeams();
            $competedAt = $club->getCompetitionsCompetedAt();

            return compact('club', 'allPlacings', 'speedRecords', 'sercRecords', 'distinctTeams', 'competedAt');
        });


        return view('public-results.stats.club', $data);
    }
}
