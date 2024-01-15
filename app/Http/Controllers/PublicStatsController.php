<?php

namespace App\Http\Controllers;

use App\Stats\StatsTeam;
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

    public function team(string $clubName, string $teamName)
    {
        $clubName = Str::lower($clubName);
        $teamName = Str::lower($teamName);

        $data = Cache::remember('team-stats-' . $clubName . '-' . $teamName, 60 * 60 * 24, function () use ($clubName, $teamName) {
            $club = \App\Models\Club::where('name', 'LIKE', '%' . $clubName . '%')->firstOrFail();
            $team = new StatsTeam($club, $teamName);

            $allPlacings['Overall'] = $team->getPlacings();
            $allPlacings['A-League'] = $team->getPlacings('A');
            $allPlacings['B-League'] = $team->getPlacings('B');
            $speedRecords = $team->getTeamRecords();
            $sercRecords = $team->getBestSercs();
            $distinctTeams = $club->getDistinctTeams();
            $competedAt = $team->getCompetitionsCompetedAt();

            return compact('club', 'team', 'allPlacings', 'speedRecords', 'sercRecords', 'distinctTeams',  'competedAt');
        });

        return view('public-results.stats.team', $data);
    }
}
