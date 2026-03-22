<?php

namespace App\Models;

use App\Models\Organisation\Organisation;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountInvite extends Model
{
    use HasFactory, HasUuids;

    protected $casts = [
        'details' => 'array'
    ];

    public function to()
    {
        return $this->morphTo();
    }

    public function getUser()
    {
        return User::where('email', $this->email)->first();
    }

    public function getName()
    {

        $to = $this->to;
        if ($to instanceof Organisation) {
            return $to->name;
        }

        if ($to instanceof Competition) {
            return $to->name;
        }

        return 'Scoring.Events';
    }

    public function getType()
    {

        $to = $this->to;
        if ($to instanceof Organisation) {
            return "an organisation";
        }

        if ($to instanceof Competition) {
            return "a competition";
        }

        return 'Scoring.Events';
    }
}
