<?php

namespace App\Scoring\Golden;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\SERC;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * A competition's scoring inputs as a file: every row the engine reads to score it (entries, times,
 * marks, penalties, DQs, formulas, sheets), and nothing else. Exported from a real database and
 * loaded into an empty test one, keeping ids so the engine sees exactly what it saw live.
 *
 * Competitors are people, so their names are swapped for "Competitor 1", "Competitor 2"…; clubs and
 * teams keep theirs, except where one is named after a competitor (a full name that's entered as a
 * competitor anywhere in the database). Pins, accounts, timestamps and anything else scoring doesn't
 * read is left out. A team named after someone who has never been entered as a competitor can't be
 * spotted: read a new fixture's team names before committing it.
 */
class CompetitionFixture
{
    /** Columns never written to a fixture, whichever table they're on */
    private const DROPPED = ['created_at', 'updated_at'];

    /** Columns blanked (column => what's written instead) because they're secrets, free text, or point at rows the fixture doesn't carry */
    private const BLANKED = [
        'competitions' => ['digitalJudgePin' => null, 'digitalJudgeHeadPin' => null, 'wi_user' => null, 'championship_id' => null, 'status_message' => null, 'serc_start_time' => null, 'sercs_released_at' => null],
        'organisations' => ['logo' => null],
        'sercs' => ['image' => null],
        'serc_judges' => ['description' => ''],
        'serc_marking_points' => ['marking_point_template_id' => null],
    ];

    /** Free-text names that could have a person's name in them */
    private const NAMED = ['clubs' => ['name'], 'competition_teams' => ['team']];

    private const JSON_COLUMNS = ['schema', 'data'];

    public static function export(Competition $comp): array
    {
        $id = $comp->id;

        $speedIds = DB::table('competition_speed_events')->where('competition', $id)->pluck('id');
        $sercIds = DB::table('sercs')->where('competition', $id)->pluck('id');
        $sheetIds = DB::table('result_schemas')->where('competition', $id)->pluck('id');
        $leagueIds = DB::table('leagues')->where('competition', $id)->pluck('id');

        $entities = [
            'club' => DB::table('clubs')->where('competition', $id)->pluck('id'),
            'team' => DB::table('competition_teams')->where('competition', $id)->pluck('id'),
            'competitor' => DB::table('competitors')->where('competition', $id)->pluck('id'),
        ];

        $forEntities = function (string $table) use ($entities) {
            return DB::table($table)->where(function ($query) use ($entities) {
                foreach ($entities as $type => $ids) {
                    $query->orWhere(fn($q) => $q->where('entity_type', $type)->whereIn('entity_id', $ids));
                }
            });
        };

        $forEvents = function (string $table) use ($speedIds, $sercIds) {
            return DB::table($table)->where(function ($query) use ($speedIds, $sercIds) {
                $query->where(fn($q) => $q->whereIn('event_type', self::morphNames(CompetitionSpeedEvent::class))->whereIn('event_id', $speedIds))
                    ->orWhere(fn($q) => $q->whereIn('event_type', self::morphNames(SERC::class))->whereIn('event_id', $sercIds));
            });
        };

        $scoringSchemaIds = DB::table('competition_speed_events')->where('competition', $id)->pluck('scoring_schema')
            ->merge(DB::table('sercs')->where('competition', $id)->pluck('scoring_schema'))
            ->filter()->unique();

        $queries = [
            'organisations' => DB::table('organisations')->where('id', $comp->organisation),
            'competitions' => DB::table('competitions')->where('id', $id),
            'competition_scoring_settings' => DB::table('competition_scoring_settings')->where('competition', $id),
            'leagues' => DB::table('leagues')->where('competition', $id),
            'clubs' => DB::table('clubs')->where('competition', $id),
            'competition_teams' => DB::table('competition_teams')->where('competition', $id),
            'competitors' => DB::table('competitors')->where('competition', $id),
            'leagueables' => $forEntities('leagueables')->whereIn('league_id', $leagueIds),
            'entity_data' => $forEntities('entity_data'),
            'scoring_schemas' => DB::table('scoring_schemas')->whereIn('id', $scoringSchemaIds),
            'speed_events' => DB::table('speed_events')->whereIn('id', DB::table('competition_speed_events')->where('competition', $id)->pluck('event')),
            'competition_speed_events' => DB::table('competition_speed_events')->where('competition', $id),
            'entity_event_seeds' => DB::table('entity_event_seeds')->whereIn('speed_event', $speedIds),
            'speed_results' => DB::table('speed_results')->whereIn('event', $speedIds),
            'sercs' => DB::table('sercs')->where('competition', $id),
            'serc_judges' => DB::table('serc_judges')->whereIn('serc', $sercIds),
            'serc_marking_points' => DB::table('serc_marking_points')->whereIn('serc', $sercIds),
            'serc_results' => DB::table('serc_results')->whereIn('marking_point', DB::table('serc_marking_points')->whereIn('serc', $sercIds)->pluck('id')),
            'penalties' => $forEvents('penalties'),
            'disqualifications' => $forEvents('disqualifications'),
            'result_schemas' => DB::table('result_schemas')->where('competition', $id),
            'result_schema_events' => DB::table('result_schema_events')->whereIn('schema', $sheetIds),
            'master_schemas' => DB::table('master_schemas')->where('competition', $id),
        ];

        // Full names only, longest first, so "Ann Lee" inside "Joanne Leeson" isn't what gets swapped
        $people = DB::table('competitors')->distinct()->pluck('name')
            ->map(fn($name) => trim((string) $name))
            ->filter(fn($name) => strlen($name) > 5 && str_contains($name, ' '))
            ->sortByDesc(fn($name) => strlen($name))
            ->values()->all();

        $tables = [];
        foreach ($queries as $table => $query) {
            $rows = $query->orderBy('id')->get()->map(fn($row) => (array) $row)->all();

            if ($rows) {
                $tables[$table] = self::pack($table, $rows, $people);
            }
        }

        return ['competition' => $id, 'name' => $comp->name, 'tables' => $tables];
    }

