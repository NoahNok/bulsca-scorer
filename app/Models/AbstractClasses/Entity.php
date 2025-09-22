<?php

namespace App\Models\AbstractClasses;

use App\DigitalJudge\DigitalJudge;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\DigitalJudge\BetterJudgeLog;
use App\Models\EntityData;
use App\Models\Orders\EntityEventSeed;
use App\Models\SERCResult;
use App\Models\SpeedResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Nette\NotImplementedException;

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

    public function getCompetition()
    {
        return $this->belongsTo(Competition::class, 'competition');
    }

    public function getSeeds()
    {
        return $this->morphMany(EntityEventSeed::class, 'entity');
    }



    public function data()
    {
        return $this->morphOne(EntityData::class, 'entity');
    }

    public function getData(string $key, $default = null)
    {
        $data = $this->data;

        if (!$data || !is_array($data->data)) {
            return $default;
        }

        $value = $data->data[$key];

        return $value ? $value : $default;
    }

    public function setData(string $key, $value)
    {
        $data = $this->data;

        if ($data == null) {
            $data = new EntityData();
            $data->entity()->associate($this);
        }

        $actual_data = $data->data;
        $actual_data[$key] = $value;
        $data->data = $actual_data;

        $data->save();
    }

    public abstract function getName(): string;
}
