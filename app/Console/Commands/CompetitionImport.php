<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use function Laravel\Prompts\text;

class CompetitionImport extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'competition:import';

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
        $filePath = "/home/noah/L2023.xlsm";

        $importer = new \App\Importer\Importer($this);
        $importer->import($filePath);

        return Command::SUCCESS;
    }
}
