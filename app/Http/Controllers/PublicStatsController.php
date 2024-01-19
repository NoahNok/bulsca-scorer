<?php

namespace App\Http\Controllers;

use App\Stats\Statables\Club\ClubCompetedAt;
use App\Stats\Statables\Club\ClubLeagueData;
use App\Stats\Statables\Club\ClubSercRecords;
use App\Stats\Statables\Club\ClubSpeedRecords;
use App\Stats\StatsTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use NumberFormatter;

class PublicStatsController extends Controller
{

    private array $clubStats = [], $teamStats = [];

    public function __construct()
    {
        $this->clubStats = [
            new ClubSpeedRecords(),
            new ClubSercRecords(),
            new ClubCompetedAt(),
            new ClubLeagueData('O'),
            new ClubLeagueData('A'),
            new ClubLeagueData('B'),
        ];

        $this->teamStats = [
            new \App\Stats\Statables\Team\TeamSpeedRecords(),
            new \App\Stats\Statables\Team\TeamSercRecords(),
            new \App\Stats\Statables\Team\TeamCompetedAt(),
            new \App\Stats\Statables\Team\TeamLeagueData('O'),
            new \App\Stats\Statables\Team\TeamLeagueData('A'),
            new \App\Stats\Statables\Team\TeamLeagueData('B'),
        ];
    }

    public function clubs()
    {
        return view('public-results.stats.clubs');
    }

    public function club(string $clubName)
    {
        $clubName = Str::lower($clubName);

        $data = Cache::remember('club-stats-' . $clubName, 60 * 60 * 24, function () use ($clubName) {
            $club = \App\Models\Club::where('name', 'LIKE', '%' . $clubName . '%')->firstOrFail();
            $distinctTeams = $club->getDistinctTeams();

            return compact('club', 'distinctTeams');
        });

        foreach ($this->clubStats as $stat) {
            $stat->computeFor(['club' => $clubName]);
        }

        return view('public-results.stats.club', ['clubData' => $data, 'stats' => $this->clubStats]);
    }

    public function team(string $clubName, string $teamName)
    {
        $clubName = Str::lower($clubName);
        $teamName = Str::lower($teamName);

        $data = Cache::remember('team-stats-' . $clubName . '-' . $teamName, 60 * 60 * 24, function () use ($clubName, $teamName) {
            $club = \App\Models\Club::where('name', 'LIKE', '%' . $clubName . '%')->firstOrFail();
            $team = new StatsTeam($club, $teamName);



            $distinctTeams = $club->getDistinctTeams();

            return compact('club', 'team', 'distinctTeams');
        });

        foreach ($this->teamStats as $stat) {
            $stat->computeFor(['club' => $clubName, 'team' => $teamName]);
        }

        return view('public-results.stats.team', ['clubData' => $data, 'stats' => $this->teamStats]);
    }

    public function compare(string $team1, string $team2)
    {
        $t1s = explode('.', $team1 = Str::lower($team1), 2);
        $t2s = explode('.', $team2 = Str::lower($team2), 2);

        $data1 = Cache::remember('team-stats-' . $t1s[0] . '-' . $t1s[1], 60 * 60 * 24, function () use ($t1s) {
            $club = \App\Models\Club::where('name', 'LIKE', '%' . $t1s[0] . '%')->firstOrFail();
            $team = new StatsTeam($club, $t1s[1]);

            $allPlacings['Overall'] = $team->getPlacings();
            $allPlacings['A-League'] = $team->getPlacings('A');
            $allPlacings['B-League'] = $team->getPlacings('B');
            $speedRecords = $team->getTeamRecords();
            $sercRecords = $team->getBestSercs();
            $distinctTeams = $club->getDistinctTeams();
            $competedAt = $team->getCompetitionsCompetedAt();

            return compact('club', 'team', 'allPlacings', 'speedRecords', 'sercRecords', 'distinctTeams',  'competedAt');
        });

        $data2 = Cache::remember('team-stats-' . $t2s[0] . '-' . $t2s[1], 60 * 60 * 24, function () use ($t2s) {
            $club = \App\Models\Club::where('name', 'LIKE', '%' . $t2s[0] . '%')->firstOrFail();
            $team = new StatsTeam($club, $t2s[1]);

            $allPlacings['Overall'] = $team->getPlacings();
            $allPlacings['A-League'] = $team->getPlacings('A');
            $allPlacings['B-League'] = $team->getPlacings('B');
            $speedRecords = $team->getTeamRecords();
            $sercRecords = $team->getBestSercs();
            $distinctTeams = $club->getDistinctTeams();
            $competedAt = $team->getCompetitionsCompetedAt();

            return compact('club', 'team', 'allPlacings', 'speedRecords', 'sercRecords', 'distinctTeams',  'competedAt');
        });

        return view('public-results.stats.compare', compact('data1', 'data2'));
    }
}
