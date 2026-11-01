<?php

namespace App\Http\Controllers;

use App\Helpers\Pagination;
use App\Helpers\RouteHelpers;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\DigitalJudge\JudgeDQSubmission;
use App\Models\DigitalJudge\Violation\ViolationSubmission;
use App\Models\DQCode;
use App\Models\Orders\Heat;
use App\Models\PenaltyCode;
use App\Models\SERC;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Services\SercOrderEstimate;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LiveController extends Controller
{

    /**
     * The competition the live pages are for: the one in `?comp=`, or the nearest live one. A message saying why,
     * when there isn't one.
     */
    private function resolveComp(Request $request): Competition|string
    {
        if ($request->has('comp')) {
            $comp = Competition::find($request->input('comp'));
            if (!$comp) return 'No competition with that ID exists.';
            abort_unless($comp->canBeViewedLive(), 404);

            return $comp;
        }

        return Competition::where(DB::raw('DATEDIFF(competitions.when, NOW())'), '>', -2)->where('can_be_live', true)->orderBy(DB::raw('DATEDIFF(competitions.when, NOW())'), 'asc')->first()
            ?? 'No competition is live right now. One shows here on the day it runs.';
    }

    /** A live page, or the Unavailable page when there's no competition to show */
    private function page(Request $request, string $component, callable $props): InertiaResponse
    {
        $comp = $this->resolveComp($request);
        if (is_string($comp)) return $this->unavailable($comp);

        return Inertia::render($component, ['competition' => $this->header($comp, $request), ...$props($comp)]);
    }

    /** What LiveLayout draws above every live page, and its tabs' urls (keeping `?comp=`) */
    private function header(Competition $comp, Request $request): array
    {
        $query = $request->only('comp');

        return [
            'id' => $comp->id,
            'name' => $comp->name,
            'when' => $comp->when->toDateString(),
            'where' => $comp->where,
            'logo' => $comp->getOrganisation?->getLogo(),
            'dataUrl' => route('live.data', $comp->id),
            'statusMessage' => $comp->status_message ?: null,
            'links' => [
                'live' => route('live', $query),
                'dqs' => route('live.dqs', $query),
                'display' => route('live.display', $query),
                'heatsDraws' => RouteHelpers::mainRoute('landing.competition.heats-draws', $comp->getSlug()),
            ],
        ];
    }

    private function unavailable(string $message): InertiaResponse
    {
        $past = Competition::where('when', '<=', now())
            ->where(fn ($query) => $query->where('public_results', true)->orWhere('can_be_live', true))
            ->orderBy('when', 'desc')
            ->paginate(10)
            // keeping ?comp=, so page 2 of a bad id's list isn't the live competition's page
            ->withQueryString();

        return Inertia::render('Live/Unavailable', [
            'message' => $message,
            'competitions' => $past->getCollection()->map(fn (Competition $comp) => [
                'id' => $comp->id,
                'name' => $comp->name,
                'when' => $comp->when->toDateString(),
                'where' => $comp->where,
                'url' => route('live', ['comp' => $comp->id]),
            ]),
            'pagination' => Pagination::props($past),
        ]);
    }

    /** The SERC order's Tanks, each Team's Draw and name, for the Live and Display pages */
    private function sercOrder(Competition $comp): array
    {
        $useTanks = $comp->usesTanks();

        $tanks = collect($comp->getDraws()[0]['draws'] ?? [])
            ->map(fn ($draws) => collect($draws)->filter(fn ($draw) => $draw->entity)->values())
            ->filter(fn ($draws) => $draws->isNotEmpty())
            ->values()
            ->map(fn ($draws, $i) => [
                'label' => $useTanks ? 'Tank ' . ($i + 1) : null,
                'teams' => $draws->map(fn ($draw) => [
                    'id' => $draw->entity->id,
                    'draw' => $draw->draw,
                    'name' => $draw->entity->getName($comp),
                ]),
            ]);

        return [
            'tanks' => $tanks,
            'sercCount' => $comp->getSERCs->count(),
        ];
    }

    public function index(Request $request)
    {
        return $this->page($request, 'Live/Index', fn ($comp) => ['sercOrder' => $this->sercOrder($comp)]);
    }

    public function display(Request $request)
    {
        return $this->page($request, 'Live/Display', fn ($comp) => [
            'sercOrder' => $this->sercOrder($comp),
            'qr' => (string) QrCode::size(112)->margin(0)->generate(route('live', $request->only('comp'))),
        ]);
    }

    public function all()
    {
        return $this->unavailable('No competition is live right now. One shows here on the day it runs.');
    }

    /**
     * Each Team's SERC progress and Estimated start (SercOrderEstimate), polled every 5s by the live pages.
     * Starts are unix seconds. The Organisers' status message comes too, read fresh each poll, so it shows up
     * and goes away without a reload.
     */
    public function liveData(Competition $comp)
    {
        abort_unless($comp->canBeViewedLive(), 404);

        $teams = Cache::remember('live.' . $comp->id . '.sercOrder', 10, fn () => SercOrderEstimate::for($comp)->teams());

        return response()->json(['teams' => (object) $teams, 'statusMessage' => $comp->status_message ?: null]);
    }

    public function dqs(Request $request)
    {
        return $this->page($request, 'Live/Dqs', fn ($comp) => [
            'groups' => collect([
                'SERCs' => $comp->getSERCs->map(fn ($serc) => ['key' => 'se:' . $serc->id, 'name' => $serc->getName()]),
                'Speeds' => $comp->getSpeedEvents->map(fn ($speed) => ['key' => 'sp:' . $speed->id, 'name' => $speed->getName()]),
            ])
                ->filter(fn ($events) => $events->isNotEmpty())
                ->map(fn ($events, $title) => [
                    'title' => $title,
                    'events' => $events->map(fn ($event) => [...$event, 'url' => route('live.dqs.event', [$event['key'], ...$request->only('comp')])]),
                ])
                ->values(),
        ]);
    }

    public function eventDqs(Request $request, string $event)
    {
        return $this->page($request, 'Live/EventDqs', function (Competition $comp) use ($event, $request) {
            $realEvent = str_starts_with($event, 'sp')
                ? CompetitionSpeedEvent::where('id', substr($event, 3))->first()
                : SERC::where('id', substr($event, 3))->first();

            abort_unless($realEvent?->competition == $comp->id, 404);

            // DQs come from both the old JudgeDQSubmission flow and the newer ViolationSubmission one
            $dqs = $this->judgeDqRows($comp, $realEvent)->concat($this->violationRows($comp, $realEvent))->values();

            return [
                'event' => ['name' => $realEvent->getName()],
                'dqs' => $dqs->map(fn ($dq, $i) => ['id' => $i, ...$dq]),
                'back' => route('live.dqs', $request->only('comp')),
            ];
        });
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
