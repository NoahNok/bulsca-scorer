<?php

namespace App\Stats\Statables\Team;

use App\Models\Club;
use App\Stats\Statable;
use App\Stats\StatsTeam;

class TeamSercRecords extends TeamBase
{

    public function __construct()
    {
        parent::__construct('team-serc-records', 'club-serc-records');
    }

    public function teamCompute(StatsTeam $team, Club $club, array $options)
    {
        return $team->getBestSercs();
    }
}
