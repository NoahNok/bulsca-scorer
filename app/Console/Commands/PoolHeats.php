<?php

namespace App\Console\Commands;

use App\Exceptions\HeatException;
use App\Models\Competition;
use App\Models\Pool;
use App\Services\PoolHeatService;
use Illuminate\Console\Command;

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

    const DONE = 'Done';

    /**
     * Execute the console command.
     */
    public function handle(PoolHeatService $service)
    {
        $comp = Competition::find($this->argument('competition_id'));

        if (!$comp) {
            $this->error('No competition found with that id');
            return self::FAILURE;
        }

        $this->info("Using: {$comp->name}");

        if ($comp->heats_per_event) {
            $this->error('Pool heats need heats per event to be off.');
            return self::FAILURE;
        }

        $event = $comp->getSpeedEvents()->orderBy('id')->first();

        if (!$event) {
            $this->error('The competition has no speed events.');
            return self::FAILURE;
        }

        if (!$comp->use_pools) {
            if (!$this->confirm('Pools are turned off for this competition. Turn them on?', true)) {
                $this->error('aborting');
                return self::FAILURE;
            }

            $comp->use_pools = true;
            $comp->save();
        }

        $this->showPools($comp);

        if (!$this->hasUsableSetup($comp) || !$this->confirm('Use the saved pool setup?', true)) {
            $this->setupPools($comp);
        }

        $style = PoolHeatService::STYLE_STANDARD;

        if ($event->scorable_entity == 'competitor') {
            $style = $this->choice('Heat style', [PoolHeatService::STYLE_STANDARD, PoolHeatService::STYLE_PAIRS], PoolHeatService::STYLE_STANDARD);
        }

        $unplaced = $service->unplacedEntities($comp);

        if ($unplaced->isNotEmpty()) {
            $this->warn("These entities aren't in any pooled league and won't get a heat:");
            $unplaced->each(fn($entity) => $this->line(" - {$entity->getName($comp)}"));

            if (!$this->confirm('Continue anyway?')) {
                $this->error('aborting');
                return self::FAILURE;
            }
        }

        try {
            // Check the setup before anything is deleted
            $service->plan($comp, $style);
        } catch (HeatException $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        $impact = $service->deletionImpact($comp);

        if ($impact['heats'] > 0 && !$this->confirm("This will delete {$impact['heats']} existing heat entries, {$impact['oofs']} OOFs and {$impact['dq_submissions']} DQ submissions. Continue?")) {
            $this->error('aborting');
            return self::FAILURE;
        }

        $service->generate($comp, $style);

        foreach ($comp->getPools()->withCount('heats')->get() as $pool) {
            $heats = $pool->heats()->max('heat') ?? 0;
            $this->info("{$pool->name}: {$heats} heats, {$pool->heats_count} entries");
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
            $this->line('No pools set up yet.');
            return;
        }

        $this->table(['Pool', 'Lanes', 'Leagues (in running order)'], $pools->map(fn(Pool $pool) => [
            $pool->name,
            $pool->lanes,
            $pool->leagues->map(fn($league) => $league->name . ($league->pivot->is_pair ? ' (pairs)' : ''))->join(', '),
        ]));
    }

    private function setupPools(Competition $comp): void
    {
        while (true) {
            $action = $this->choice('Pool setup', ['Add a pool', 'Remove a pool', "Set a pool's leagues", self::DONE], self::DONE);

            match ($action) {
                'Add a pool' => $this->addPool($comp),
                'Remove a pool' => $this->removePool($comp),
                "Set a pool's leagues" => $this->setLeagues($comp),
                default => null,
            };

            if ($action != self::DONE) {
                $this->showPools($comp);
                continue;
            }

            if ($this->hasUsableSetup($comp)) {
                return;
            }

            $this->warn('Add at least one pool with leagues first.');
        }
    }

    private function addPool(Competition $comp): void
    {
        $name = trim((string) $this->ask('Pool name (e.g. Main Pool)'));

        if ($name == '') {
            $this->warn('A pool needs a name.');
            return;
        }

        $lanes = (int) $this->ask('Number of lanes', (string) $comp->max_lanes);

        if ($lanes < 1) {
            $this->warn('A pool needs at least one lane.');
            return;
        }

        $pool = new Pool();
        $pool->competition = $comp->id;
        $pool->name = $name;
        $pool->lanes = $lanes;
        $pool->save();
    }

    private function removePool(Competition $comp): void
    {
        $pool = $this->pickPool($comp);

        if (!$pool) return;

        if ($pool->heats()->exists()) {
            $this->warn("{$pool->name} still has heats, so it can't be removed.");
            return;
        }

        $pool->delete();
    }

    private function setLeagues(Competition $comp): void
    {
        $pool = $this->pickPool($comp);

        if (!$pool) return;

        $remaining = $comp->getLeagues()->orderBy('id')->get()->keyBy('name');
        $chosen = [];

        $this->line("Pick {$pool->name}'s leagues in the order their heats should run.");

        while ($remaining->isNotEmpty()) {
            $name = $this->choice('Next league', [...$remaining->keys()->all(), self::DONE], self::DONE);

            if ($name == self::DONE) break;

            $chosen[] = $remaining->pull($name);
        }

        $pool->leagues()->detach();

        foreach ($chosen as $order => $league) {
            $pool->leagues()->attach($league->id, [
                'order' => $order,
                'is_pair' => $this->confirm("Is {$league->name} a pairs league?"),
            ]);
        }
    }

    private function pickPool(Competition $comp): ?Pool
    {
        $pools = $comp->getPools()->get()->keyBy('name');

        if ($pools->isEmpty()) {
            $this->warn('Add a pool first.');
            return null;
        }

        return $pools->get($this->choice('Which pool?', $pools->keys()->all()));
    }
}
