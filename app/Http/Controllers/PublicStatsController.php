<?php

namespace App\Http\Controllers;

use App\Stats\Statables\Club\ClubCompetedAt;
use App\Stats\Statables\Club\ClubLeagueData;
use App\Stats\Statables\Club\ClubSercRecords;
use App\Stats\Statables\Club\ClubSpeedRecords;
use App\Stats\StatsManager;
use App\Stats\StatsTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use NumberFormatter;

class PublicStatsController extends Controller
{

    private array $clubStats = [], $teamStats = [];

 

    public function clubs()
    {
        return view('public-results.stats.clubs', ['clubs' => StatsManager::getStatableClubs()]);
    }

    public function club(string $clubName)
    {
        $club = StatsManager::getClubFromName($clubName);
        $teams = StatsManager::getClubTeams($club);

        return view('public-results.stats.club', ['club' => $club, 'teams' => $teams]);
      
    }

    public function team(string $clubName, string $teamName)
    {
        
    }

    public function compare(string $team1, string $team2)
    {

    }
}
