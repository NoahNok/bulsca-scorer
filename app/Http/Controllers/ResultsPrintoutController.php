<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\ResultSchema;
use App\Models\SERC;
use App\Pdf\ResultsPdfCreator;
use Illuminate\Support\Facades\Gate;

/**
 * Results printouts: PDFs of an Event's or a Results sheet's rankings, built by ResultsPdfCreator
 */
class ResultsPrintoutController extends Controller
{
    public function speedEvent(Competition $comp, CompetitionSpeedEvent $event)
    {
        return (new ResultsPdfCreator($comp))->speedEvent($event);
    }

    public function serc(Competition $comp, SERC $serc)
    {
        return (new ResultsPdfCreator($comp))->serc($serc);
    }

    public function standings(Competition $comp, ResultSchema $sheet)
    {
        return (new ResultsPdfCreator($comp))->standings($sheet);
    }

    public function breakdown(Competition $comp, ResultSchema $sheet)
    {
        return (new ResultsPdfCreator($comp))->breakdown($sheet);
    }

    public function allSheets(Competition $comp)
    {
        return (new ResultsPdfCreator($comp))->allSheets();
    }

    /**
     * The old sheet print pages, outside their competition, sent on to their printouts ($kind is
     * standings or breakdown)
     */
    public function old(ResultSchema $sheet, string $kind)
    {
        $comp = $sheet->getCompetition;
        Gate::authorize('access', [$comp, 'results']);

        return redirect()->route("manage.competitions.results.print.$kind", [$comp, $sheet], 301);
    }
}
