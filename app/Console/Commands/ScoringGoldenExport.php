<?php

namespace App\Console\Commands;

use App\Models\Competition;
use App\Scoring\Golden\CompetitionFixture;
use App\Scoring\Golden\GoldenJson;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Writes competitions out as golden fixtures (tests/Golden). Run it against a copy of the live
 * database, then `UPDATE_GOLDEN=1 php artisan test --testsuite=Golden` to record what the engine
 * makes of them. See docs/scoring-engine.md.
 */
class ScoringGoldenExport extends Command
{
    protected $signature = 'scoring:golden-export {competition* : Competition ids} {--path=tests/Golden/fixtures : Where the fixtures go}';

    protected $description = 'Export competitions\' scoring inputs as golden test fixtures (competitors made anonymous)';

    public function handle(): int
    {
        foreach ($this->argument('competition') as $id) {
            $comp = Competition::find($id);

            if (!$comp) {
                $this->error("No competition {$id}");

                return Command::FAILURE;
            }

            $fixture = CompetitionFixture::export($comp);
            $path = base_path($this->option('path') . '/' . Str::slug($comp->name) . "-{$comp->id}/input.json");

            GoldenJson::write($path, $fixture);

            $rows = array_sum(array_map(fn($table) => count($table['rows']), $fixture['tables']));
            $this->info("{$comp->name}: {$rows} rows → {$path}");
        }

        return Command::SUCCESS;
    }
}
