<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\AbstractClasses\Entity;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\SERC;
use App\Services\DrawService;
use App\Traits\RecordActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DrawController extends Controller
{
    use RecordActivity;

    public function generate(Competition $comp, DrawService $drawService)
    {

        if ($comp->getScoringSettings->use_tanks) {
            return redirect()->route('comps.heats_and_draws.draws.tank_setup', $comp);
        }

        $serc = $comp->getSERCs()->orderBy('id')->first();

        $drawService->generateDrawForSERC($serc);

        $comp->clearDrawCache();

        $this->recordActivity('DRAW_GENERATED', related: $comp);

        return redirect()->back()->with('success', 'Draw Generated');
    }

    public function hide(Competition $comp)
    {

        $comp->show_draws = !$comp->show_draws;
        $comp->save();

        return redirect()->back();
    }

    public function edit(Competition $comp, SERC $serc)
    {
        $this->ensureSercBelongsToCompetition($comp, $serc);

        return view('competition.heats-and-orders.draws.edit', compact('comp', 'serc'));
    }

    public function swap(Competition $comp, SERC $serc, Request $request, DrawService $drawService)
    {
        $this->ensureSercBelongsToCompetition($comp, $serc);
        $data = $request->validate([
            'swap_from' => ['required', 'integer'],
            'swap_to' => ['required', 'integer', 'different:swap_from'],
        ]);
        [$swap_from, $swap_to, $activityDescription] = DB::transaction(function () use ($serc, $data, $comp, $drawService) {
            $drawService->lockSercForDrawMutation($serc);
            $swap_from = $serc->draw()->with('entity')->lockForUpdate()->findOrFail($data['swap_from']);
            $swap_to = $serc->draw()->with('entity')->lockForUpdate()->findOrFail($data['swap_to']);
            $activityDescription = "Swapped draw positions: {$swap_from->entity?->getName($comp)} (Tank {$swap_from->tank}, Draw {$swap_from->draw}) <-> {$swap_to->entity?->getName($comp)} (Tank {$swap_to->tank}, Draw {$swap_to->draw})";
            $drawService->swapInDraw($swap_from, $swap_to);

            return [$swap_from, $swap_to, $activityDescription];
        });
        $comp->clearDrawCache();
        $this->recordActivity('DRAW_SWAP', $activityDescription, context: ['swap_from' => $swap_from->id, 'swap_to' => $swap_to->id], related: [$comp, $swap_from->entity, $swap_to->entity]);

        return $this->drawEditorResponse($comp, $serc);
    }

    public function remove(Competition $comp, SERC $serc, Request $request, DrawService $drawService)
    {
        $this->ensureSercBelongsToCompetition($comp, $serc);
        $drawId = $request->validate(['draw' => ['required', 'integer']])['draw'];
        DB::transaction(function () use ($serc, $drawId, $drawService) {
            $drawService->lockSercForDrawMutation($serc);
            $serc->draw()->whereKey($drawId)->delete();
        });
        $comp->clearDrawCache();

        return $this->drawEditorResponse($comp, $serc);
    }

    public function move(Competition $comp, SERC $serc, Request $request, DrawService $drawService)
    {
        $this->ensureSercBelongsToCompetition($comp, $serc);
        $data = $request->validate([
            'source_type' => ['required', 'in:draw,entity'],
            'source' => ['required', 'integer'],
            'target' => ['nullable', 'required_without:target_tank', 'integer'],
            'target_tank' => ['nullable', 'required_without:target', 'integer'],
            'placement' => ['nullable', 'required_with:target', 'in:before,after,end'],
        ]);
        $drawService->moveDraw($comp, $serc, $data);

        $comp->clearDrawCache();

        return $this->drawEditorResponse($comp, $serc);
    }

    public function assign(Competition $comp, SERC $serc, Request $request, DrawService $drawService)
    {
        $this->ensureSercBelongsToCompetition($comp, $serc);
        $data = $request->validate([
            'entity' => ['required', 'integer'],
            'tank' => ['required', 'integer'],
        ]);
        DB::transaction(function () use ($comp, $serc, $data, $drawService) {
            $drawService->lockSercForDrawMutation($serc);
            $entity = $serc->getScorableEntity()::where('competition', $comp->id)->findOrFail($data['entity']);
            $tankExists = $serc->drawTanks()->where('tank', $data['tank'])->exists();
            abort_unless($tankExists || (!$comp->getScoringSettings->use_tanks && (int) $data['tank'] === 0), 422, 'Tank does not exist.');
            abort_if($serc->draw()->whereMorphedTo('entity', $entity)->exists(), 422, 'Entity is already assigned.');

            $serc->draw()->create([
                'entity_id' => $entity->id,
                'entity_type' => $entity->getMorphClass(),
                'tank' => $data['tank'],
                'draw' => (int) $serc->draw()->where('tank', $data['tank'])->max('draw') + 1,
            ]);
        });
        $comp->clearDrawCache();

        return $this->drawEditorResponse($comp, $serc);
    }

    public function addTank(Competition $comp, SERC $serc, DrawService $drawService)
    {
        $this->ensureSercBelongsToCompetition($comp, $serc);
        abort_unless($comp->getScoringSettings->use_tanks, 422, 'Tanks are not enabled for this competition.');
        DB::transaction(function () use ($serc, $drawService) {
            $drawService->lockSercForDrawMutation($serc);
            $tank = max((int) $serc->drawTanks()->max('tank') + 1, 1);
            $serc->drawTanks()->create(['tank' => $tank]);
        });
        $comp->clearDrawCache();

        return $this->drawEditorResponse($comp, $serc);
    }

    public function compactTanks(Competition $comp, SERC $serc, DrawService $drawService)
    {
        $this->ensureSercBelongsToCompetition($comp, $serc);
        $useTanks = (bool) $comp->getScoringSettings->use_tanks;

        DB::transaction(function () use ($serc, $useTanks, $drawService) {
            $drawService->lockSercForDrawMutation($serc);
            if (!$useTanks) {
                $serc->draw()->orderBy('tank')->orderBy('draw')->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->groupBy('tank')
                    ->each(function ($draws) {
                        $draws->values()->each(function ($draw, $index) {
                            $draw->draw = $index + 1;
                            $draw->save();
                        });
                    });

                return;
            }

            $tankNumbers = $serc->drawTanks()->pluck('tank')
                ->merge($serc->draw()->distinct()->pluck('tank'))
                ->map(fn($tank) => (int) $tank)
                ->unique()
                ->sort()
                ->values();

            if ($tankNumbers->isEmpty()) {
                return;
            }

            $minimum = min(0, (int) $tankNumbers->min());
            $maximum = max(0, (int) $tankNumbers->max());
            $offset = $maximum - $minimum + $tankNumbers->count() + 10;

            $serc->drawTanks()->update(['tank' => DB::raw("tank + {$offset}")]);
            $serc->draw()->update(['tank' => DB::raw("tank + {$offset}")]);

            foreach ($tankNumbers as $index => $oldTankNumber) {
                $temporaryTankNumber = $oldTankNumber + $offset;
                $newTankNumber = $index + 1;

                $serc->drawTanks()->where('tank', $temporaryTankNumber)->update(['tank' => $newTankNumber]);
                $serc->draw()->where('tank', $temporaryTankNumber)->update(['tank' => $newTankNumber]);

                $serc->draw()->where('tank', $newTankNumber)
                    ->orderBy('draw')
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->each(function ($draw, $drawIndex) {
                        $draw->draw = $drawIndex + 1;
                        $draw->save();
                    });
            }
        });

        $comp->clearDrawCache();
        $this->recordActivity('DRAW_TANKS_COMPACTED', related: $comp);

        return $this->drawEditorResponse($comp, $serc);
    }

    public function removeTank(Competition $comp, SERC $serc, Request $request, DrawService $drawService)
    {
        $this->ensureSercBelongsToCompetition($comp, $serc);
        abort_unless($comp->getScoringSettings->use_tanks, 422, 'Tanks are not enabled for this competition.');
        $tank = $request->validate(['tank' => ['required', 'integer', 'min:1']])['tank'];
        abort_unless($serc->drawTanks()->where('tank', $tank)->exists(), 404);

        DB::transaction(function () use ($serc, $tank, $drawService) {
            $drawService->lockSercForDrawMutation($serc);
            abort_unless($serc->drawTanks()->where('tank', $tank)->exists(), 404);
            $serc->draw()->where('tank', $tank)->delete();
            $serc->drawTanks()->where('tank', $tank)->delete();
            $serc->drawTanks()->where('tank', '>', $tank)->update(['tank' => DB::raw('tank + 1000000')]);
            $serc->draw()->where('tank', '>', $tank)->update(['tank' => DB::raw('tank + 1000000')]);
            $serc->drawTanks()->where('tank', '>', 1000000)->update(['tank' => DB::raw('tank - 1000001')]);
            $serc->draw()->where('tank', '>', 1000000)->update(['tank' => DB::raw('tank - 1000001')]);
        });
        $comp->clearDrawCache();

        return $this->drawEditorResponse($comp, $serc);
    }

    private function drawEditorResponse(Competition $comp, SERC $serc)
    {
        $assignedIds = $serc->draw()->pluck('entity_id')->unique();
        $unassigned = $serc->getScorableEntity()::where('competition', $comp->id)
            ->whereNotIn('id', $assignedIds)
            ->get()
            ->map(fn(Entity $entity) => [
                'id' => $entity->id,
                'name' => $entity->getName($comp),
            ])->values();

        return response()->json(['tanks' => $serc->getTankDraw(), 'unassigned' => $unassigned]);
    }

    private function ensureSercBelongsToCompetition(Competition $comp, SERC $serc): void
    {
        abort_unless((int) $serc->competition === (int) $comp->id, 404);
    }

    public function reset(Competition $comp, SERC $serc, DrawService $drawService)
    {
        $this->ensureSercBelongsToCompetition($comp, $serc);

        if ($comp->getScoringSettings->use_tanks) {
            return redirect()->route('comps.heats_and_draws.draws.tank_setup', $comp);
        }

        $drawService->generateDrawForSERC($serc);

        $comp->clearDrawCache();

        $this->recordActivity('DRAW_RESET', related: $comp);

        return redirect()->back()->with('success', 'Draw Reset');
    }

    public function tankSetup(Competition $comp)
    {
        return view('competition.heats-and-orders.draws.draw', ['comp' => $comp]);
    }

    public function tankSetupPost(Competition $comp, Request $request, DrawService $drawService)
    {
        abort_unless($comp->getScoringSettings->use_tanks, 422, 'Tanks are not enabled for this competition.');

        $data = $request->validate([
            'tanks' => ['present', 'array'],
            'tanks.*' => ['array'],
            'tanks.*.*.league' => ['required', 'integer'],
        ]);

        $allCompetitorsPerLeague = CompetitionTeam::where('competition', $comp->id)
            ->with('leagues')
            ->get()
            ->groupBy(fn(CompetitionTeam $entity) => $entity->getLeague()?->id ?? -1);
        $expectedLeagueIds = $allCompetitorsPerLeague->keys()
            ->map(fn($leagueId) => (int) $leagueId)
            ->sort()
            ->values();
        $submittedLeagueIds = collect($data['tanks'])
            ->flatMap(fn($tank) => collect($tank)->pluck('league'))
            ->map(fn($leagueId) => (int) $leagueId)
            ->sort()
            ->values();

        abort_unless(
            $submittedLeagueIds->count() === $expectedLeagueIds->count()
                && $submittedLeagueIds->unique()->count() === $expectedLeagueIds->count()
                && $submittedLeagueIds->all() === $expectedLeagueIds->all(),
            422,
            'Each competition bracket must be assigned to exactly one tank.'
        );

        $targetSerc = SERC::where('competition', $comp->id)->orderBy('id')->firstOrFail();

        DB::transaction(function () use ($targetSerc, $data, $allCompetitorsPerLeague, $drawService) {
            $drawService->lockSercForDrawMutation($targetSerc);
            $targetSerc->draw()->delete();
            $targetSerc->drawTanks()->delete();

            foreach ($data['tanks'] as $tankIndex => $tank) {
                $tankNumber = $tankIndex + 1;
                $targetSerc->drawTanks()->create(['tank' => $tankNumber]);
                $drawNumber = 0;

                foreach ($tank as $bracket) {
                    $leagueId = (int) $bracket['league'];
                    $competitors = $allCompetitorsPerLeague->get($leagueId)->shuffle();

                    foreach ($competitors as $competitor) {
                        $drawNumber++;
                        $targetSerc->draw()->create([
                            'entity_id' => $competitor->id,
                            'entity_type' => $competitor->getMorphClass(),
                            'tank' => $tankNumber,
                            'draw' => $drawNumber,
                        ]);
                    }
                }
            }
        });

        $comp->clearDrawCache();

        $this->recordActivity('DRAW_TANK_SETUP', related: $comp);

        return response()->json();
    }
}
