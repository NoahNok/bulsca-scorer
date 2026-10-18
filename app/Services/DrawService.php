<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\Orders\Draw;
use App\Models\SERC;
use Illuminate\Support\Facades\DB;

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
            abort_if($sourceDraw && $target && $sourceDraw->is($target), 422, 'Choose a different draw position.');

            $entity = $sourceDraw
                ? $sourceDraw->entity
                : $serc->getScorableEntity()::where('competition', $comp->id)->findOrFail($data['source']);
            abort_if(!$entity, 422, 'The selected draw has no entity.');
            abort_if(
                !$sourceDraw && $serc->draw()->whereMorphedTo('entity', $entity)->exists(),
                422,
                'Entity is already assigned.'
            );

            if ($targetTank !== null) {
                $tankExists = $serc->drawTanks()->where('tank', $targetTank)->exists();
                abort_unless(
                    $tankExists || (!$comp->getScoringSettings->use_tanks && $targetTank === 0),
                    422,
                    'Tank does not exist.'
                );

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

                abort_if(
                    $draws->contains(fn($draw) => (int) $draw->tank === $targetTank),
                    422,
                    'The tank is no longer empty.'
                );

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

    public function lockSercForDrawMutation(SERC $serc): void
    {
        SERC::whereKey($serc->id)->lockForUpdate()->firstOrFail();
    }
}
