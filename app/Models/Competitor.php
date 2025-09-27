<?php

namespace App\Models;

use App\Models\AbstractClasses\Entity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competitor extends Entity
{
    use HasFactory;

    public function getName(): string
    {
        // Implement the logic to return the competitor's name
        // For example, if there is a 'name' property:
        return $this->name ?? '';
    }
}
