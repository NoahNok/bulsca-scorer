<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventOOF extends Model
{
    use HasFactory;

    protected $table = "event_oofs";
    protected $fillable = ['heat_lane', 'event'];
}
