<?php

namespace App\Models;

use App\DTO\MasterResult;
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

        $final_results = collect();

        foreach ($target_entities as $entity) {
            $master_result = new MasterResult($entity);

            $total = 0;
            $sheetResults = [];

            foreach ($sheets as $sheet) {
                $resultForSheet = $sheet->results->first(function ($result) use ($entity) {
                    // An entry without a Club counts for none
                    $club = $result->entity ? self::clubOf($result->entity) : null;
                    return $club !== null && $club === $entity->id;
                });

                $resultForSheet = $resultForSheet ? $resultForSheet->{$sum_over} : $default_value;

                $sheetResults[$sheet->id] = $resultForSheet;

                $total += $resultForSheet;
            }

            $master_result->total = $total;
            $master_result->sheetResults = $sheetResults;

            $final_results->add($master_result);
        }

        // Fewest places wins, most points wins
        $final_results = $sum_over == 'position' ? $final_results->sortBy('total') : $final_results->sortByDesc('total');

        $rankedResults = collect();
        $previousScore = null;
        $previousRank = 0;
        $position = 1;

        foreach ($final_results as $result) {
            $currentScore = $result->total;

            if ($currentScore === $previousScore) {
                $rank = $previousRank;
            } else {
                $rank = $position;
                $previousScore = $currentScore;
                $previousRank = $rank;
            }

            $result->position = $rank;
            $rankedResults->push($result);
            $position++;
        }

        return $rankedResults;
    }
}
