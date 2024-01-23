<?php

namespace App\Stats\Statables\Club;

use App\Stats\Statable;

class ClubSpeedRecords extends Statable
{

    public function __construct()
    {
        parent::__construct('club-speed-records');
    }

    public function compute(array $options)
    {
        $club = \App\Models\Club::where('name', 'LIKE', '%' . $options['club'] . '%')->firstOrFail();

        return $club->getClubRecords();
    }
}
