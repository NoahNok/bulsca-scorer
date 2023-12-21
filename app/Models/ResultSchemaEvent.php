<?php

namespace App\Models;

use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultSchemaEvent extends Model
{
    use HasFactory, Cloneable;

    public function getActualEvent()
    {
        return $this->morphTo(__FUNCTION__, 'event_type', 'event_id');
    }
}
