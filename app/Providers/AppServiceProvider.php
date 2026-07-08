<?php

namespace App\Providers;

use App\Models\Club;
use App\Models\CompetitionTeam;
use App\Models\Competitor;
use App\View\Components\SERC\SERCEditor;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local')) {
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {


        Relation::morphMap([
            'club' => Club::class,
            'team' => CompetitionTeam::class,
            'competitor' => Competitor::class
        ]);

        View::composer('*', function ($view) {

            if (!Auth::check()) {
                return;
            }
        });

        Blade::component('serc-editor', SERCEditor::class);
    }
}
