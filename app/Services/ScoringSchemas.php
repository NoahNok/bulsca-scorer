<?php

namespace App\Services;

use App\Models\Organisation\Organisation;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Scoring schemas an event's scoring can be cloned from: every one held by the user's organisations,
 * and by the competition's own organisation when given.
 */
class ScoringSchemas
{
    /**
     * @return Collection<int, array{id: int, name: string, schema: array}>
     */
    public static function availableFor(User $user, ?Organisation $org = null): Collection
    {
        $orgs = $user->getOrganisations;

        if ($org) {
            $orgs = $orgs->push($org)->unique('id');
        }

        return $orgs->flatMap(fn (Organisation $org) => $org->scoringSchemas->map(fn ($schema) => [
            'id' => $schema->id,
            'name' => $schema->name,
            'organisation' => $org->name,
            'schema' => $schema->schema,
        ]))->values();
    }

    /**
     * A schema with every setting the editor shows, filling in ones saved before they existed
     */
    public static function withDefaults(?array $schema): array
    {
        return array_merge([
            'equation' => '',
            'penalty_func' => '',
            'global_variables' => [],
            'local_variables' => [],
            'auto_penalties' => [],
            'auto_disqualifications' => [],
            'rank_higher' => true,
            'rank_equation' => '',
            'allow_disqualified_to_rank' => false,
            'group_by' => [],
            'force_penalty' => false,
        ], array_filter($schema ?? [], fn ($value) => $value !== null));
    }
}
