<?php

namespace App\Console\Commands;

use App\Models\Competition;
use App\Models\Competition\CompetitionScoringSettings;
use Illuminate\Console\Command;

class CreateCompetitionSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'competition:create-settings';

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
        foreach (Competition::all() as $comp) {
            if ($comp->getScoringSettings == null) {
                $css = new CompetitionScoringSettings();
                $css->competition = $comp->id;
                $css->save();
            }
        }
    }
}
