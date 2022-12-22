<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultSchemaEvent extends Model
{
    use HasFactory;

    public function getActualEvent()
    {
        return $this->morphTo(__FUNCTION__, 'event_type', 'event_id');
    }
}
