<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SERCJudge extends Model
{
    use HasFactory;

    protected $table = "serc_judges";

    public function getMarkingPoints()
    {
        return $this->hasMany(SERCMarkingPoint::class, 'judge', 'id');
    }

    public function getSERC()
    {
        return $this->hasOne(SERC::class, 'id', 'serc');
    }
}
