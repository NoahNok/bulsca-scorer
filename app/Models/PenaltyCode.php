<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenaltyCode extends Model
{
    use HasFactory;


    static function message($code)
    {

        if (str_starts_with(strtolower($code), 'p')) $code = substr($code, 1);

        return PenaltyCode::find($code)?->description ?: "";
    }
}
