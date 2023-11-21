<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DQCode extends Model
{
    use HasFactory;

    protected $table = "dq_codes";

    static function message($code)
    {

        if (str_starts_with(strtolower($code), 'dq')) $code = substr($code, 2);

        return DQCode::find($code)?->description ?: "";
    }
}
