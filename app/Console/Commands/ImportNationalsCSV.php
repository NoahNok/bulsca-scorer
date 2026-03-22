<?php

namespace App\Console\Commands;

use App\Models\Club;
use App\Models\CompetitionTeam;
use App\Models\Competitor;
use App\Models\League;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportNationalsCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nationals:import-csv {competition_id} {csv}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $competition_id = $this->argument('competition_id');
        $csv_path = $this->argument('csv');



        if (!File::exists($csv_path)) {
            throw new \Exception("CSV file not found at path: $csv_path");
        }

        $rows = array_map('str_getcsv', file($csv_path));

        foreach ($rows as $row) {
            // Skip empty or malformed rows
            if (count($row) < 4) {
                continue;
            }

            [$club_name, $team_name, $league_name, $competitor, $optional_competitor] = array_map('trim', $row + [null, null, null, null, null]);

            // 1. Find or create Club
            $club = Club::firstOrCreate(
                ['name' => $club_name, 'competition' => $competition_id],
            );

            $league = League::where('name', $league_name)->where('competition', $competition_id)->first();

            if ($league == null) {
                $league = new League();
                $league->name = $league_name;
                $league->competition = $competition_id;
                $league->save();
            }

            // 2. Create CompetitionTeam
            $team = CompetitionTeam::create([
                'team' => $team_name,
                'club' => $club->id,
                'competition' => $competition_id,
                'league' => $league->id
            ]);

            // 3. Create Competitor(s)
            $competitorNames = [$competitor];
            if (!empty($optional_competitor)) {
                $competitorNames[] = $optional_competitor;
            }



            foreach ($competitorNames as $name) {
                Competitor::create([
                    'name' => $name,
                    'competition' => $competition_id,
                    'team' => $team->id,
                    'league' => $league->id,
                ]);
            }
        }
    }
}
