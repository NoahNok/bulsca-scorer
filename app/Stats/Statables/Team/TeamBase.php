<?php

namespace App\Stats\Statables\Team;

use App\Models\Club;
use App\Stats\Statable;
use App\Stats\Stats;
use App\Stats\StatsTeam;

abstract class TeamBase extends Statable
{


    public function compute(array $options)
    {
        $club = \App\Models\Club::where('name', 'LIKE', '%' . $options['club'] . '%')->firstOrFail();
        $team = new StatsTeam($club, $options['team']);

        return $this->teamCompute($team, $club, $options);
    }

    public abstract function teamCompute(StatsTeam $team, Club $club, array $options);
}
