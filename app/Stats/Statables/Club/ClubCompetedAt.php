<?php

namespace App\Stats\Statables\Club;

use App\Stats\Statable;

class ClubCompetedAt extends Statable
{

    public function __construct()
    {
        parent::__construct('club-competed-at');
    }

    public function compute(array $options)
    {
        $club = \App\Models\Club::where('name', 'LIKE', '%' . $options['club'] . '%')->firstOrFail();

        return $club->getCompetitionsCompetedAt();
    }
}
