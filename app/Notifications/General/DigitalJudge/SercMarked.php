<?php

namespace App\Notifications\General\DigitalJudge;

use App\Models\AbstractClasses\Entity;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\SERC;

use App\Notifications\GenericPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SercMarked extends GenericPush
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(SERC $serc, Entity $entity)
    {

        $sercName = $serc->getName();
        $teamName = $entity->getName($serc->getCompetition);

        parent::__construct("$sercName SERC Marked", "$teamName has been marked.");
    }
}
