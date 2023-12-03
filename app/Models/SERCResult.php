<?php

namespace App\Models;

use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SERCResult extends Model
{
    use HasFactory, Cloneable;

    protected $table = "serc_results";

    protected $fillable = [
        'marking_point',
        'team'
    ];

    public function getMarkingPointName()
    {
        return $this->belongsTo(SERCMarkingPoint::class, 'marking_point', 'id')->get('name')->implode('name');
    }
}
