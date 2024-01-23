<?php

namespace App\Models\AbstractClasses;

use App\DigitalJudge\DigitalJudge;
use App\Models\CompetitionTeam;
use App\Models\DigitalJudge\BetterJudgeLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

abstract class Loggable extends Model
{

    private bool $shouldLog = true;

    abstract public function getJudgeLogTitle();
    abstract public function getJudgeLogDescription();
    abstract public function resolveJudgeLogTeam(): ?CompetitionTeam;
    abstract public function resolveJudgeLogName();
    abstract public function resolveJudgeLogAssociation();

    private static bool $skipLogging = false;

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


    public function disableLogging()
    {
        $this->shouldLog = false;
    }

    public static function setLogging(bool $logging)
    {
        self::$skipLogging = !$logging;
    }

    private function log($action)
    {

        if (!$this->shouldLog) {
            return;
        }


        if (self::$skipLogging) {
            return;
        }

        $judgeName = "SCORER";

        // GET CURRENT ROUTE NAME
        $routeName = Route::currentRouteName();

        if (str_starts_with($routeName, 'dj.') && DigitalJudge::getClientName() != 'UNKNOWN') {
            $judgeName = DigitalJudge::getClientName();
        }


        $log = new BetterJudgeLog();
        $log->loggable_type = get_class($this);
        $log->loggable_data = json_encode($this->getAttributes());
        $log->associated_with()->associate($this->resolveJudgeLogAssociation());
        $log->team = $this->resolveJudgeLogTeam()->id;
        $log->action = $action;

        $log->competition = DigitalJudge::getClientCompetition() ? DigitalJudge::getClientCompetition()->id : (auth()->user()->getCompetition()?->id ?? Session::get('ac')->id);
        $log->judge_name = $judgeName;
        $log->save();
    }
}
