<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\DigitalJudge\JudgeDQSubmission;
use App\Models\DigitalJudge\Violation\ViolationSubmission;
use App\Models\DQCode;
use App\Models\Orders\Heat;
use App\Models\PenaltyCode;
use App\Models\SERC;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LiveController extends Controller
{

    private function resolveComp(Request $request)
    {
        $comp = Competition::where(DB::raw('DATEDIFF(competitions.when, NOW())'), '>', -2)->where('can_be_live', true)->orderBy(DB::raw('DATEDIFF(competitions.when, NOW())'), 'asc')->first();

        if ($request->has('comp')) {
            $comp = Competition::find($request->input('comp'));
            if (!$comp) return view('live.unavailable', ['message' => 'No competitions with that ID exists!']);
            abort_unless($comp->canBeViewedLive(), 404);
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

    public function display(Request $request)
    {
        $comp = $this->resolveComp($request);
        if ($comp instanceof \Illuminate\View\View) return $comp;

        return view('live.display', ['comp' => $comp]);
    }

    public function all()
    {
        return view('live.unavailable', ['message' => 'No competitions are currently available to view live.']);
    }

    public function liveData(Competition $comp)
    {
        abort_unless($comp->canBeViewedLive(), 404);

        $serc = Cache::remember('live.' . $comp->id . '.drySerc', 60 * 60, function () use ($comp) {
            $serc = SERC::where('competition', $comp->id)->where(function ($query) {
                $query->where('type', 'DRY')->orWhere('name', 'LIKE', '%Dry%');
            })->first();

            if (!$serc) {
                return SERC::where('competition', $comp->id)->first();
            }

            return $serc;
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

        return response()->json(['sercsFinished' => $sercsFinished, 'avgTime' => (float) $avgTime, 'sercStartTime' => (int) $startTime]);
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

        abort_unless($realEvent?->competition == $comp->id, 404);


        // DQs come from both the old JudgeDQSubmission flow and the newer ViolationSubmission one
        $dqs = $this->judgeDqRows($comp, $realEvent)->concat($this->violationRows($comp, $realEvent))->values();

        return view('live.dqs.event', ['comp' => $comp, 'event' => $realEvent, 'dqs' => $dqs]);
    }

    /**
     * Accepted JudgeDQSubmissions for the event, as rows for the live DQ page
     */
    private function judgeDqRows(Competition $comp, $event)
    {
        $dqs = JudgeDQSubmission::with('getHeat.entity', 'getHeat.getPool')->where('event_id', $event->id)->where('event_type', $event::class)->where('competition', $comp->id)->where('resolved', true)->get();

        // Codes are P<penalty code id> or DQ<dq code id>
        $penaltyCodes = PenaltyCode::whereIn('id', $dqs->filter(fn($dq) => str_starts_with((string) $dq->code, 'P'))->map(fn($dq) => substr((string) $dq->code, 1)))->pluck('description', 'id');
        $dqCodes = DQCode::whereIn('id', $dqs->reject(fn($dq) => str_starts_with((string) $dq->code, 'P'))->map(fn($dq) => substr((string) $dq->code, 2)))->pluck('description', 'id');

        return $dqs->map(fn($dq) => [
            'entity' => $dq->getHeat?->entity?->getName($comp),
            'code' => $dq->code,
            'description' => str_starts_with((string) $dq->code, 'P')
                ? $penaltyCodes[substr((string) $dq->code, 1)] ?? 'Penalty code not found'
                : $dqCodes[substr((string) $dq->code, 2)] ?? 'DQ code not found',
            'appealed' => (bool) $dq->appealed,
            'placement' => $this->heatPlacement($dq->getHeat),
            'turn' => $dq->turn,
            'length' => $dq->length,
            'reporter' => [$dq->name, $dq->position],
            'seconder' => [$dq->seconder_name, $dq->seconder_position],
            'details' => $dq->details,
        ]);
    }

    /**
     * Accepted and successfully appealed ViolationSubmissions for the event, as rows for the live DQ page
     */
    private function violationRows(Competition $comp, $event)
    {
        $submissions = ViolationSubmission::with(['entity', 'submitted', 'submitter', 'seconder'])->where('competition_id', $comp->id)->whereMorphedTo('event', $event)->whereIn('status', ['ACCEPTED', 'APPEALED'])->orderBy('created_at')->get();

        $placements = $event->getType() == 'speed' ? $event->getHeats()->with('getPool')->get() : $event->getDraw()->get();

        return $submissions->map(function (ViolationSubmission $submission) use ($comp, $event, $placements) {
            $placement = $placements->first(fn($row) => $row->entity_type == $submission->entity_type && $row->entity_id == $submission->entity_id);

            return [
                'entity' => $submission->entity?->getName($comp),
                'code' => $submission->code(),
                'description' => $submission->submitted?->description ?? 'Code not found',
                'appealed' => $submission->status == 'APPEALED',
                'placement' => $event->getType() == 'speed'
                    ? $this->heatPlacement($placement)
                    : array_filter(['Tank' => $comp->usesTanks() ? $placement?->tank : null, 'Draw' => $placement?->draw], fn($value) => $value !== null),
                'turn' => $submission->turn,
                'length' => $submission->length,
                'reporter' => [$submission->submitter?->name, $submission->submitter_position],
                'seconder' => [$submission->seconder?->name ?? $submission->seconder_name, $submission->seconder_position],
                'details' => $submission->details,
            ];
        });
    }

    /**
     * Where an entity swam, with its pool first when the competition uses pools
     */
    private function heatPlacement(?Heat $heat): array
    {
        $pool = $heat?->getPool ? ['Pool' => $heat->getPool->name] : [];

        return [...$pool, 'Heat' => $heat?->heat, 'Lane' => $heat?->lane];
    }
}
