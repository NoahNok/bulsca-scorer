<?php

namespace App\Notifications\General;

use App\Models\Competition;
use App\Notifications\BrandBasePushNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SercOrderRegenerated extends BrandBasePushNotification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(Competition $competition)
    {
        parent::__construct($competition, "SERC Order Regenerated", "The SERC Order for $competition->name has been regenerated");
    }
}
