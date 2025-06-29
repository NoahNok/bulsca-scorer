<?php

namespace App\Policies;

use App\Models\Competition;
use App\Models\User;

class CompetitionPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function access(User $user, Competition $comp, $access_to = [])
    {
        if ($user->admin) return true; // Allow global admins

        echo "Checking access for user {$user->id} to competition {$comp->id} with access type(s): " . implode(', ', (array) $access_to) . "\n";

        return $comp->canUser($user, $access_to);
    }
}
