<?php

namespace App\Services;

use App\Http\Requests\Event\UpdateScoringSettings;
use App\Models\AbstractClasses\Event;
use App\Models\Event\ScoringSchema;
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
        // concat makes a new collection; push would add the org to the user's loaded relation too
        $orgs = $user->getOrganisations->concat($org ? [$org] : [])->unique('id');

        return $orgs->flatMap(fn (Organisation $org) => $org->scoringSchemas->map(fn ($schema) => [
            'id' => $schema->id,
            'name' => $schema->name,
            'organisation' => $org->name,
            'schema' => $schema->schema,
        ]))->values();
    }

    /**
     * What an event's Scoring part edits: its schema (null until it has one), and the schemas it can
     * start from
     */
    public static function editorFor(Event $event, User $user, ?Organisation $org = null): array
    {
        $schema = $event->scoringSchema;

        return [
            'schema' => $schema ? self::withDefaults($schema->schema) : null,
            'options' => self::availableFor($user, $org)
                ->map(fn ($option) => [...$option, 'schema' => self::withDefaults($option['schema'])]),
        ];
    }

    /**
     * Saves an event's scoring, giving it a schema of its own first if it has none
     */
    public static function save(Event $event, UpdateScoringSettings $request): void
    {
        $schema = $event->scoringSchema;

        if (!$schema) {
            $schema = new ScoringSchema();
            $schema->name = "Scoring Schema for {$event->getType()}:{$event->id}";
            $schema->schema = [];
            $schema->save();
            $event->scoring_schema = $schema->id;
            $event->save();
        }

        $schema->editFromRequest($request);
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
