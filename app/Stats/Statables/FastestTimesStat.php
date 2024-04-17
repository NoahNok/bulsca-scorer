<?php


namespace App\Stats\Statables;

use App\Models\Club;
use App\Stats\Statable;
use App\Stats\StatTarget;
use Illuminate\Support\Facades\DB;

class FastestTimesStat extends Statable
{

    private string $baseQuery = "SELECT c.name AS competition, c.id AS competition_id, se.id AS event, cl.name AS club, ct.team, t1.time, t1.points, t1.place FROM stats_times t1 JOIN (SELECT event, MIN(time) AS fastest_time FROM stats_times GROUP BY event) t2 ON t1.event=t2.event AND t1.time=t2.fastest_time INNER JOIN
    speed_events se ON se.id=t1.event INNER JOIN competitions c ON c.id=t1.competition INNER JOIN competition_teams ct ON ct.id=t1.team INNER JOIN clubs cl ON cl.id=ct.club :WHERE: ORDER BY t1.event";

    protected string $templateName = "fastest-times";



    public function forGlobal(): array
    {
        return DB::select(str_replace(":WHERE:", "", $this->baseQuery));
    }

    public function forClub(Club $club): array
    {
        return DB::select(str_replace(":WHERE:", "  WHERE cl.id = ?", $this->baseQuery), [$club->id]);
    }

    public function forTeam(Club $club, string $team): array
    {
        return DB::select(str_replace(":WHERE:", "WHERE cl.id = ? AND ct.team = ?", $this->baseQuery), [$club->id, $team]);
    }


    
}