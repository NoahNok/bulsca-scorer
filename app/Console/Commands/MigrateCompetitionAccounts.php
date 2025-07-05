<?php

namespace App\Console\Commands;

use App\Models\Competition;
use App\Models\User;
use Illuminate\Console\Command;

class MigrateCompetitionAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'competition:migrate-accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates competition accounts from being attached to once competition to newer system using access';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $this->info('Starting migration of competition accounts...');

        $users = User::whereNotNull('competition')->get();

        foreach ($users as $user) {
            $competition = Competition::find($user->competition);

            if (!$competition) {
                $this->error("Competition not found for user: {$user->name} ({$user->email}) SKIPPING");
                continue;
            }

            $this->info("Migrating user: {$user->name} ({$user->email}) for competition: {$competition->name}");

            $competition->addAccount($user, 'admin');

            $user->competition = null;
            $user->save();

            $this->info("User: {$user->name} ({$user->email}) migrated successfully.");
        }
    }
}
