<?php

namespace App\Http\Controllers;

use App\Models\CompetitionSpeedEvent;
use App\Models\Competition;

/**
 * A speed event's printed results (Blade). Its page is Manage\SpeedEventController.
 */
class SpeedsEventController extends Controller
{
    public function printResults(Competition $comp, CompetitionSpeedEvent $event)
    {
        $data = [[
            'league' => 'Overall',
            'results' => $event->getRankedResults(),
        ]];

        foreach ($comp->getLeagues->sortBy('name') as $league) {
            $results = $event->getRankedResults($league);

            $data[] = [
                'league' => $league->name,
                'results' => $results,
            ];
        }


        $show_dq_points = $event->scoringSchema->schema['allow_disqualified_to_rank'] ?? false;

        return view('competition.events.speeds.print', ['comp' => $comp, 'event' => $event, 'data' => $data, 'show_dq_points' => $show_dq_points]);
    }
}
