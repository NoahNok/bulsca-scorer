<?php

namespace App\Console\Commands;

use App\Models\Competition;
use App\Scoring\Golden\CompetitionSnapshot;
use App\Scoring\Golden\GoldenJson;
use Illuminate\Console\Command;

/**
 * Records everything the engine works out for the competitions in the connected database, or checks
 * it against an earlier recording. For proving an engine change moves nothing across every
 * competition there is, not only the golden fixtures:
 *
 *   php artisan scoring:snapshot --out=storage/app/scoring-before     (on the old code)
 *   php artisan scoring:snapshot --compare=storage/app/scoring-before (on the new code)
 */
class ScoringSnapshot extends Command
{
    protected $signature = 'scoring:snapshot {competition?* : Competition ids (default: all)} {--out= : Directory to record into} {--compare= : Directory of an earlier recording to check against}';

    protected $description = 'Record or compare the scoring engine\'s output for whole competitions';

    public function handle(): int
    {
        $out = $this->option('out');
        $compare = $this->option('compare');

        if (!$out && !$compare) {
            $this->error('Give --out to record or --compare to check.');

            return Command::FAILURE;
        }

        $ids = $this->argument('competition');
        $comps = Competition::when($ids, fn($query) => $query->whereIn('id', $ids))->orderBy('id')->get();

        $changed = 0;

        foreach ($comps as $comp) {
            $started = hrtime(true);
            $snapshot = CompetitionSnapshot::of($comp);
            $took = intdiv(hrtime(true) - $started, 1_000_000);

            if ($out) {
                GoldenJson::write("{$out}/{$comp->id}.json", $snapshot);
            }

            if (!$compare) {
                $this->line("{$comp->id} {$comp->name}: recorded ({$took}ms)");
                continue;
            }

            if (!is_file("{$compare}/{$comp->id}.json")) {
                $this->warn("{$comp->id} {$comp->name}: nothing recorded to compare with");
                continue;
            }

            $differences = GoldenJson::differences(GoldenJson::read("{$compare}/{$comp->id}.json"), $snapshot);

            if (!$differences) {
                $this->line("{$comp->id} {$comp->name}: same ({$took}ms)");
                continue;
            }

            $changed++;
            $this->error("{$comp->id} {$comp->name}: " . count($differences) . ' differences');
            foreach (array_slice($differences, 0, 15) as $difference) {
                $this->line("    {$difference}");
            }
        }

        if ($compare) {
            $changed ? $this->error("{$changed} of {$comps->count()} competitions changed.") : $this->info("All {$comps->count()} competitions score the same.");
        }

        return $changed ? Command::FAILURE : Command::SUCCESS;
    }
}
