<?php

namespace App\Models\Orders;

use App\Models\EventOOF;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Heat extends Model
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

    public function getOOF($speedId)
    {
        return $this->hasOne(EventOOF::class, 'heat_lane', 'id')->where('event', $speedId)->first();
    }
}
