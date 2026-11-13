<?php

namespace App\Models;

use App\Helpers\ClassHelpers;
use App\Scoring\CompetitionResults;
use App\Models\ResultSchemas\ClubSERCResultSchema;
use App\Models\ResultSchemas\HighestSumResultSchema;
use App\Models\ResultSchemas\NationalsResultSchema;
use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ResultSchema extends Model
{
    use HasFactory, Cloneable;

    protected $casts = [
        'schema' => 'array',
    ];

    protected static function booted()
    {
        // Its cached results have no version to outlive, so they go with it
        static::deleted(function (ResultSchema $sheet) {
            CompetitionResults::forget($sheet);
        });
    }

    public function getEvents()
    {
        return $this->hasMany(ResultSchemaEvent::class, 'schema', 'id');
    }

    public function getCompetition()
    {
        return $this->hasOne(Competition::class, 'id', 'competition');
    }
}
