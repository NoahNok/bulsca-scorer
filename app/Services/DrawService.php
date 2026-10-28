<?php

namespace App\Services;

use App\Models\AbstractClasses\Entity;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\Orders\Draw;
use App\Models\SERC;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DrawService
{

    public function generateDrawForSERC(SERC $serc)
    {
        DB::transaction(function () use ($serc) {
            $this->lockSercForDrawMutation($serc);
            $serc->draw()->delete();

            foreach ($serc->getScorableEntities()->shuffle() as $index => $entity) {
                $serc->draw()->create([
                    'entity_id' => $entity->id,
                    'entity_type' => $entity->getMorphClass(),
                    'tank' => 0,
                    'draw' => $index + 1,
                ]);
            }
        });
    }

    public function swapInDraw(Draw $drawA, Draw $drawB)
    {
        $temp_tank = $drawA->tank;
        $temp_draw = $drawA->draw;

        $drawA->tank = $drawB->tank;
        $drawA->draw = $drawB->draw;
        $drawA->save();

        $drawB->tank = $temp_tank;
        $drawB->draw = $temp_draw;
        $drawB->save();
    }

    public function moveDraw(Competition $comp, SERC $serc, array $data): void
    {
        DB::transaction(function () use ($comp, $serc, $data) {
            $this->lockSercForDrawMutation($serc);
            $draws = $serc->draw()->orderBy('tank')->orderBy('draw')->lockForUpdate()->get();
            $targetTank = isset($data['target_tank']) ? (int) $data['target_tank'] : null;
            $target = $targetTank === null
                ? $draws->firstWhere('id', (int) $data['target'])
                : null;
            abort_if($targetTank === null && !$target, 404);

            $sourceDraw = $data['source_type'] === 'draw'
                ? $draws->firstWhere('id', (int) $data['source'])
                : null;
            abort_if($data['source_type'] === 'draw' && !$sourceDraw, 404);
            $this->refuseIf($sourceDraw && $target && $sourceDraw->is($target), 'Choose a different draw position.');

            $entity = $sourceDraw
                ? $sourceDraw->entity
                : $serc->getScorableEntity()::where('competition', $comp->id)->findOrFail($data['source']);
            $this->refuseIf(!$entity, 'That draw has no entry.');
            $this->refuseIf(!$sourceDraw && $serc->draw()->whereMorphedTo('entity', $entity)->exists(), 'That entry is already in the draw.');

            if ($targetTank !== null) {
                $tankExists = $serc->drawTanks()->where('tank', $targetTank)->exists();
                $this->refuseIf(!($tankExists || (!$comp->getScoringSettings->use_tanks && $targetTank === 0)), 'Tank does not exist.');

                if (($data['placement'] ?? null) === 'end') {
                    $targetItems = $draws->where('tank', $targetTank)->values();

                    if ($sourceDraw) {
                        $sourceTank = (int) $sourceDraw->tank;
                        $targetItems = $targetItems
                            ->reject(fn($draw) => $draw->id === $sourceDraw->id)
                            ->push($sourceDraw)
                            ->values();

                        if ($sourceTank !== $targetTank) {
                            $draws->where('tank', $sourceTank)
                                ->reject(fn($draw) => $draw->id === $sourceDraw->id)
                                ->values()
                                ->each(function ($draw, $index) {
                                    $draw->draw = $index + 1;
                                    $draw->save();
                                });
                        }

                        $targetItems->each(function ($draw, $index) use ($targetTank) {
                            $draw->tank = $targetTank;
                            $draw->draw = $index + 1;
                            $draw->save();
                        });

                        return;
                    }

                    $serc->draw()->create([
                        'entity_id' => $entity->id,
                        'entity_type' => $entity->getMorphClass(),
                        'tank' => $targetTank,
                        'draw' => (int) $targetItems->max('draw') + 1,
                    ]);

                    return;
                }

                $this->refuseIf($draws->contains(fn($draw) => (int) $draw->tank === $targetTank), 'The tank is no longer empty.');

                if ($sourceDraw) {
                    $sourceTank = (int) $sourceDraw->tank;
                    $sourceDraw->tank = $targetTank;
                    $sourceDraw->draw = 1;
                    $sourceDraw->save();

                    if ($sourceTank !== $targetTank) {
                        $draws->where('tank', $sourceTank)->values()->each(function ($draw, $index) {
                            $draw->draw = $index + 1;
                            $draw->save();
                        });
                    }

                    return;
                }

                $serc->draw()->create([
                    'entity_id' => $entity->id,
                    'entity_type' => $entity->getMorphClass(),
                    'tank' => $targetTank,
                    'draw' => 1,
                ]);

                return;
            }

            $targetItems = $draws->where('tank', $target->tank)
                ->reject(fn($draw) => $draw->id === $sourceDraw?->id)
                ->values();
            $targetIndex = $targetItems->search(fn($draw) => $draw->id === $target->id);
            abort_if($targetIndex === false, 404);
            $targetItems->splice($targetIndex + ($data['placement'] === 'after' ? 1 : 0), 0, [$sourceDraw ?? $entity]);

            if ($sourceDraw && $sourceDraw->tank !== $target->tank) {
                $draws->where('tank', $sourceDraw->tank)
                    ->reject(fn($draw) => $draw->id === $sourceDraw->id)
                    ->values()
                    ->each(function ($draw, $index) {
                        $draw->draw = $index + 1;
                        $draw->save();
                    });
            }

            foreach ($targetItems as $index => $item) {
                if ($item instanceof Draw) {
                    $item->tank = $target->tank;
                    $item->draw = $index + 1;
                    $item->save();
                } else {
                    $serc->draw()->create([
                        'entity_id' => $item->id,
                        'entity_type' => $item->getMorphClass(),
                        'tank' => $target->tank,
                        'draw' => $index + 1,
                    ]);
                }
            }
        });
    }

    /**
     * The Draw tank by tank, each Draw in running order. Without tanks it's one Tank numbered 0, there
     * even before anything is drawn so Entities can be added to it.
     *
     * @return array<int, array{number: int, draws: array<int, array{id: int, entity: int, name: string, draw: int}>}>
     */
    public function tanks(Competition $comp, SERC $serc): array
    {
        $draws = $serc->draw()->with('entity')->orderBy('tank')->orderBy('draw')->orderBy('id')->get();
        $numbers = $serc->drawTanks()->pluck('tank')
            ->merge($draws->pluck('tank'))
            ->map(fn ($tank) => (int) $tank)
            ->unique()
            ->sort()
            ->values();

        if ($numbers->isEmpty() && !$comp->getScoringSettings->use_tanks) {
            $numbers = collect([0]);
        }

        return $numbers->map(fn (int $number) => [
            'number' => $number,
            'draws' => $draws->where('tank', $number)->map(fn (Draw $draw) => [
                'id' => $draw->id,
                'entity' => $draw->entity_id,
                'name' => $draw->entity?->getName($comp) ?? 'No Entity',
                'draw' => $draw->draw,
            ])->values()->all(),
        ])->all();
    }

    /**
     * The Entities the SERC scores that have no Draw
     *
     * @return array<int, array{id: int, name: string}>
     */
    public function unassigned(Competition $comp, SERC $serc): array
    {
        return $serc->getScorableEntity()::where('competition', $comp->id)
            ->whereNotIn('id', $serc->draw()->pluck('entity_id'))
            ->get()
            ->map(fn (Entity $entity) => ['id' => $entity->id, 'name' => $entity->getName($comp)])
            ->values()
            ->all();
    }

    /**
     * Adds an Entity to the end of a Tank (0 without tanks)
     */
    public function assign(Competition $comp, SERC $serc, Entity $entity, int $tank): void
    {
        DB::transaction(function () use ($comp, $serc, $entity, $tank) {
            $this->lockSercForDrawMutation($serc);
            $this->refuseIf(
                !$serc->drawTanks()->where('tank', $tank)->exists() && ($comp->getScoringSettings->use_tanks || $tank !== 0),
                'That tank does not exist.'
            );
            $this->refuseIf($serc->draw()->whereMorphedTo('entity', $entity)->exists(), "{$entity->getName($comp)} is already in the draw.");

            $serc->draw()->create([
                'entity_id' => $entity->id,
                'entity_type' => $entity->getMorphClass(),
                'tank' => $tank,
                'draw' => (int) $serc->draw()->where('tank', $tank)->max('draw') + 1,
            ]);
        });
    }

    /**
     * Takes a Draw out, leaving its position open until the Draw is compacted
     */
    public function remove(SERC $serc, int $id): Draw
    {
        return DB::transaction(function () use ($serc, $id) {
            $this->lockSercForDrawMutation($serc);
            $draw = $serc->draw()->with('entity')->lockForUpdate()->findOrFail($id);
            $draw->delete();

            return $draw;
        });
    }

    /**
     * Adds an empty Tank after the last one, returning its number
     */
    public function addTank(SERC $serc): int
    {
        return DB::transaction(function () use ($serc) {
            $this->lockSercForDrawMutation($serc);
            $tank = max((int) $serc->drawTanks()->max('tank') + 1, 1);
            $serc->drawTanks()->create(['tank' => $tank]);

            return $tank;
        });
    }

    /**
     * Removes a Tank, unassigning its Entities and numbering the later Tanks down by one. Returns how
     * many Entities it unassigned.
     */
    public function removeTank(SERC $serc, int $tank): int
    {
        return DB::transaction(function () use ($serc, $tank) {
            $this->lockSercForDrawMutation($serc);
            abort_unless($serc->drawTanks()->where('tank', $tank)->exists(), 404);

            $unassigned = $serc->draw()->where('tank', $tank)->count();
            $serc->draw()->where('tank', $tank)->delete();
            $serc->drawTanks()->where('tank', $tank)->delete();
            // Out of the way first, so no two Tanks share a number on the way down
            foreach ([$serc->drawTanks(), $serc->draw()] as $query) {
                (clone $query)->where('tank', '>', $tank)->update(['tank' => DB::raw('tank + 1000000')]);
            }
            foreach ([$serc->drawTanks(), $serc->draw()] as $query) {
                (clone $query)->where('tank', '>', 1000000)->update(['tank' => DB::raw('tank - 1000001')]);
            }

            return $unassigned;
        });
    }

    /**
     * Numbers the Tanks 1, 2, 3… and each Tank's Draws 1, 2, 3…, keeping their order. Without tanks only
     * the Draws are renumbered.
     */
    public function compact(Competition $comp, SERC $serc): void
    {
        DB::transaction(function () use ($comp, $serc) {
            $this->lockSercForDrawMutation($serc);

            if ($comp->getScoringSettings->use_tanks) {
                $numbers = $serc->drawTanks()->pluck('tank')
                    ->merge($serc->draw()->distinct()->pluck('tank'))
                    ->map(fn ($tank) => (int) $tank)
                    ->unique()
                    ->sort()
                    ->values();
                // Past every number in use, so renumbering never lands on a Tank still to move
                $offset = max(0, (int) $numbers->max()) - min(0, (int) $numbers->min()) + $numbers->count() + 10;

                $serc->drawTanks()->update(['tank' => DB::raw("tank + {$offset}")]);
                $serc->draw()->update(['tank' => DB::raw("tank + {$offset}")]);

                foreach ($numbers as $index => $old) {
                    $serc->drawTanks()->where('tank', $old + $offset)->update(['tank' => $index + 1]);
                    $serc->draw()->where('tank', $old + $offset)->update(['tank' => $index + 1]);
                }
            }

            $serc->draw()->orderBy('tank')->orderBy('draw')->orderBy('id')->lockForUpdate()->get()
                ->groupBy('tank')
                ->each(fn ($draws) => $draws->values()->each(fn (Draw $draw, int $index) => $draw->update(['draw' => $index + 1])));
        });
    }

    /**
     * Each League's Entities, as Tank setup assigns them (-1 for those without a League), and the Leagues
     * each Tank holds now
     *
     * @return array{leagues: array<int, array{id: int, name: string, count: int}>, tanks: array<int, array<int, int>>}
     */
    public function tankSetup(Competition $comp, SERC $serc): array
    {
        $leagueOf = fn (Entity $entity) => $entity->leagues->first();

        $leagues = $this->tankEntities($comp)
            ->groupBy(fn (Entity $entity) => $leagueOf($entity)?->id ?? -1)
            ->map(fn ($entities, $id) => ['id' => (int) $id, 'name' => $leagueOf($entities->first())?->name ?? 'No League', 'count' => $entities->count()])
            ->sortBy(fn ($league) => [$league['id'] === -1, $league['name']])
            ->values()
            ->all();

        $tanks = $serc->draw()->with('entity.leagues')->orderBy('tank')->get()
            ->filter(fn (Draw $draw) => $draw->entity)
            ->groupBy('tank')
            ->map(fn ($draws) => $draws->map(fn (Draw $draw) => $leagueOf($draw->entity)?->id ?? -1)->unique()->values()->all())
            ->values()
            ->all();

        return ['leagues' => $leagues, 'tanks' => $tanks];
    }

    /**
     * Redraws from Tank setup: a Tank per list of League ids, each League's Entities shuffled into it in
     * the order the Leagues are listed. Every League must be in exactly one Tank.
     *
     * @param array<int, array<int, int>> $tanks
     */
    public function setUpTanks(Competition $comp, SERC $serc, array $tanks): void
    {
        $byLeague = $this->tankEntities($comp)->groupBy(fn (Entity $entity) => $entity->leagues->first()?->id ?? -1);
        $expected = $byLeague->keys()->map(fn ($id) => (int) $id)->sort()->values()->all();
        $submitted = collect($tanks)->flatten()->map(fn ($id) => (int) $id)->sort()->values()->all();

        if ($submitted !== $expected) {
            throw ValidationException::withMessages(['tanks' => 'Put every League in exactly one tank.']);
        }

        DB::transaction(function () use ($serc, $tanks, $byLeague) {
            $this->lockSercForDrawMutation($serc);
            $serc->draw()->delete();
            $serc->drawTanks()->delete();

            foreach (array_values($tanks) as $index => $leagues) {
                $serc->drawTanks()->create(['tank' => $index + 1]);
                $draw = 0;

                foreach ($leagues as $league) {
                    foreach ($byLeague->get($league)->shuffle() as $entity) {
                        $serc->draw()->create([
                            'entity_id' => $entity->id,
                            'entity_type' => $entity->getMorphClass(),
                            'tank' => $index + 1,
                            'draw' => ++$draw,
                        ]);
                    }
                }
            }
        });
    }

    /**
     * The Entities Tank setup assigns by League: the competition's teams
     */
    private function tankEntities(Competition $comp)
    {
        return CompetitionTeam::where('competition', $comp->id)->with('leagues')->get();
    }

    private function refuseIf(bool $refused, string $message): void
    {
        if ($refused) {
            throw ValidationException::withMessages(['draw' => $message]);
        }
    }

    public function lockSercForDrawMutation(SERC $serc): void
    {
        SERC::whereKey($serc->id)->lockForUpdate()->firstOrFail();
    }
}
