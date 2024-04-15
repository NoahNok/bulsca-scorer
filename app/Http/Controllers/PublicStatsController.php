<?php

namespace App\Http\Controllers;

use App\Stats\Statables\Club\ClubCompetedAt;
use App\Stats\Statables\Club\ClubLeagueData;
use App\Stats\Statables\Club\ClubSercRecords;
use App\Stats\Statables\Club\ClubSpeedRecords;
use App\Stats\Statables\FastestTimesStat;
use App\Stats\StatsManager;
use App\Stats\StatsTeam;
use App\Stats\StatTarget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use NumberFormatter;

class PublicStatsController extends Controller
{

    private array $clubStats = [], $teamStats = [];

 

    public function clubs()
    {
        $fts = new FastestTimesStat(StatTarget::GLOBAL);
        return view('public-results.stats.clubs', ['clubs' => StatsManager::getStatableClubs(), 'fastestTimes' => $fts->computeAndRender()]);
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
