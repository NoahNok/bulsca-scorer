<?php

namespace App\Models;

use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SERCPenalty extends Model
{
    use HasFactory, Cloneable;


    protected $fillable = ['serc', 'team'];

    protected $table = "serc_penalties";
}
