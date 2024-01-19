<?php

namespace App\Stats\Statables\Team;

use App\Models\Club;
use App\Stats\Statable;
use App\Stats\StatsTeam;

class TeamLeagueData extends TeamBase
{


    public function __construct($league)
    {
        parent::__construct('team-league-data');
        $this->baseOptions = ['league' => $league];
    }

    public function teamCompute(StatsTeam $team, Club $club, array $options)
    {
        $club = \App\Models\Club::where('name', 'LIKE', '%' . $options['club'] . '%')->firstOrFail();

        $data = [];

        $data['placings'] = $team->getPlacings($options['league']);
        $data['competedAt'] = $team->getCompetitionsCompetedAt();
        $data['distinctTeams'] = $club->getDistinctTeams();
        $data['league'] = $options['league'] == 'O' ? 'Overall' : $options['league'];
        $data['team'] = $team->getTeamName();

        return $data;
    }
}
