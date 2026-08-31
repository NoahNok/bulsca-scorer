<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use function Laravel\Prompts\info;
use function Laravel\Prompts\text;

class CompetitionImport extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'competition:import {filePath?} {name?} {when?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filePath = $this->argument('filePath') ?: text('What is the path to the score sheet?');

        $loader = new \App\Importer\Loader($this);
        $data = $loader->load($filePath);

        info("All events loaded. Starting import");

        $competitionDetails = [
            'name' => $this->argument('name') ?: text('What is the name of the competition?'),
            'when' => $this->argument('when') ?: text('When was the competition?')
        ];

        $importer = new \App\Importer\Importer();
        $comp = $importer->import($competitionDetails, $data['teams'], $data['speeds'], $data['sercs']);

        info("View at: " . route('comps.view', [$comp->id]));

        return Command::SUCCESS;
    }
}
