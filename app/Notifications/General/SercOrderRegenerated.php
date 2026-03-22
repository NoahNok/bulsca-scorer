<?php

namespace App\Notifications\General;

use App\Models\Competition;
use App\Notifications\GenericPush;

class SercOrderRegenerated extends GenericPush
{


    /**
     * Create a new notification instance.
     */
    public function __construct(Competition $competition)
    {
        parent::__construct("SERC Order Regenerated", "The SERC Order for $competition->name has been regenerated");
    }
}
