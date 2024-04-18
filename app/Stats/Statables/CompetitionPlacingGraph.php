<?php


namespace App\Stats\Statables;

use App\Models\Club;
use App\Stats\Statable;
use App\Stats\StatTarget;
use Illuminate\Support\Facades\DB;

class CompetitionPlacingGraph extends Statable
{

    private string $baseQuery = "SELECT c.name AS competition, c.id AS competition_id, c.when, cl.name, ct.team, sr.league, sr.points, sr.place FROM stats_results sr INNER JOIN competition_teams ct ON sr.team=ct.id INNER JOIN competitions c ON sr.competition=c.id INNER JOIN clubs cl ON ct.club=cl.id :WHERE: ORDER BY c.when;";

    protected string $templateName = "placing-graph";



    public function forGlobal(): array
    {
        return [];
    }

    public function forClub(Club $club): array
    {
        return DB::select(str_replace(":WHERE:", "  WHERE cl.id = ? ", $this->baseQuery), [$club->id]);
    }

    public function forTeam(Club $club, string $team): array
    {
        return DB::select(str_replace(":WHERE:", "WHERE cl.id = ? AND ct.team = ? ", $this->baseQuery), [$club->id, $team]);
    }


    
}