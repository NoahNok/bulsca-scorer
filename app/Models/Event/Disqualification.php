<?php

namespace App\Models\Event;

use App\Models\AbstractClasses\Violation;
use App\Models\DQCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disqualification extends Violation
{
    use HasFactory;

    public function __toString(): string
    {

        return match ($this->code) {
            99915 => 'DNF',
            99904 => 'DNS',
            99901 => 'OOT',
            default => "DQ{$this->code}",
        };
    }

    public function getMessage(): string
    {

        return match ($this->code) {
            99915 => 'Did Not Finish',
            99904 => 'Did Not Start',
            99901 => 'Out Of Time',
            default => DQCode::message($this->code),
        };
    }
}
