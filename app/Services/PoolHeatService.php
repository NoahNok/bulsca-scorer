<?php

namespace App\Services;

use App\Exceptions\HeatException;
use App\Models\AbstractClasses\Entity;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\Orders\Heat;
use App\Models\Pool;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Places every entity in each pool's leagues into that pool's heats, using the pool setup
 * saved on the competition. Heat numbers restart per pool and leagues never share a heat.
 */
class PoolHeatService
{
    const STYLE_STANDARD = 'standard';
    const STYLE_PAIRS = 'pairs';

    /**
     * Replace the competition's heats with freshly generated pool heats.
     *
     * @throws HeatException when the pool setup can't be turned into heats
     */
    public function generate(Competition $comp, string $style): void
    {
        $rows = $this->plan($comp, $style);
        $event = $this->heatsEvent($comp);

        DB::transaction(function () use ($event, $rows) {
            // Delete one by one so each heat's DQ submissions go with it
            $event->heats()->get()->each->delete();
            Heat::insert($rows);
        });
    }

    /**
     * Work out the heat rows without saving anything.
     *
     * @throws HeatException
     */
    public function plan(Competition $comp, string $style): array
    {
        if ($comp->heats_per_event) {
            throw new HeatException('Pool heats need heats per event to be off.');
        }

        $event = $this->heatsEvent($comp);
        $pools = $comp->getPools()->with('leagues')->get();

        if ($pools->isEmpty()) {
            throw new HeatException('The competition has no pools.');
        }

        if ($style == self::STYLE_PAIRS && $event->scorable_entity != 'competitor') {
            throw new HeatException('Pair heats only work when the speed events are swum by competitors.');
        }

        $entities = $event->getScorableEntities()->load('leagues');
        $this->ensureNoLeagueClashes($comp, $pools, $entities);

        $seeds = $this->seedsFor($comp, $event);
        $now = now();
        $rows = [];

        foreach ($pools as $pool) {
            $heatNumber = 1;

            foreach ($pool->leagues as $league) {
                $members = $entities->filter(fn(Entity $entity) => $entity->leagues->contains('id', $league->id))->values();

                if ($members->isEmpty()) continue;

                $heats = $style == self::STYLE_PAIRS && $league->pivot->is_pair
                    ? $this->pairHeats($comp, $league->name, $members, $seeds, $pool->lanes)
                    : $this->standardHeats($comp, $members, $seeds, $pool->lanes);

                foreach ($heats as $lanes) {
                    foreach ($lanes as $lane => $entity) {
                        $rows[] = [
                            'speed_event' => $event->id,
                            'pool' => $pool->id,
                            'entity_id' => $entity->id,
                            'entity_type' => $entity->getMorphClass(),
                            'heat' => $heatNumber,
                            'lane' => $lane,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                    $heatNumber++;
                }
            }
        }

        return $rows;
    }

    /**
     * Entities that aren't in any league swum in a pool, so won't get a heat.
     */
    public function unplacedEntities(Competition $comp): Collection
    {
        $leagueIds = $comp->getPools()->with('leagues')->get()->flatMap(fn(Pool $pool) => $pool->leagues->pluck('id'));

        return $this->heatsEvent($comp)->getScorableEntities()->load('leagues')
            ->filter(fn(Entity $entity) => $entity->leagues->pluck('id')->intersect($leagueIds)->isEmpty())
            ->values();
    }

    /**
     * What regenerating would throw away: [heats, oofs, dq_submissions].
     */
    public function deletionImpact(Competition $comp): array
    {
        $heats = $this->heatsEvent($comp)->heats();

        return [
            'heats' => $heats->count(),
            'oofs' => DB::table('event_oofs')->whereIn('heat_lane', $heats->select('id'))->count(),
            'dq_submissions' => DB::table('judge_dq_submissions')->whereIn('heat_lane', $heats->select('id'))->count(),
        ];
    }

    /**
     * With heats per event off every event reads its heats from the first speed event.
     */
    private function heatsEvent(Competition $comp): CompetitionSpeedEvent
    {
        $event = $comp->getSpeedEvents()->orderBy('id')->first();

        if (!$event) {
            throw new HeatException('The competition has no speed events.');
        }

        return $event;
    }

    private function ensureNoLeagueClashes(Competition $comp, Collection $pools, Collection $entities): void
    {
        $placements = $pools->flatMap(fn(Pool $pool) => $pool->leagues->map(fn($league) => [$pool, $league]));

        $clashes = $entities->map(function (Entity $entity) use ($placements, $comp) {
            $found = $placements->filter(fn($placement) => $entity->leagues->contains('id', $placement[1]->id));

            if ($found->count() < 2) return null;

            $where = $found->map(fn($placement) => "{$placement[1]->name} ({$placement[0]->name})")->join(', ');
            return "{$entity->getName($comp)}: {$where}";
        })->filter();

        if ($clashes->isNotEmpty()) {
            throw new HeatException("These entities are in more than one pooled league:\n" . $clashes->join("\n"));
        }
    }

    /**
     * Seed times keyed by "type:id", or null when the competition doesn't use seeds.
     */
    private function seedsFor(Competition $comp, CompetitionSpeedEvent $event): ?Collection
    {
        if (!$comp->use_seeds) {
            return null;
        }

        return $event->seeds()->get()->mapWithKeys(fn($seed) => ["{$seed->entity_type}:{$seed->entity_id}" => $seed->seed]);
    }

    private function seedOf(Competition $comp, Entity $entity, ?Collection $seeds): int
    {
        if ($seeds === null) {
            return $entity->id;
        }

        $seed = $seeds->get("{$entity->getMorphClass()}:{$entity->id}");

        if ($seed === null) {
            throw new HeatException("{$entity->getName($comp)} has no seed time.");
        }

        return $seed;
    }

    /**
     * @return array<int, array<int, Entity>> heats in running order, each as [lane => entity]
     */
    private function standardHeats(Competition $comp, Collection $members, ?Collection $seeds, int $lanes): array
    {
        $fastestFirst = $members->sortBy(fn(Entity $entity) => $this->seedOf($comp, $entity, $seeds))->values();

        return $fastestFirst->chunk($lanes)
            ->reverse()
            ->map(fn(Collection $heat) => $this->laneMap($heat->values()->all(), $lanes))
            ->values()
            ->all();
    }

    /**
     * Partners swim back to back in the same lane: the first of each pair, then the second.
     *
     * @return array<int, array<int, Entity>>
     */
    private function pairHeats(Competition $comp, string $leagueName, Collection $members, ?Collection $seeds, int $lanes): array
    {
        $pairs = $members->groupBy('team')->map(function (Collection $pair) use ($comp, $leagueName) {
            if ($pair->count() != 2) {
                $team = $pair->first()->getTeam;
                throw new HeatException("{$team?->getName($comp)} has {$pair->count()} competitors in {$leagueName}, pairs need exactly 2.");
            }

            return $pair->sortBy('id')->values();
        });

        $fastestFirst = $pairs->sortBy(fn(Collection $pair) => $pair->map(fn($competitor) => $this->seedOf($comp, $competitor, $seeds))->min())->values();

        $heats = [];
        foreach ($fastestFirst->chunk($lanes)->reverse() as $chunk) {
            $laneMap = $this->laneMap($chunk->values()->all(), $lanes);
            $heats[] = array_map(fn(Collection $pair) => $pair[0], $laneMap);
            $heats[] = array_map(fn(Collection $pair) => $pair[1], $laneMap);
        }

        return $heats;
    }

    /**
     * Fastest in the middle lane, then alternating outwards: for 8 lanes 4, 5, 3, 6, 2, 7, 1, 8.
     */
    private function laneMap(array $fastestFirst, int $lanes): array
    {
        $middle = (int) ceil($lanes / 2);
        $offset = 0;
        $mapped = [];

        foreach ($fastestFirst as $entry) {
            $mapped[$middle + $offset] = $entry;
            $offset = $offset > 0 ? -$offset : -$offset + 1;
        }

        ksort($mapped);

        return $mapped;
    }
}
