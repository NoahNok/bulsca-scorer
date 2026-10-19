<?php

namespace App\Console;

use ALajusticia\Logins\Models\Login;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
        // expired logins (sessions and remember tokens)
        $schedule->command('expirable:purge', [Login::class])->daily();

        // old logins, log files and unused WhatIf competitions and accounts, per the privacy policy
        $schedule->command('retention:prune')->daily();
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
