<?php

namespace App\Models;

use App\Models\Organisation\Organisation;
use Illuminate\Database\Eloquent\Model;

class Championship extends Model
{

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where(function ($query) use ($value) {

            $query->where('id', $value)
                ->orWhere('name', str_replace('-', ' ', $value));
        })->first();
    }
    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function competitions()
    {
        return $this->hasMany(Competition::class)->orderBy('when', 'asc');
    }

    public function slug()
    {
        return str_replace(' ', '-', $this->name);
    }
}
