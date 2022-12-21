<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SERCResult extends Model
{
    use HasFactory;

    protected $table = "serc_results";

    protected $fillable = [
        'marking_point',
        'team'
    ];
}
