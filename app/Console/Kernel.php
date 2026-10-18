<?php

namespace App\Console;

use ALajusticia\Logins\Models\Login;
use App\Models\Competition;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Config;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function() {
            
            Config::set('database.default', 'whatif');

            Competition::whereNotNull('wi_user')->where('updated_at', '<', now()->subDays(14))->delete();

            Config::set('database.default', 'mysql');
        })->daily();

        // expired logins (sessions and remember tokens), plus ones signed out over a month ago
        $schedule->command('expirable:purge', [Login::class])->daily();
        $schedule->call(function () {
            Login::onlyTrashed()->where('deleted_at', '<', now()->subMonth())->forceDelete();
        })->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
