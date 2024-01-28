<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\DigitalJudge\JudgeDQSubmission;
use App\Models\SERC;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LiveController extends Controller
{

    private function resolveComp(Request $request)
    {
        $comp = Competition::where(DB::raw('DATEDIFF(competitions.when, NOW())'), '>', -2)->where('can_be_live', true)->orderBy(DB::raw('DATEDIFF(competitions.when, NOW())'), 'asc')->first();

        if (auth()->user() && auth()->user()->isAdmin() && $request->has('comp')) {
            $comp = Competition::find($request->input('comp'));
            if (!$comp) return view('live.unavailable', ['message' => 'No competitions with that ID exists!']);
            $comp->can_be_live = true;
        }

        if (!$comp) return view('live.unavailable', ['message' => 'No competitions are currently available to view live.']);



        return $comp;
    }

    public function index(Request $request)
    {


        $comp = $this->resolveComp($request);
        if ($comp instanceof \Illuminate\View\View) return $comp;

        return view('live.index', ['comp' => $comp]);
    }

    public function liveData(Competition $comp)
    {

        $serc = Cache::remember('live.' . $comp->id . '.drySerc', 60 * 60, function () use ($comp) {
            return SERC::where('competition', $comp->id)->where('name', 'LIKE', '%Dry%')->first();
        });


        $sercsFinished = Cache::remember('live.' . $comp->id . '.howManySercsHasEachTeamFinished', 10, function () use ($comp) {
            return $comp->howManySercsHasEachTeamFinished();
        });
        $avgTime = Cache::remember('live.' . $comp->id . '.getAverageSercTime', 10, function () use ($serc) {
            // This is not ideal, should make comp org select which serc is dry

            if (!$serc) return 360;

            return $serc->getAverageTimeBetweenTeams();
        });


        $startTime = $comp->serc_start_time ? $comp->serc_start_time->timestamp * 1000 : null;
        if (!empty($sercsFinished)) {
            $startTime = Cache::remember('live.' . $comp->id . '.getStartTime', 10, function () use ($serc) {
                $t =  new Carbon(DB::select(' SELECT MAX(sr.created_at) AS start_time FROM serc_results sr INNER JOIN serc_marking_points smp on smp.id=sr.marking_point WHERE serc=?;', [$serc->id])[0]->start_time);
                $t->addMinutes(2); // Assume max 2m reset time
                return $t->timestamp * 1000;
            });
        }

        return response()->json(['sercsFinished' => $sercsFinished, 'avgTime' => (float) $avgTime, 'sercStartTime' => (int) $startTime, 'heatsFinished' => $comp->whichSpeedEventHeatsHaveFinished()]);
    }

    public function dqs(Request $request)
    {
        $comp = $this->resolveComp($request);
        if ($comp instanceof \Illuminate\View\View) return $comp;
        return view('live.dqs.index', ['comp' => $comp]);
    }

    public function eventDqs(Request $request, string $event)
    {
        $comp = $this->resolveComp($request);
        if ($comp instanceof \Illuminate\View\View) return $comp;

        $realEvent = null;

        if (str_starts_with($event, 'sp')) {
            $realEvent = CompetitionSpeedEvent::where('id', substr($event, 3))->first();
        } else {
            $realEvent = SERC::where('id', substr($event, 3))->first();
        }


        $dqs = JudgeDQSubmission::where('event_id', $realEvent->id)->where('event_type', $realEvent::class)->where('competition', $comp->id)->where('resolved', true)->get();

        return view('live.dqs.event', ['comp' => $comp, 'event' => $realEvent, 'dqs' => $dqs]);
    }
}
