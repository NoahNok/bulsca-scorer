<?php

namespace App\Notifications\General\DigitalJudge;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\SERC;
use App\Notifications\BrandBasePushNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SpeedMarked extends BrandBasePushNotification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(CompetitionSpeedEvent $speed, int $heatNo)
    {
        $speedName = $speed->getName();
        $competition = $speed->getCompetition;

        parent::__construct($competition, "$speedName Marked", "Heat $heatNo has been marked.");
    }
}
