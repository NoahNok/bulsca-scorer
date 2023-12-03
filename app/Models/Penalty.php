<?php

namespace App\Models;

use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penalty extends Model
{
    use HasFactory, Cloneable;

    protected $table = "speed_result_penalties";
}
