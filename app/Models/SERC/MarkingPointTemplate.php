<?php

namespace App\Models\SERC;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MarkingPointTemplate extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];
}
