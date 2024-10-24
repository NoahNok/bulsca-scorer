<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
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
        View::composer('*', function ($view) {

            if (!Auth::check()) {
                return;
            }

            $user = Auth::user();

            // Try and get brand from connected competition
            if ($user->competition && $user->getCompetition->brand) {
                $view->with('brand', $user->getCompetition->getBrand);
                return;
            }

            // Try and get brand if user is a brand account
            if ($user->getBrands()->exists()) {
                $view->with('brand', $user->getBrands->first());
                return;
            }
        });
    }
}
