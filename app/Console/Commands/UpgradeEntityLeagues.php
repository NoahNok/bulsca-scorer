<?php

namespace App\Console\Commands;

use App\Models\AbstractClasses\Entity;
use App\Models\Club;
use App\Models\CompetitionTeam;
use App\Models\Competitor;
use App\Models\League;
use Illuminate\Console\Command;

class UpgradeEntityLeagues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'entity:upgrade-entity-leagues';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        foreach (Club::all() as $club) {
            $this->updateEntityLeague($club);
        }

        foreach (CompetitionTeam::all() as $club) {
            $this->updateEntityLeague($club);
        }

        foreach (Competitor::all() as $club) {
            $this->updateEntityLeague($club);
        }
    }

    private function updateEntityLeague(Entity $entity)
    {
        $league = League::find($entity->league);

        if (!$league) return;

        $entity->leagues()->attach($league);
    }
}
