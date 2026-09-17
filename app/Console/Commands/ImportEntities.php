<?php

namespace App\Console\Commands;

use App\Models\Club;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\Competitor;
use App\Models\League;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportEntities extends Command
{
    protected $signature = 'import-entities {competition_id} {csv}';

    protected $description = 'Import clubs, teams, competitors, and optional leagues from a CSV file';

    public function handle(): int
    {
        $competitionId = $this->argument('competition_id');
        $csvPath = $this->argument('csv');

        if (!Competition::find($competitionId)) {
            $this->error("Competition not found: {$competitionId}");

            return self::FAILURE;
        }

        if (!File::exists($csvPath) || !is_readable($csvPath)) {
            $this->error("CSV file not found or not readable: {$csvPath}");

            return self::FAILURE;
        }

        $handle = fopen($csvPath, 'rb');

        if ($handle === false) {
            $this->error("Unable to open CSV file: {$csvPath}");

            return self::FAILURE;
        }

        try {
            $firstLine = fgets($handle);

            if ($firstLine === false) {
                throw new \RuntimeException('CSV file is empty.');
            }

            $delimiter = $this->detectDelimiter($firstLine);
            $header = str_getcsv($firstLine, $delimiter);

            $header = array_map(static fn($value) => strtolower(trim((string) $value)), $header);
            $header[0] = ltrim($header[0], "\xEF\xBB\xBF");
            $expectedColumns = ['league', 'club', 'team'];

            if (array_slice($header, 0, 3) !== $expectedColumns || count($header) < 4) {
                throw new \RuntimeException('CSV must start with League, Club, and Team columns, followed by at least one competitor column.');
            }

            $imported = DB::transaction(function () use ($handle, $competitionId, $delimiter) {
                $imported = 0;
                $lineNumber = 1;
                $teamIndexes = [];

                while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                    $lineNumber++;

                    if ($this->isEmptyRow($row)) {
                        continue;
                    }

                    if (count($row) < 3) {
                        throw new \RuntimeException("CSV row {$lineNumber} has fewer than three columns.");
                    }

                    $leagueName = trim((string) ($row[0] ?? ''));
                    $clubName = trim((string) ($row[1] ?? ''));
                    $teamName = trim((string) ($row[2] ?? ''));

                    if ($clubName === '') {
                        throw new \RuntimeException("CSV row {$lineNumber} has no club name.");
                    }

                    $league = null;
                    if ($leagueName !== '') {
                        $league = League::where('name', $leagueName)
                            ->where('competition', $competitionId)
                            ->first();

                        if ($league === null) {
                            $league = new League();
                            $league->name = $leagueName;
                            $league->competition = $competitionId;
                            $league->save();
                        }
                    }

                    $club = Club::firstOrCreate([
                        'name' => $clubName,
                        'competition' => $competitionId,
                    ]);

                    if ($teamName === '') {
                        if ($league === null) {
                            throw new \RuntimeException("CSV row {$lineNumber} needs a team name when no league is specified.");
                        }

                        $teamKey = $club->id . ':' . $league->id;
                        $teamIndexes[$teamKey] = ($teamIndexes[$teamKey] ?? 0) + 1;
                        $teamName = $this->generatedTeamName($league, $teamIndexes[$teamKey]);
                    }

                    $team = CompetitionTeam::create([
                        'team' => $teamName,
                        'club' => $club->id,
                        'competition' => $competitionId,
                        'league' => $league?->id,
                    ]);

                    if ($league !== null) {
                        $team->leagues()->attach($league);
                    }

                    foreach (array_slice($row, 3) as $competitorName) {
                        $competitorName = trim((string) $competitorName);

                        if ($competitorName === '') {
                            continue;
                        }

                        $competitor = Competitor::create([
                            'name' => $competitorName,
                            'competition' => $competitionId,
                            'team' => $team->id,
                            'league' => $league?->id,
                        ]);

                        if ($league !== null) {
                            $competitor->leagues()->attach($league);
                        }
                    }

                    $imported++;
                }

                return $imported;
            });
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        } finally {
            fclose($handle);
        }

        $this->info("Imported {$imported} row(s).");

        return self::SUCCESS;
    }

    private function generatedTeamName(League $league, int $teamIndex): string
    {
        $initials = collect(preg_split('/[\s_-]+/', trim($league->name)) ?: [])
            ->map(static fn($word) => preg_replace('/[^[:alnum:]].*$/', '', $word))
            ->filter()
            ->map(static fn($word) => strtoupper(substr($word, 0, 1)))
            ->implode('');

        return trim($initials) . ' - ' . $teamIndex;
    }

    private function isEmptyRow(array $row): bool
    {
        return count(array_filter($row, static fn($value) => trim((string) $value) !== '')) === 0;
    }

    private function detectDelimiter(string $line): string
    {
        $commaColumns = count(str_getcsv($line, ','));
        $semicolonColumns = count(str_getcsv($line, ';'));

        return $semicolonColumns > $commaColumns ? ';' : ',';
    }
}
