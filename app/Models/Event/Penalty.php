<?php

namespace App\Models\Event;

use App\Models\AbstractClasses\Violation;
use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penalty extends Violation
{
    use HasFactory, Cloneable;

    public function __toString(): string
    {
        return "P{$this->code}";
    }
}
