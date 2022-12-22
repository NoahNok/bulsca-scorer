<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SERCPenalty extends Model
{
    use HasFactory;


    protected $fillable = ['serc', 'team'];

    protected $table = "serc_penalties";
}
