<?php

namespace App\Models;

use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpeedEvent extends Model
{
    use HasFactory, Cloneable;

    public function getName()
    {
        return $this->name;
    }
}
