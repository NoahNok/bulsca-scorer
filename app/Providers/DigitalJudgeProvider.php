<?php

namespace App\Providers;

use App\DigitalJudge\DigitalJudge;
use Illuminate\Support\ServiceProvider;

class DigitalJudgeProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('digitaljudge', function () {
            return new DigitalJudge();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
