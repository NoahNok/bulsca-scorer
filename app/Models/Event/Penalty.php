<?php

namespace App\Models\Event;

use App\Models\AbstractClasses\Violation;
use App\Models\PenaltyCode;
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

    public function getMessage(): string
    {
        return PenaltyCode::message($this->code);
    }
}
