<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompetitionSpeedEvent extends Model
{
    use HasFactory;

    public function getName()
    {
        return $this->hasOne(SpeedEvent::class, 'id', 'event')->first()->name;
    }

    public function getCompetition()
    {
        return $this->belongsTo(Competition::class, 'competition', 'id');
    }

    public function getTeams()
    {
        return $this->getCompetition->getCompetitionTeams();
    }

    public function getResults()
    {

        $record = 377030;
        $results = DB::select('SELECT *, RANK() OVER (ORDER BY points DESC) place FROM (SELECT sr.id, CONCAT(c.name, " ", ct.team) AS team, se.name as event, sr.result, sr.disqualification, IF(sr.result, LEAST((1-((sr.result-?)/(?))) * 1000, 1000), 0) AS points FROM speed_results sr INNER JOIN competition_speed_events cse ON cse.id=sr.event INNER JOIN speed_events se ON cse.event=se.id INNER JOIN competition_teams ct ON ct.id=sr.competition_team INNER JOIN clubs c ON c.id=ct.club WHERE cse.id=? ORDER BY result) t ORDER BY place;', [$record, $record, $this->id]);
        return $results;
    }
}
