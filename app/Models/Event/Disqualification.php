<?php

namespace App\Models\Event;

use App\Models\AbstractClasses\Violation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disqualification extends Violation
{
    use HasFactory;

    public function __toString(): string
    {
        return "DQ{$this->code}";
    }
}
