<?php

namespace App\Models\DigitalJudge;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BetterJudgeLog extends Model
{
    use HasFactory;

    public function loggable()
    {
        $clazz = new ($this->loggable_type)();

        foreach (json_decode($this->loggable_data) as $key => $value) {
            $clazz->$key = $value;
        }

        return $clazz;
    }

    public function associated_with()
    {
        return $this->morphTo(__FUNCTION__, 'associated_type', 'associated_id');
    }
}
