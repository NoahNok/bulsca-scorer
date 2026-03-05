<?php

namespace App\Models\Activity;

use App\Models\User;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'activity',
        'description',
        'user_id',
        'context',
        'related_id',
        'related_type',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function relations()
    {
        return $this->hasMany(ActivityRelation::class, 'activity_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
