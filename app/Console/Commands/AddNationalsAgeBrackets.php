<?php

namespace App\Console\Commands;

use App\Models\League;
use Illuminate\Console\Command;

class AddNationalsAgeBrackets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nationals:age-brackets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds all the age brackets for nationals as league options';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $brackets = ['Junior Girls Pairs', 'Junior Open Pairs', 'Senior Girls Pairs', 'Senior Open Pairs', 'Adult Ladies Pairs', 'Adult Open Pairs', 'Ladies', 'Open', 'Masters Ladies 30-39', 'Masters Open 30-39', 'Masters Ladies 40-49', 'Masters Open 40-49', 'Masters Ladies 50-59', 'Masters Open 50-59', 'Masters Ladies 60-69', 'Masters Open 60-69', 'Masters Ladies 70+', 'Masters Open 70+'];

        foreach ($brackets as $bracket) {

            if (League::where('name', $bracket)->exists()) {
                continue;
            }

            $l = new League();
            $l->name = $bracket;
            $l->scoring_type = 'rlss-nationals';
            $l->save();

     
        }
    }
}
