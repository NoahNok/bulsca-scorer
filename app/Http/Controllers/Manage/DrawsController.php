<?php

namespace App\Http\Controllers\Manage;

use App\Helpers\CompetitionHeader;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Manage\Concerns\RecordsOrderEdits;
use App\Models\Competition;
use App\Models\SERC;
use App\Services\DrawService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

/**
 * The draw editor and Tank setup (Svelte, full width), and every edit to the Draw. A competition has one
 * set of Draws, held by its first SERC (Competition::drawSerc) and shared by them all. Each edit saves at
 * once, goes back and is recorded as Activity.
 */
class DrawsController extends Controller
{
    use RecordsOrderEdits;

    public function __construct(private DrawService $draws) {}

    public function edit(Competition $comp)
    {
        $serc = $this->serc($comp);

        return Inertia::render('Manage/Competition/DrawEditor', [
            'competition' => CompetitionHeader::for($comp),
            'useTanks' => $comp->usesTanks(),
            'tanks' => $this->draws->tanks($comp, $serc),
            'unassigned' => $this->draws->unassigned($comp, $serc),
        ]);
    }

    /**
     * Draws every Entity at random, or with tanks, opens Tank setup to draw them by League. Only while
     * there's no Draw: drawing again is Regenerate, which confirms first.
     */
    public function generate(Competition $comp)
    {
        if (!$comp->usesTanks() && $this->serc($comp)->draw()->exists()) {
            throw ValidationException::withMessages(['draw' => 'There\'s a draw already. Regenerate it from the draw editor instead.']);
        }

        return $this->drawAtRandom($comp, 'DRAW_GENERATED', 'Draw generated');
    }

    public function regenerate(Competition $comp)
    {
        return $this->drawAtRandom($comp, 'DRAW_RESET', 'Draw regenerated');
    }

    public function swap(Competition $comp, Request $request)
    {
        $serc = $this->serc($comp);
        $data = $request->validate([
            'from' => ['required', 'integer'],
            'to' => ['required', 'integer', 'different:from'],
        ]);

        [$from, $to, $description] = DB::transaction(function () use ($comp, $serc, $data) {
            $this->draws->lockSercForDrawMutation($serc);
            $from = $serc->draw()->with('entity')->lockForUpdate()->findOrFail($data['from']);
            $to = $serc->draw()->with('entity')->lockForUpdate()->findOrFail($data['to']);
            $description = "Swapped draw positions: {$from->entity?->getName($comp)} (Tank {$from->tank}, Draw {$from->draw}) <-> {$to->entity?->getName($comp)} (Tank {$to->tank}, Draw {$to->draw})";
            $this->draws->swapInDraw($from, $to);

            return [$from, $to, $description];
        });
        $this->changed($comp, 'DRAW_SWAP', $description, ['swap_from' => $from->id, 'swap_to' => $to->id], [$from->entity, $to->entity]);

        return back()->with('success', 'Draws swapped');
    }

    public function move(Competition $comp, Request $request)
    {
        $serc = $this->serc($comp);
        $data = $request->validate([
            'source_type' => ['required', 'in:draw,entity'],
            'source' => ['required', 'integer'],
            'target' => ['nullable', 'required_without:target_tank', 'integer'],
            'target_tank' => ['nullable', 'required_without:target', 'integer'],
            'placement' => ['nullable', 'required_with:target', 'in:before,after,end'],
        ]);

        $entity = $data['source_type'] === 'draw'
            ? $serc->draw()->with('entity')->findOrFail($data['source'])->entity
            : $serc->findScorableEntity($data['source']);
        $target = isset($data['target']) ? $serc->draw()->with('entity')->findOrFail($data['target']) : null;

        $this->draws->moveDraw($comp, $serc, $data);

        $destination = $target
            ? "{$data['placement']} {$target->entity?->getName($comp)} in Tank {$target->tank}"
            : (($data['placement'] ?? null) === 'end' ? "the end of Tank {$data['target_tank']}" : "Tank {$data['target_tank']}");
        $this->changed(
            $comp,
            $data['source_type'] === 'entity' ? 'DRAW_ENTRY_ADDED' : 'DRAW_ENTRY_MOVED',
            "Moved {$entity?->getName($comp)} to {$destination}",
            [
                'source_type' => $data['source_type'],
                'source' => (int) $data['source'],
                'target' => isset($data['target']) ? (int) $data['target'] : null,
                'target_tank' => isset($data['target_tank']) ? (int) $data['target_tank'] : null,
                'placement' => $data['placement'] ?? null,
            ],
            [$entity],
        );

        return back()->with('success', $data['source_type'] === 'entity' ? 'Entry added' : 'Entry moved');
    }

