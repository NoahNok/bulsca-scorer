<?php

namespace App\Http\Controllers;

use App\Stats\Statables\Club\ClubCompetedAt;
use App\Stats\Statables\Club\ClubLeagueData;
use App\Stats\Statables\Club\ClubSercRecords;
use App\Stats\Statables\Club\ClubSpeedRecords;
use App\Stats\Statables\CompetitionPlacingGraph;
use App\Stats\Statables\FastestTimesStat;
use App\Stats\Statables\SercStats;
use App\Stats\StatsManager;
use App\Stats\StatsTeam;
use App\Stats\StatTarget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use NumberFormatter;

class PublicStatsController extends Controller
{

    private array $clubStats = [FastestTimesStat::class, CompetitionPlacingGraph::class, SercStats::class], $teamStats = [FastestTimesStat::class, CompetitionPlacingGraph::class, SercStats::class];


    public function clubs()
    {
        $fts = new FastestTimesStat(StatTarget::GLOBAL);
        $ss = new SercStats(StatTarget::GLOBAL);
        return view('public-results.stats.clubs', ['clubs' => StatsManager::getStatableClubs(), 'fastestTimes' => $fts->computeAndRender(), 'sercStats' => $ss->computeAndRender(['hideBlock' => true])]);
    }

    public function club(string $clubName)
    {
        $club = StatsManager::getClubFromName($clubName);
        $teams = StatsManager::getClubTeams($club);

        $data = [];

        foreach ($this->clubStats as $stat) {
            $stat = new $stat(StatTarget::CLUB, $club);
            $data[] = $stat->computeAndRender(['club' => $club]);
        }

        return view('public-results.stats.club', ['club' => $club, 'teams' => $teams, 'data' => $data]);
      
    }

    public function team(string $clubName, string $teamName)
    {

        $club = StatsManager::getClubFromName($clubName);
        $teams = StatsManager::getClubTeams($club);

        $data = [];

        foreach ($this->teamStats as $stat) {
            $stat = new $stat(StatTarget::TEAM, $club, $teamName);
            $data[] = $stat->computeAndRender(['club' => $club]);
        }

        return view('public-results.stats.team', ['club' => $club, 'team' => $teamName, 'teams' => $teams, 'data' => $data]);
        
    }

    public function compare(string $team1, string $team2)
    {

        $team1Split = explode(".", $team1);
        $team2Split = explode(".", $team2);

        $team1Club = StatsManager::getClubFromName($team1Split[0]);
        $team2Club = StatsManager::getClubFromName($team2Split[0]);

        $team1Team = $team1Split[1];
        $team2Team = $team2Split[1];

        $data1 = [];
        $data2 = [];

        if ($team1Club != null) {
            foreach ($this->teamStats as $stat) {
                $stat = new $stat(StatTarget::TEAM, $team1Club, $team1Team);
                $data1[] = $stat->computeAndRender(['club' => $team1Club]);
    
  
            }
        }

        if ($team2Club != null) {
            foreach ($this->teamStats as $stat) {
                $stat = new $stat(StatTarget::TEAM, $team2Club, $team2Team);
                $data2[] = $stat->computeAndRender(['club' => $team2Club]);
    
  
            }
        }

 

        return view('public-results.stats.compare', ['data1' => $data1, 'data2' => $data2, 'team1' => ($team1Club?->name ?? "") . ' ' . $team1Team, 'team2' => ($team2Club?->name ?? "") . ' ' . $team2Team, 'team1Slug' => ($team1Club?->name ?? "") . '.' . $team1Team, 'team2Slug' => ($team2Club?->name ?? "") . '.' . $team2Team]);
    }
}
