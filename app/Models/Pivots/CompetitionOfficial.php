<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CompetitionOfficial extends Pivot
{
    public function isOfficialRole(string $role): bool
    {
        return $this->role === $role;
    }
}
