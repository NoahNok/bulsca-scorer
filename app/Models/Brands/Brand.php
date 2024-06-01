<?php

namespace App\Models\Brands;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'website',
        'email',
        'primary_color',
        'secondary_color',
        'logo',
    ];
}
