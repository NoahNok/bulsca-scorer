<?php

namespace App\Stats\Statables\Team;

use App\Models\Club;
use App\Stats\Statable;
use App\Stats\StatsTeam;

class TeamCompetedAt extends TeamBase
{

    public function __construct()
    {
        parent::__construct('team-competed-at', 'club-competed-at');
    }

    public function teamCompute(StatsTeam $team, Club $club, array $options)
    {
        return $team->getCompetitionsCompetedAt();
    }
}
