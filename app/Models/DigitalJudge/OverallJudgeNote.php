<?php

namespace App\Models\DigitalJudge;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OverallJudgeNote extends Model
{
    use HasFactory;

    protected $fillable = ['judge'];
}
