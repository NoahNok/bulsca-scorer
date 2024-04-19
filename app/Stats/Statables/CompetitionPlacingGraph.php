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
        return $this->process(DB::select(str_replace(":WHERE:", "  WHERE cl.id = ? ", $this->baseQuery), [$club->id]));
    }

    public function forTeam(Club $club, string $team): array
    {
        return $this->process(DB::select(str_replace(":WHERE:", "WHERE cl.id = ? AND ct.team = ? ", $this->baseQuery), [$club->id, $team]));
    }


    private function process(array $data): array {
       
        $uniqueCompetitions = [];
     
        
        foreach ($data as $row) {

  
            $newData = ['name' => $row->competition, 'id' => $row->competition_id];
            // Add a new comp to the array if it doesn't exist for our unique comps
            if (!in_array($newData, $uniqueCompetitions)) {
                $uniqueCompetitions[] = $newData;
            }

        }
       
        // Now lets produce a array in the form [league][team] = place/null
        // which is ordered by the uniqueCompetitions array, where it is null if it has no entry

        $leagueData = [];
      
        foreach($data as $row) {
            $leagueKey = $row->league;

            if ($leagueKey == "O") {
                $leagueKey = "Overall";
            }

            $leagueData[$leagueKey][$row->team][$row->competition] = ['place' => $row->place, 'points' => $row->points, 'competition_id' => $row->competition_id, 'competition_name' => $row->competition];
        }
        




        return [
            "leagues" => $leagueData,
            "competitions" => $uniqueCompetitions,
        ];
    }


    
}