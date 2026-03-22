<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Draw extends Model
{
    use HasFactory;

    public function entity()
    {
        return $this->morphTo();
    }

    public function event()
    {
        return $this->morphTo();
    }
}
