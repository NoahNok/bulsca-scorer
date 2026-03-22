<?php

namespace App\Policies;

use App\Models\Organisation\Organisation;
use App\Models\User;

class OrganisationPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function access(User $user, Organisation $organisation, $access_to = [])
    {



        if ($user->admin) return true; // Allow global admins

        if (is_string($access_to)) {
            $access_to = explode('|', $access_to);
        }



        return $organisation->canUser($user, $access_to);
    }
}
