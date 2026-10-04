<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Model;

class DrawTank extends Model
{
    protected $table = 'serc_tanks';

    protected $fillable = ['serc', 'tank'];

    public function serc()
    {
        return $this->belongsTo(\App\Models\SERC::class, 'serc');
    }
}
