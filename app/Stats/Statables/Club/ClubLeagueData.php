<?php

namespace App\Stats\Statables\Club;

use App\Stats\Statable;

class ClubLeagueData extends Statable
{


    public function __construct($league)
    {
        parent::__construct('club-league-data');
        $this->baseOptions = ['league' => $league];
    }

    public function compute(array $options)
    {
        $club = \App\Models\Club::where('name', 'LIKE', '%' . $options['club'] . '%')->firstOrFail();

        $data = [];

        $data['placings'] = $club->getPlacings($options['league']);
        $data['competedAt'] = $club->getCompetitionsCompetedAt();
        $data['distinctTeams'] = $club->getDistinctTeams();
        $data['league'] = $options['league'] == 'O' ? 'Overall' : $options['league'];

        return $data;
    }
}
