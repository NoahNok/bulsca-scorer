<?php

namespace App\Models;

use App\DTO\MasterResult;
use App\Scoring\Engine\MasterScorer;
use App\Scoring\Engine\MasterStanding;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterSchema extends Model
{
    use HasFactory;

    protected $casts = [
        'schema' => 'array',
    ];

    public function getCompetition()
    {
        return $this->hasOne(Competition::class, 'id', 'competition');
    }

    /**
     * @return Collection<int, ResultSchema>
     */
    public function getSheets(): Collection
    {

        $sheetIds = array_map(fn($sheet) => $sheet['id'], $this->schema['sheets'] ?? []);
        $sheetWeights = array_column($this->schema['sheets'] ?? [], 'weight', 'id');
        $sheets = ResultSchema::whereIn('id', $sheetIds)->get();

        $sheets->each(function ($sheet) use ($sheetWeights) {
            $sheet->weight = $sheetWeights[$sheet->id];
        });

        return $sheets;
    }

    /**
     * Who getResults() left out for having no Club to count for, by sheet name: `[sheet => [names]]`
     *
     * @var array<string, string[]>
     */
    public array $withoutClub = [];

    /**
     * The id of the Club an entry counts for: its own as a Club, a team's, or a competitor's team's
     */
    private static function clubOf(Model $entity): ?int
    {
        return match (true) {
            $entity instanceof Club => $entity->id,
            $entity instanceof CompetitionTeam => $entity->club,
            $entity instanceof Competitor => $entity->getTeam?->club,
            default => null,
        };
    }

    public function getResults()
    {

        $sheets = $this->getSheets()->each(function ($sheet) {
            $sheet->results = $sheet->getResults();
        });

        $this->withoutClub = [];
        foreach ($sheets as $sheet) {
            $names = $sheet->results->filter(fn ($result) => $result->entity && self::clubOf($result->entity) === null)
                ->map(fn ($result) => $result->entity->getName($this->getCompetition))
                ->unique()->values()->all();
            if ($names) {
                $this->withoutClub[$sheet->name] = $names;
            }
        }
        $schema = $this->schema;

        $group_on = $schema['group_on'] ?? 'club';;
        $sum_over = $schema['sum_over'] ?? 'position';
        $default_value = $schema['default_value'];
        $exclude = $schema['exclude'] ?? [];

        $target_entities = match ($group_on) {
            'club' => $this->getCompetition->getClubs,
        };

        // Specify entities we want
        $target_entities = $target_entities->whereNotIn('id', $exclude);

        // Each sheet's rows with the Club each counts for. An entry without a Club counts for none.
        $sheetRows = [];
        foreach ($sheets as $sheet) {
            $sheetRows[$sheet->id] = $sheet->results
                ->map(fn ($result) => [$result->entity ? self::clubOf($result->entity) : null, $result])
                ->values()->all();
        }

        $standings = MasterScorer::score($target_entities->pluck('id')->all(), $sheetRows, $default_value, $sum_over);

        $clubs = $target_entities->keyBy('id');

        return collect($standings)->map(fn (MasterStanding $standing) => new MasterResult(
            $clubs[$standing->clubId],
            $standing->sheetResults,
            $standing->total,
            $standing->position,
        ));
    }
}
