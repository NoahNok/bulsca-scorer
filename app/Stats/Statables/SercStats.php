<?php


namespace App\Stats\Statables;

use App\Models\Club;
use App\Stats\Statable;
use App\Stats\StatTarget;
use Illuminate\Support\Facades\DB;

class SercStats extends Statable
{

    private string $baseQuery = "SELECT c.name AS competition_name, c.id AS competition_id, c.when, s.name AS serc_name, s.id AS serc_id, cl.name
    AS club, ct.team, ss.score, ss.points, ss.place, (SELECT SUM(weight)*10 FROM serc_marking_points WHERE serc=s.id) AS serc_max FROM stats_serc ss INNER JOIN sercs s ON ss.event=s.id INNER JOIN competitions c ON c.id=ss.competition INNER JOIN competition_teams ct ON ct.id=ss.team INNER JOIN clubs cl ON cl.id=ct.club :WHERE: ORDER BY score/serc_max DESC;";

    protected string $templateName = "serc-stats";



    public function forGlobal(): array
    {
        return DB::select(str_replace(":WHERE:", "", $this->baseQuery));
    }

    public function forClub(Club $club): array
    {
        return DB::select(str_replace(":WHERE:", "  WHERE cl.id = ? ", $this->baseQuery), [$club->id]);
    }

    public function forTeam(Club $club, string $team): array
    {
        return DB::select(str_replace(":WHERE:", "WHERE cl.id = ? AND ct.team = ?", $this->baseQuery), [$club->id, $team]);
    }


    
}