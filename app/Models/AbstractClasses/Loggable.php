<?php

namespace App\Models\AbstractClasses;

use App\DigitalJudge\DigitalJudge;
use App\Models\CompetitionTeam;
use App\Models\DigitalJudge\BetterJudgeLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

abstract class Loggable extends Model
{



    abstract public function getJudgeLogTitle();
    abstract public function getJudgeLogDescription();
    abstract public function resolveJudgeLogTeam(): CompetitionTeam;
    abstract public function resolveJudgeLogName();
    abstract public function resolveJudgeLogAssociation();

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->log('created');
        });

        static::updated(function ($model) {
            $model->log('updated');
        });

        static::deleted(function ($model) {
            $model->log('deleted');
        });
    }

    private function log($action)
    {

        if (!str_starts_with(Route::currentRouteName(), 'dj.')) {
            return;
        }


        // If this wasn't from a logged in judge skip it
        if (DigitalJudge::getClientName() == 'UNKNOWN') {
            return;
        }


        $log = new BetterJudgeLog();
        $log->loggable_type = get_class($this);
        $log->loggable_data = json_encode($this->getAttributes());
        $log->associated_with()->associate($this->resolveJudgeLogAssociation());
        $log->team = $this->resolveJudgeLogTeam()->id;
        $log->action = $action;
        $log->competition = DigitalJudge::getClientCompetition()->id;
        $log->judge_name = DigitalJudge::getClientName();
        $log->save();
    }
}
