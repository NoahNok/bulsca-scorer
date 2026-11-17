<?php

namespace App\Providers;

use ALajusticia\Logins\Models\Login;
use App\DigitalJudge\DigitalJudge;
use App\DTO\JudgeAuthUser;
use App\Models\Club;
use App\Models\CompetitionTeam;
use App\Models\Competitor;
use App\Scoring\ResultsVersion;
use App\Scoring\CompetitionResults;
use App\Scoring\ScoringRun;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Events\TransactionRolledBack;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
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
        // One per request: the Events already scored, shared between Results sheets
        $this->app->scoped(ScoringRun::class);
        // One per request: each competition's results version, as last read
        $this->app->scoped(ResultsVersion::class);
        // One per request: what pages read results from
        $this->app->scoped(CompetitionResults::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Anything saved or deleted may change a result, so what was scored before it is dropped,
        // and the results version of the competition it belongs to moves on
        Event::listen(['eloquent.saved: *', 'eloquent.deleted: *'], function (string $event, array $models) {
            if ($this->app->resolved(ScoringRun::class)) {
                $this->app->make(ScoringRun::class)->forget();
            }

            // A save that changed nothing changes no result
            $model = $models[0];
            if (str_starts_with($event, 'eloquent.deleted') || $model->wasRecentlyCreated || $model->wasChanged()) {
                $this->app->make(ResultsVersion::class)->changed($model);
            }
        });



        // What a rolled-back transaction changed, it didn't
        Event::listen(TransactionRolledBack::class, function () {
            if ($this->app->resolved(ScoringRun::class)) {
                $this->app->make(ScoringRun::class)->forget();
            }
            if ($this->app->resolved(ResultsVersion::class)) {
                $this->app->make(ResultsVersion::class)->forget();
            }
        });

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


        Auth::viaRequest('can-judge', function ($request) {


            if (!DigitalJudge::canClientJudge()) {
                return null;
            }

            return new JudgeAuthUser(
                DigitalJudge::getClientId(),
                DigitalJudge::getClientName()
            );
        });

        // A login without "remember me" gets expires_at = now + session lifetime and the package
        // never moves it, so a session kept alive past that drops out of logins() and can't be
        // listed or signed out. Slide it along whenever activity is recorded, like the session.
        // The activity update interval is added since last_activity_at can lag by that much.
        Login::updating(function (Login $login) {
            if ($login->isDirty('last_activity_at') && $login->remember_token === null && $login->session_id !== null) {
                $login->expiresAt(now()
                    ->addMinutes((int) config('session.lifetime', 120))
                    ->addSeconds((int) config('logins.activity_update.interval', 0)));
            }
        });
    }
}