    public function assign(Competition $comp, Request $request)
    {
        $serc = $this->serc($comp);
        $data = $request->validate([
            'entity' => ['required', 'integer'],
            'tank' => ['required', 'integer'],
        ]);
        $entity = $serc->findScorableEntity($data['entity']);

        $this->draws->assign($comp, $serc, $entity, $data['tank']);
        $this->changed($comp, 'DRAW_ENTRY_ADDED', "Added {$entity->getName($comp)} to Tank {$data['tank']}", ['entity_id' => $entity->id, 'tank' => (int) $data['tank']], [$entity]);

        return back()->with('success', 'Entry added');
    }

    public function remove(Competition $comp, Request $request)
    {
        $id = $request->validate(['draw' => ['required', 'integer']])['draw'];

        $draw = $this->draws->remove($this->serc($comp), $id);
        $this->changed(
            $comp,
            'DRAW_ENTRY_REMOVED',
            "Removed {$draw->entity?->getName($comp)} from Tank {$draw->tank}, Draw {$draw->draw}",
            ['draw_id' => $draw->id, 'entity_id' => $draw->entity_id, 'tank' => $draw->tank, 'draw' => $draw->draw],
            [$draw->entity],
        );

        return back()->with('success', 'Entry removed');
    }

    public function addTank(Competition $comp)
    {
        $this->ensureTanks($comp);

        $tank = $this->draws->addTank($this->serc($comp));
        $this->changed($comp, 'DRAW_TANK_ADDED', "Added Tank {$tank}", ['tank' => $tank]);

        return back()->with('success', "Tank {$tank} added");
    }

    public function removeTank(Competition $comp, Request $request)
    {
        $this->ensureTanks($comp);
        $tank = (int) $request->validate(['tank' => ['required', 'integer', 'min:1']])['tank'];

        $unassigned = $this->draws->removeTank($this->serc($comp), $tank);
        $this->changed(
            $comp,
            'DRAW_TANK_REMOVED',
            "Removed Tank {$tank} {$this->unassignedCount($unassigned)}",
            ['tank' => $tank, 'entries_unassigned' => $unassigned],
        );

        return back()->with('success', "Tank {$tank} removed");
    }

    public function compactTanks(Competition $comp)
    {
        $this->draws->compact($comp, $this->serc($comp));
        $this->changed($comp, 'DRAW_TANKS_COMPACTED');

        return back()->with('success', 'Draw compacted');
    }

    public function tankSetup(Competition $comp)
    {
        abort_unless($comp->usesTanks(), 404);

        return Inertia::render('Manage/Competition/TankSetup', [
            'competition' => CompetitionHeader::for($comp),
            ...$this->draws->tankSetup($comp, $this->serc($comp)),
        ]);
    }

    public function saveTankSetup(Competition $comp, Request $request)
    {
        $this->ensureTanks($comp);
        $tanks = $request->validate([
            'tanks' => ['present', 'array'],
            'tanks.*' => ['array'],
            'tanks.*.*' => ['integer'],
        ])['tanks'];

        $this->draws->setUpTanks($comp, $this->serc($comp), $tanks);
        $this->changed($comp, 'DRAW_TANK_SETUP');

        return redirect()->route('manage.competitions.heats_and_draws.draws.edit', $comp)->with('success', 'Draw made from the tanks');
    }

    protected function clearCache(Competition $comp): void
    {
        $comp->clearDrawCache();
    }

    /**
     * Draws every Entity at random, replacing the Draw; with tanks, Tank setup draws them by League instead
     */
    private function drawAtRandom(Competition $comp, string $activity, string $message)
    {
        if ($comp->usesTanks()) {
            return redirect()->route('manage.competitions.heats_and_draws.draws.tank_setup', $comp);
        }

        $this->draws->generateDrawForSERC($this->serc($comp));
        $this->changed($comp, $activity);

        return back()->with('success', $message);
    }

    /**
     * The SERC holding the Draw; without a SERC there's no Draw to edit
     */
    private function serc(Competition $comp): SERC
    {
        return $comp->drawSerc() ?? abort(404);
    }

    private function ensureTanks(Competition $comp): void
    {
        if (!$comp->usesTanks()) {
            throw ValidationException::withMessages(['draw' => 'Tanks are not turned on for this competition.']);
        }
    }
}
