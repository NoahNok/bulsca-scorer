<?php

namespace App\Models\AbstractClasses;

use App\DigitalJudge\DigitalJudge;
use App\Models\CompetitionTeam;
use App\Models\DigitalJudge\BetterJudgeLog;
use App\Models\SERCResult;
use App\Models\SpeedResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

abstract class Entity extends Model
{

    protected static function booted()
    {
        static::deleting(function (Entity $entity) {
            $entity->speedResults()->delete();
            $entity->sercResults()->delete();
        });
    }

    public function speedResults()
    {
        return $this->morphMany(SpeedResult::class, 'entity');
    }

    public function sercResults()
    {
        return $this->morphMany(SERCResult::class, 'entity');
    }

    public abstract function getName(): string;
}
