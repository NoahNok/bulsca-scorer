<?php

namespace App\Console\Commands;

use App\Exceptions\HeatException;
use App\Models\Competition;
use App\Models\Pool;
use App\Services\PoolHeatService;
use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\note;
use function Laravel\Prompts\select;
use function Laravel\Prompts\table;
use function Laravel\Prompts\text;
use function Laravel\Prompts\warning;

class PoolHeats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'heats-and-draws:pool-heats {competition_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Generate heats across a competition's pools from the leagues swum in each pool";

    /**
     * Execute the console command.
     */
    public function handle(PoolHeatService $service)
    {
        $comp = Competition::find($this->argument('competition_id'));

        if (!$comp) {
            error('No competition found with that id');
            return self::FAILURE;
        }

        info("Using: {$comp->name}");

        if ($comp->heats_per_event) {
            error('Pool heats need heats per event to be off.');
            return self::FAILURE;
        }

        $event = $comp->getSpeedEvents()->orderBy('id')->first();

        if (!$event) {
            error('The competition has no speed events.');
            return self::FAILURE;
        }

        if (!$comp->use_pools && !confirm('Pools are turned off for this competition. Turn them on?')) {
            error('aborting');
            return self::FAILURE;
        }

        $this->showPools($comp);

        if (!$this->hasUsableSetup($comp) || !confirm('Use the saved pool setup?')) {
            $this->setupPools($comp);
        }

        $style = PoolHeatService::STYLE_STANDARD;

        if ($event->scorable_entity == 'competitor') {
            $style = select('Heat style', [
                PoolHeatService::STYLE_STANDARD => 'Standard: fastest heat last, fastest in the middle lane',
                PoolHeatService::STYLE_PAIRS => 'Pairs: partners swim back to back in the same lane',
            ]);
        }

        try {
            // Check the setup before anything is deleted
            $service->plan($comp, $style);
        } catch (HeatException $e) {
            error($e->getMessage());
            return self::FAILURE;
        }

        if (!$comp->use_seeds) {
            warning("The competition doesn't use seed times, so entities are placed in the order they were added.");
        }

        $unplaced = $service->unplacedEntities($comp);

        if ($unplaced->isNotEmpty()) {
            warning("These entities aren't in any pooled league and won't get a heat:");
            note($unplaced->map(fn($entity) => " - {$entity->getName($comp)}")->join("\n"));

            if (!confirm('Continue anyway?', default: false)) {
                error('aborting');
                return self::FAILURE;
            }
        }

        $impact = $service->deletionImpact($comp);

        if ($impact['heats'] > 0 && !confirm("This will delete {$impact['heats']} existing heat entries, {$impact['oofs']} OOFs and {$impact['dq_submissions']} DQ submissions. Continue?", default: false)) {
            error('aborting');
            return self::FAILURE;
        }

        // Only switch pools on once nothing else can abort, so a cancelled run leaves the competition as it was
        if (!$comp->use_pools) {
            $comp->use_pools = true;
            $comp->save();
        }

        $service->generate($comp, $style);

        foreach ($comp->getPools()->withCount('heats')->get() as $pool) {
            $heats = $pool->heats()->max('heat') ?? 0;
            info("{$pool->name}: {$heats} heats, {$pool->heats_count} entries");
        }

        return self::SUCCESS;
    }

    private function hasUsableSetup(Competition $comp): bool
    {
        return $comp->getPools()->whereHas('leagues')->exists();
    }

    private function showPools(Competition $comp): void
    {
        $pools = $comp->getPools()->with('leagues')->get();

        if ($pools->isEmpty()) {
            note('No pools set up yet.');
            return;
        }

        table(['Pool', 'Lanes', 'Leagues (in running order)'], $pools->map(fn(Pool $pool) => [
            $pool->name,
            $pool->lanes,
            $pool->leagues->map(fn($league) => $league->name . ($league->pivot->is_pair ? ' (pairs)' : ''))->join(', '),
        ])->all());
    }

    private function setupPools(Competition $comp): void
    {
        while (true) {
            $action = select('Pool setup', [
                'add' => 'Add a pool',
                'leagues' => "Set a pool's leagues",
                'remove' => 'Remove a pool',
                'done' => 'Done',
            ]);

            if ($action == 'done') {
                if ($this->hasUsableSetup($comp)) {
                    return;
                }

                warning('Add at least one pool with leagues first.');
                continue;
            }

            match ($action) {
                'add' => $this->addPool($comp),
                'leagues' => $this->setLeagues($comp),
                'remove' => $this->removePool($comp),
            };

            $this->showPools($comp);
        }
    }

    private function addPool(Competition $comp): void
    {
        $name = text('Pool name', placeholder: 'Main Pool', required: true);

        $lanes = (int) text(
            'Number of lanes',
            default: (string) $comp->max_lanes,
            required: true,
            validate: fn($value) => ctype_digit($value) && (int) $value > 0 ? null : 'A pool needs at least one lane.',
        );

        $pool = new Pool();
        $pool->competition = $comp->id;
        $pool->name = trim($name);
        $pool->lanes = $lanes;
        $pool->save();
    }

    private function removePool(Competition $comp): void
    {
        $pool = $this->pickPool($comp);

        if (!$pool) return;

        if ($pool->heats()->exists()) {
            warning("{$pool->name} still has heats, so it can't be removed.");
            return;
        }

        $pool->delete();
    }

    private function setLeagues(Competition $comp): void
    {
        $pool = $this->pickPool($comp);

        if (!$pool) return;

        // A base collection keyed by id: Eloquent's only() would re-index the leagues as a list
        $leagues = $comp->getLeagues()->orderBy('id')->get()->keyBy('id')->toBase();

        $chosen = multiselect(
            "Leagues swum in {$pool->name}",
            $leagues->map->name->all(),
            default: $pool->leagues->pluck('id')->all(),
            required: true,
            scroll: 15,
        );

        $pairs = multiselect(
            'Which of these are pairs leagues?',
            $leagues->only($chosen)->map->name->all(),
            default: $pool->leagues->where('pivot.is_pair', true)->pluck('id')->all(),
            hint: 'Leave empty if none are.',
        );

        // Multiselect returns them in list order, so ask for the running order separately
        $remaining = $leagues->only($chosen);
        $order = [];

        while ($remaining->count() > 1) {
            $next = select("Which league runs next in {$pool->name}?", $remaining->map->name->all());
            $order[] = $next;
            $remaining->forget($next);
        }

        $order = [...$order, ...$remaining->keys()];

        $pool->leagues()->detach();

        foreach ($order as $position => $leagueId) {
            $pool->leagues()->attach($leagueId, [
                'order' => $position,
                'is_pair' => in_array($leagueId, $pairs),
            ]);
        }
    }

    private function pickPool(Competition $comp): ?Pool
    {
        $pools = $comp->getPools()->get()->keyBy('id');

        if ($pools->isEmpty()) {
            warning('Add a pool first.');
            return null;
        }

        if ($pools->count() == 1) {
            return $pools->first();
        }

        return $pools->get(select('Which pool?', $pools->map->name->all()));
    }
}
