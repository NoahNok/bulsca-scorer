<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ResultSchema extends Model
{
    use HasFactory;

    public function getEvents()
    {
        return $this->hasMany(ResultSchemaEvent::class, 'schema', 'id');
    }

    public function getCompetition()
    {
        return $this->hasOne(Competition::class, 'id', 'competition');
    }

    public function getTargetLeagueQueryExtra()
    {

        switch (Str::lower($this->league)) {
            case "o":
                return "";

            case "a":
                return " AND ct.team='A' ";

            case "b":
                return " AND ct.team !='A' ";

            case "f":
                return " ";

            default:
                return "";
        }
    }
}
