<?php


namespace App\Stats\Statables;

use App\Models\Club;
use App\Stats\Statable;
use App\Stats\StatTarget;
use Illuminate\Support\Facades\DB;

class FastestTimesStat extends Statable
{

    private string $baseQuery = "SELECT * FROM (SELECT *, RANK() OVER (PARTITION BY event ORDER BY time) AS r FROM (
        SELECT c.name AS competition, c.id AS competition_id, se.id AS event, cl.name AS club, ct.team, t.time, t.points, t.place FROM stats_times t INNER JOIN competitions c ON c.id=t.competition INNER JOIN speed_events se ON se.id=t.event INNER JOIN competition_teams ct ON ct.id=t.team INNER JOIN clubs cl ON cl.id=ct.club WHERE time > 4 :WHERE: ORDER BY event) tt) ttt WHERE ttt.r=1;";

    protected string $templateName = "fastest-times";



    public function forGlobal(): array
    {
        return DB::select(str_replace(":WHERE:", "", $this->baseQuery));
    }

    public function forClub(Club $club): array
    {
        return DB::select(str_replace(":WHERE:", "  AND cl.id = ?", $this->baseQuery), [$club->id]);
    }

    public function forTeam(Club $club, string $team): array
    {
        return DB::select(str_replace(":WHERE:", " AND cl.id = ? AND ct.team = ?", $this->baseQuery), [$club->id, $team]);
    }


    
}