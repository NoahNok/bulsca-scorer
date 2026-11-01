<?php

namespace App\Models\SERC;

use App\Models\SERCMarkingPoint;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /** The marking points marked this way */
    public function markingPoints(): HasMany
    {
        return $this->hasMany(SERCMarkingPoint::class, 'marking_point_template_id');
    }
}
