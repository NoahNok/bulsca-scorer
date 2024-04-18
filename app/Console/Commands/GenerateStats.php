<?php

namespace App\Console\Commands;

use App\Models\Competition;
use App\Models\Stats\ResultStat;
use App\Models\Stats\SercStat;
use App\Models\Stats\SpeedEventStat;
use Illuminate\Console\Command;
use function Laravel\Prompts\info;
use function Laravel\Prompts\progress;

class GenerateStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stats:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates stats for all league competitions. This will delete any existing stats.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        info("Deleting old stats");
        ResultStat::truncate();
        SercStat::truncate();
        SpeedEventStat::truncate();


        $comps = Competition::where('isLeague', true)->where('public_results', true)->get();

        info("Found " . count($comps) . " competitions to generate stats for");


        progress(
            label: 'Generating stats',
            steps: $comps,
            callback: function ($comp, $progress) {
                $progress
                    ->label("Generating for {$comp->name}");
                    
         
                return $comp->generateStats();
            },
            hint: 'This may take some time.',
        );
       

        return Command::SUCCESS;
    }
}
