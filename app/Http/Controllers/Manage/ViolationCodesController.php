<?php

namespace App\Http\Controllers\Manage;

use App\Helpers\OrganisationHeader;
use App\Http\Controllers\Controller;
use App\Models\DQCode;
use App\Models\EventCode;
use App\Models\Organisation\Organisation;
use App\Models\PenaltyCode;
use App\Models\SpeedEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

/**
 * An organisation's Violations (Svelte, its Organisers only): its DQ and penalty codes, each with the kind
 * of call it is for every speed event and SERC (an EventCode), or none there. Officials pick from these.
 */
class ViolationCodesController extends Controller
{
    /** The kinds of call a Violation code can be for an event, as event_codes.type takes them */
    private const KINDS = ['GENERIC', 'LANE', 'TURN', 'CHANGEOVER', 'CROSSLINE', 'BACKLINE', 'OOF', 'STARTER', 'PICKUP'];

    public function index(Organisation $organisation)
    {
        $events = $this->events();

        $codes = fn ($query) => $query->with('eventCodes')->reorder('code')->get()->map(fn (Model $code) => [
            'id' => $code->id,
            'code' => (int) $code->code,
            'description' => $code->description,
            'events' => collect($events)->mapWithKeys(fn (string $event) => [
                $event => $code->eventCodes->firstWhere('event', $event)?->type,
            ]),
        ]);

        return Inertia::render('Manage/Organisation/Violations', [
            'organisation' => OrganisationHeader::for($organisation),
            'dqs' => $codes($organisation->disqualificationCodes()),
            'penalties' => $codes($organisation->penaltyCodes()),
            'events' => $events,
            'kinds' => collect(self::KINDS)->map(fn (string $kind) => [
                'value' => $kind,
                'label' => $kind === 'OOF' ? 'OOF' : ucfirst(strtolower($kind)),
            ]),
        ]);
    }

    public function store(Request $request, Organisation $organisation)
    {
        $type = $request->validate(['type' => ['required', Rule::in(['dq', 'pen'])]])['type'];
        $class = $type === 'dq' ? DQCode::class : PenaltyCode::class;

        $validated = $request->validate([
            'code' => [
                'required', 'integer', 'min:1',
                Rule::unique((new $class())->getTable(), 'code')->where('organisation', $organisation->id),
            ],
            ...$this->rules(),
        ], [
            'code.min' => 'The code must be a positive number.',
            'code.unique' => ($type === 'dq' ? 'DQ' : 'P') . "{$request->input('code')} already exists.",
        ]);

        $code = new $class();
        $code->code = $validated['code'];
        $code->organisation = $organisation->id;

        $this->save($code, $validated);

        return back()->with('success', "{$code} added");
    }

    public function update(Request $request, Organisation $organisation, string $type, int $code)
    {
        $code = $this->find($organisation, $type, $code);

        $this->save($code, $request->validate($this->rules()));

        return back()->with('success', "{$code} saved");
    }

    public function destroy(Organisation $organisation, string $type, int $code)
    {
        $code = $this->find($organisation, $type, $code);

        DB::transaction(function () use ($code) {
            $code->eventCodes()->delete();
            $code->delete();
        });

        return back()->with('success', "{$code} deleted");
    }

    /** What both a new code and a change take: its description, and its call for each event (null for none) */
    private function rules(): array
    {
        return [
            'description' => 'required|string|max:255',
            'events' => 'present|array',
            'events.*' => ['nullable', Rule::in(self::KINDS)],
        ];
    }

    /**
     * Saves its description, then its calls: one EventCode per event it has a kind for, none for the rest.
     * Names that aren't an event are ignored.
     */
    private function save(DQCode|PenaltyCode $code, array $validated): void
    {
        DB::transaction(function () use ($code, $validated) {
            $code->description = $validated['description'];
            $code->save();

            $existing = $code->eventCodes()->get()->keyBy('event');

            foreach ($this->events() as $event) {
                $kind = $validated['events'][$event] ?? null;
                $eventCode = $existing->get($event);

                if ($kind === null) {
                    $eventCode?->delete();
                    continue;
                }

                $eventCode ??= new EventCode();
                $eventCode->event = $event;
                $eventCode->type = $kind;
                $eventCode->pendq()->associate($code);
                $eventCode->save();
            }
        });
    }

    private function find(Organisation $organisation, string $type, int $id): DQCode|PenaltyCode
    {
        $query = $type === 'dq' ? $organisation->disqualificationCodes() : $organisation->penaltyCodes();

        return $query->findOrFail($id);
    }

    /** Every speed event's name, then SERC: what a code has a call for */
    private function events(): array
    {
        return [...SpeedEvent::orderBy('id')->pluck('name'), 'SERC'];
    }
}
