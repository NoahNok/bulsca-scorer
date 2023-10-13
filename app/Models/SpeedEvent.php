<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpeedEvent extends Model
{
    use HasFactory;

    public function getName()
    {
        return $this->name;
    }
}
