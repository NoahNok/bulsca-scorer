<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\DigitalJudge\JudgeDQSubmission;
use App\Models\DigitalJudge\Violation\ViolationSubmission;
use App\Models\DQCode;
use App\Models\PenaltyCode;
use Carbon\Carbon;

/**
 * What's left of the old results site (`results.`): its pages moved to the main site and are
 * redirected there in `routes/results.php`
 */
class PublicResultsController extends Controller
{
    public function resolve($date, $name)
    {

        $d = Carbon::parse($date);

        $comp = Competition::where('when', $d)->firstOrFail();

        return redirect()->route('public.results.comp', ['comp' => $comp->resultsSlug()]);
    }

    public function viewDqPen(Competition $comp, CompetitionTeam $team, string $code)
    {

        // only accepted DQs from released events, so who reported them stays private until then
        $violationSubmission = ViolationSubmission::with(['event', 'applied', 'submitter', 'seconder'])
            ->where('competition_id', $comp->id)
            ->whereMorphedTo('entity', $team)
            ->where('status', 'ACCEPTED')
            ->get()
            ->first(fn($submission) => "{$submission->applied}" === strtoupper($code) && $comp->hasReleased($submission->event));

        if ($violationSubmission) {
            $event = $violationSubmission->event;

            return view('public-results.view-dqpen', [
                'dq' => $violationSubmission->publicDetails(),
                'team' => $team,
                'heat' => $event->getType() == 'speed' ? $event->getHeats()->whereMorphedTo('entity', $team)->first() : null,
                'description' => $violationSubmission->applied->getMessage(),
            ]);
        }

        $judgeSubmission = JudgeDQSubmission::with(['getHeat', 'getEvent'])
            ->where('competition', $comp->id)
            ->where('code', $code)
            ->where('resolved', true)
            ->whereHas('getHeat', fn($query) => $query->whereMorphedTo('entity', $team))
            ->get()
            ->first(fn($submission) => $comp->hasReleased($submission->getEvent));

        if (!$judgeSubmission) {
            $code = strtoupper($code);


            if (str_starts_with($code, 'P')) {
                $number = substr($code, 1);

                return PenaltyCode::where('id', $number)->first()->description ?? "Unknown Penalty Code";
            } else {
                $number = substr($code, 2);
                return DQCode::where('id', $number)->first()->description ?? "Unknown DQ Code";
            }
        }

        return view('public-results.view-dqpen', ['dq' => $judgeSubmission, 'team' => $team, 'heat' => $judgeSubmission->getHeat]);
    }
}
