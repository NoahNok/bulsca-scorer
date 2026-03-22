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

        if (is_string($access_to)) {
            $access_to = explode('|', $access_to);
        }


        if ($comp->canUser($user, $access_to)) {
            return true;
        }


        $organisation = $comp->getOrganisation;

        if (!$organisation) {
            return false; // No organisation - so no access
        }

        return $organisation->canUser($user, $access_to);
    }
}
