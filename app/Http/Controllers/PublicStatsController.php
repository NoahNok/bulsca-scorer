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

        $data = Cache::rememberForever('club-stats-' . $clubName, function () use ($clubName) {
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

        $data = Cache::rememberForever('team-stats-' . $clubName . '-' . $teamName, function () use ($clubName, $teamName) {
            $club = \App\Models\Club::where('name', 'LIKE', '%' . $clubName . '%')->firstOrFail();
            $team = new StatsTeam($club, $teamName);



            $distinctTeams = $club->getDistinctTeams();

            $strClub = $clubName;
            $strTeam = $teamName;

            return compact('club', 'team', 'distinctTeams', 'strClub', 'strTeam');
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

        $data1 = Cache::rememberForever('team-stats-' . $t1s[0] . '-' . $t1s[1], function () use ($t1s) {
            $club = \App\Models\Club::where('name', 'LIKE', '%' . $t1s[0] . '%')->first();

            if (!$club) {
                return null;
            }

            $team = new StatsTeam($club, $t1s[1]);

            $distinctTeams = $club->getDistinctTeams();

            $strClub = $t1s[0];
            $strTeam = $t1s[1];

            return compact('club', 'team', 'distinctTeams', 'strClub', 'strTeam');
        });

        $data2 = Cache::rememberForever('team-stats-' . $t2s[0] . '-' . $t2s[1], function () use ($t2s) {
            $club = \App\Models\Club::where('name', 'LIKE', '%' . $t2s[0] . '%')->first();

            if (!$club) {
                return null;
            }

            $team = new StatsTeam($club, $t2s[1]);

            $distinctTeams = $club->getDistinctTeams();

            $strClub = $t2s[0];
            $strTeam = $t2s[1];

            return compact('club', 'team', 'distinctTeams', 'strClub', 'strTeam');
        });

        $stats = $this->teamStats;

        return view('public-results.stats.compare', compact('data1', 'data2', 'stats'));
    }
}
