<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competitor extends CompetitionTeam
{
    protected $table = 'competition_teams';


    public function getFullname()
    {

        $names = $this->team;

        $swimmers = Competitor::where('club', $this->club)->get();

        if (count($swimmers) > 1) {
            $pair = $swimmers->where('id', "!=", $this->id)->first(); // get the other swimmer by finding the other swimmer with not the current id
            $names .= " & " . $pair->team;
        }


        return $names . " - " . $this->getClub->name . " (" . $this->getClub->region . ")" . " - " . $this->getLeague->name;
    }
}
