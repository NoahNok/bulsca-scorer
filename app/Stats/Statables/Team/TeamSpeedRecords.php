<?php

namespace App\Stats\Statables\Team;

use App\Models\Club;
use App\Stats\Statable;
use App\Stats\StatsTeam;

class TeamSpeedRecords extends TeamBase
{

    public function __construct()
    {
        parent::__construct('team-speed-records', 'club-speed-records');
    }

    public function teamCompute(StatsTeam $team, Club $club, array $options)
    {
        return $team->getTeamRecords();
    }
}
