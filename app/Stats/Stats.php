<?php

namespace App\Stats;

use Illuminate\Support\Facades\DB;

class Stats
{

    public static function getStatableClubs()
    {
        return DB::select("SELECT c.name FROM competition_teams ct INNER JOIN clubs c ON c.id=ct.club INNER JOIN competitions cp ON cp.id=ct.competition WHERE cp.isLeague=true GROUP BY c.name ORDER BY c.name;");
    }

    public static function getGlobalSpeedEventRecords()
    {
        return DB::select('SELECT * FROM (SELECT se.id AS event_id, se.name AS event_name, MIN(CAST(result AS UNSIGNED)) AS record, sr.competition_team, ct.team, c.name, cp.name AS comp_name, cp.id AS comp_id, RANK() OVER (PARTITION BY se.id ORDER BY MIN(CAST(result
        AS UNSIGNED))) AS place FROM speed_results sr INNER JOIN competition_speed_events cse ON cse.id=sr.event INNER JOIN speed_events se ON se.id=cse.event INNER JOIN competition_teams ct ON ct.id=sr.competition_team INNER JOIN clubs
        c ON c.id=ct.club INNER JOIN competitions cp ON cp.id=ct.competition WHERE result>4 AND disqualification IS NULL AND cp.isLeague=true GROUP BY se.id,sr.competition_team ORDER BY se.id, record) AS t WHERE t.place=1;');
    }

    public static function getAllTeams()
    {
        return DB::select('SELECT DISTINCT ct.team, c.name FROM competition_teams ct INNER JOIN clubs c ON c.id=ct.club INNER JOIN competitions cp ON cp.id=ct.competition WHERE cp.isLeague=true ORDER BY c.name, ct.team;');
    }
}
