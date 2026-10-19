<?php

namespace App\Console\Commands;

use ALajusticia\Logins\Models\Login;
use App\Models\Competition;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

/**
 * Deletes personal data once the privacy policy says it's no longer kept. Keep the periods here
 * in step with the retention section of the privacy policy.
 */
class PruneRetainedData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'retention:prune';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes old login records and unused WhatIf competitions and accounts.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // logins unused for a year, and ones signed out over a month ago
        Login::withoutGlobalScopes()->withTrashed()
            ->where(fn($query) => $query
                ->where('last_activity_at', '<', now()->subYear())
                ->orWhere('deleted_at', '<', now()->subMonth()))
            ->forceDelete();

        // WhatIf competitions untouched for 14 days, then accounts left with none (new ones get 14 days to start one)
        Config::set('database.default', 'whatif');

        try {
            Competition::whereNotNull('wi_user')->where('updated_at', '<', now()->subDays(14))->delete();

            User::where('created_at', '<', now()->subDays(14))->whereDoesntHave('getWhatIfEditors')->delete();
        } finally {
            Config::set('database.default', 'mysql');
        }
    }
}
