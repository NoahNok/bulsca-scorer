<?php

namespace App\Notifications\General\DigitalJudge;

use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\SERC;
use App\Notifications\BrandBasePushNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SercMarked extends BrandBasePushNotification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(SERC $serc, CompetitionTeam $team, string $by = "HOST")
    {

        $sercName = $serc->getName();
        $teamName = $team->getFullname();
        $competition = $team->getCompetition;

        $totalTeams = CompetitionTeam::where('competition', $competition->id)->max('serc_order');
        $currentTeamPosition = $team->serc_order;



        parent::__construct($competition, "$sercName SERC Marked", "$teamName ($currentTeamPosition/$totalTeams) has been marked by $by");
    }
}