    /**
     * Puts a fixture's rows into the current database, ids and all. Rows already there with the same
     * id (speed events a migration seeded, say) are overwritten.
     */
    public static function load(array $fixture): Competition
    {
        Schema::disableForeignKeyConstraints();

        try {
            foreach ($fixture['tables'] as $table => $packed) {
                $rows = array_map(function (array $values) use ($packed) {
                    $row = array_combine($packed['columns'], $values);

                    foreach (self::JSON_COLUMNS as $column) {
                        if (is_array($row[$column] ?? null)) {
                            $row[$column] = json_encode($row[$column]);
                        }
                    }

                    return $row;
                }, $packed['rows']);

                foreach (array_chunk($rows, 1000) as $chunk) {
                    DB::table($table)->upsert($chunk, ['id']);
                }
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        return Competition::findOrFail($fixture['competition']);
    }

    /**
     * A table's rows as one list of columns and a list of value rows, with what a fixture mustn't
     * carry dropped, blanked or made anonymous
     */
    private static function pack(string $table, array $rows, array $people): array
    {
        $columns = array_values(array_diff(array_keys($rows[0]), self::DROPPED));
        $person = 0;

        $packed = [];
        foreach ($rows as $row) {
            foreach (self::BLANKED[$table] ?? [] as $column => $blank) {
                if (array_key_exists($column, $row)) {
                    $row[$column] = $blank;
                }
            }

            if ($table === 'competitors') {
                $row['name'] = 'Competitor ' . ++$person;
            }

            foreach (self::NAMED[$table] ?? [] as $column) {
                $row[$column] = str_ireplace($people, 'Competitor', (string) $row[$column]);
            }

            foreach (self::JSON_COLUMNS as $column) {
                if (is_string($row[$column] ?? null)) {
                    $row[$column] = json_decode($row[$column], true);
                }
            }

            $packed[] = array_map(fn($column) => $row[$column], $columns);
        }

        return ['columns' => $columns, 'rows' => $packed];
    }

    /**
     * How a model's class has been written in morph columns over the years
     */
    private static function morphNames(string $class): array
    {
        return [$class, '\\' . $class];
    }
}
