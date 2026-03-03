<?php

namespace App\Models\AbstractClasses;

use App\DigitalJudge\DigitalJudge;
use App\DTO\EntityGrouping;
use App\Models\Club;
use App\Models\Competition;
use App\Models\EntityData;
use App\Models\Event\Disqualification;
use App\Models\Event\Penalty;
use App\Models\League;
use App\Models\Orders\Draw;
use App\Models\Orders\EntityEventSeed;
use App\Models\Orders\Heat;
use App\Models\SERCResult;
use App\Models\SpeedResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Nette\NotImplementedException;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

abstract class Entity extends Model
{
    use CascadesDeletes;

    protected $cascadeDeletes = [
        'speedResults',
        'sercResults',
        'getSeeds',
        'data',
        'disqualifications',
        'penalties',
        'heats',
        'draws'
    ];

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

    public function disqualifications()
    {
        return $this->morphMany(Disqualification::class, 'entity');
    }

    public function penalties()
    {
        return $this->morphMany(Penalty::class, 'entity');
    }

    public function heats()
    {
        return $this->morphMany(Heat::class, 'entity');
    }

    public function draws()
    {
        return $this->morphMany(Draw::class, 'entity');
    }


    public function getSeedTimes()
    {

        // if ($this->getCompetition->seed_per_heat) {
        return (object) $this->getSeeds->mapWithKeys(fn($seed) => [$seed->speed_event => ['id' => $seed->id, 'seed' => SpeedResult::prettyTime($seed->seed)]])->toArray();
        // } else {
        //     $seed = $this->getSeeds->sortBy('speed_event')->first();

        //     return [$seed->speed_event => ['id' => $seed->id, 'seed' => SpeedResult::prettyTime($seed->seed)]];
        // }
    }

    public function getLeague()
    {
        return $this->leagues()->first();
    }


    public function leagues()
    {
        return $this->morphToMany(League::class, 'entity', 'leagueables');
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


    protected static array $nameCache = [];


    public function getName(?Competition $comp): string
    {

        $clazz = static::class;

        $cacheKey = "entity.{$clazz}-{$this->id}.name";

        if (isset(self::$nameCache[$cacheKey])) {
            return self::$nameCache[$cacheKey];
        }


        $name = Cache::tags("{$comp->cacheKey()}}.entity-names")->rememberForever($cacheKey, function () use ($comp) {
            return $this->getFormattedName($comp);
        });

        self::$nameCache[$cacheKey] = $name;

        return $name;
    }

    public abstract function getFormattedName(?Competition $comp): string;

    public abstract function getGrouping(): EntityGrouping;
}
