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

    public function access(User $user, Competition $comp, $allowedRoles = ['admin'])
    {

        if ($user->admin) return true; // Allow global admins

        if ($user->competition && $user->competition == $comp->id) return true; // Allow competition owner to access

        // Check brand access
        return $comp->getBrand->isBrandRole($user, $allowedRoles);
    }
}
