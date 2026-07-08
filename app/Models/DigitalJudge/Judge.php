<?php

namespace App\Models\DigitalJudge;

use App\Models\Competition;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Judge extends User
{
    use HasUuids;
}
