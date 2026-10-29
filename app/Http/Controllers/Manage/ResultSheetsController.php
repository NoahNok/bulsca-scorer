<?php

namespace App\Http\Controllers\Manage;

use App\DigitalJudge\DigitalJudge;
use App\Helpers\CompetitionHeader;
use App\Helpers\RouteHelpers;
use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\MasterSchema;
use App\Exceptions\ScoringException;
use App\Models\ResultSchema;
use App\Models\ResultSchemaEvent;
use App\Services\ResultSheetFormulas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

/**
 * A competition's Results (Svelte, in CompetitionLayout): the overview of its Results sheets and Master
 * sheets with publishing and finalising, and each Results sheet's page and its New/Edit form. A Results
 * sheet is a ResultSchema in code.
 */
class ResultSheetsController extends Controller
{
    public function index(Competition $comp)
    {
        $leagues = $comp->getLeagues()->pluck('name', 'id');

        return Inertia::render('Manage/Competition/Results', [
            'competition' => CompetitionHeader::for($comp),
            'sheets' => $comp->getResultSchemas()->orderBy('id')->get()->map(fn (ResultSchema $sheet) => [
                'id' => $sheet->id,
                'name' => $sheet->name,
                'league' => $leagues[$sheet->schema['league'] ?? null] ?? null,
                'viewable' => (bool) $sheet->viewable,
            ]),
            'masters' => $comp->getMasterSchemas()->orderBy('id')->get()->map(fn (MasterSchema $master) => [
                'id' => $master->id,
                'name' => $master->name,
                'viewable' => (bool) $master->viewable,
            ]),
            'published' => (bool) $comp->public_results,
            'provisional' => (bool) $comp->areResultsProvisional(),
            'private' => !$comp->show_competition,
            'publicUrl' => RouteHelpers::mainRoute('landing.competition.results', $comp->resultsSlug()),
            'printAllUrl' => route('comps.results.print-all', $comp),
            ...self::confirmation($comp),
        ]);
    }

    public function create(Competition $comp)
    {
        if (!DigitalJudge::canGenerateCompetitionResults($comp)) {
            return redirect()->route('manage.competitions.results', $comp);
        }

        return $this->form($comp, null);
    }

    public function edit(Competition $comp, ResultSchema $sheet)
    {
        if (!DigitalJudge::canGenerateCompetitionResults($comp)) {
            return redirect()->route('manage.competitions.results.show', [$comp, $sheet]);
        }

        return $this->form($comp, $sheet);
    }

    /**
     * New and Edit Results sheet, one page: every Event it can include (with what it's set to on the
     * sheet, or the default weights of 1 for a speed event and 2 for a SERC), the Leagues, and the
     * built-in and Organisation Formulas to pick from
     */
    private function form(Competition $comp, ?ResultSchema $sheet)
    {
        $rows = $sheet?->getEvents ?? collect();
        $events = $comp->getSpeedEvents->map(fn ($event) => [$event, 1])
            ->concat($comp->getSERCs->map(fn ($serc) => [$serc, 2]))
            ->map(function (array $pair) use ($rows) {
                [$event, $weight] = $pair;
                $type = ResultSheetFormulas::typeOf($event);
                $row = $rows->first(fn (ResultSchemaEvent $row) => ResultSheetFormulas::typeOf($row->getActualEvent) === $type && $row->event_id == $event->id);

                return [
                    'type' => $type,
                    'id' => $event->id,
                    'name' => $event->getName(),
                    'weight' => $row ? (float) $row->weight : $weight,
                ];
            });

        return Inertia::render('Manage/Competition/ResultSheetForm', [
            'competition' => CompetitionHeader::for($comp),
            'sheet' => $sheet ? [
                'id' => $sheet->id,
                'name' => $sheet->name,
                'league' => $sheet->schema['league'] ?? null,
                'events' => $rows->filter(fn (ResultSchemaEvent $row) => $row->getActualEvent)->map(fn (ResultSchemaEvent $row) => [
                    'type' => ResultSheetFormulas::typeOf($row->getActualEvent),
                    'id' => $row->event_id,
                ])->values(),
                'equation' => $sheet->schema['equation'] ?? 'item.points',
                'global_variables' => $sheet->schema['global_variables'] ?? [],
                'group_on' => $sheet->schema['group_on'] ?? 'team',
                'rank_higher' => (bool) ($sheet->schema['rank_higher'] ?? true),
                'ignore_dq' => (bool) ($sheet->schema['ignore_dq'] ?? false),
                'break_ties' => $this->tieBreaksOf($sheet),
                'builtIn' => ResultSheetFormulas::builtInFor($sheet->schema),
            ] : null,
            'events' => $events->values(),
            'leagues' => $comp->getLeagues()->orderBy('name')->get(['id', 'name']),
            'builtIns' => collect(ResultSheetFormulas::BUILT_IN)->map(fn (array $formula, string $key) => [
                'key' => $key,
                'label' => $formula[0],
                'equation' => $formula[1],
                'rankHigher' => $formula[2],
            ])->values(),
            'organisationFormulas' => ($comp->getOrganisation?->resultSchemas ?? collect())->map(fn ($formula) => [
                'id' => $formula->id,
                'name' => $formula->name,
                'equation' => $formula->schema['equation'] ?? '',
                'global_variables' => $formula->schema['global_variables'] ?? [],
            ])->values(),
            'organisation' => $comp->getOrganisation?->name,
            'published' => (bool) $comp->public_results,
            ...self::confirmation($comp),
        ]);
    }

    /**
     * A sheet's tie-breaks as `{type, id}`, the old bare ids read as the first Event on it with that id
     */
    private function tieBreaksOf(ResultSchema $sheet): array
    {
        $events = $sheet->getEvents->map(fn (ResultSchemaEvent $row) => $row->getActualEvent)->filter();

        return collect($sheet->schema['break_ties'] ?? [])
            ->map(fn ($tieBreak) => $events->first(fn ($event) => ResultSheetFormulas::tieBreakIs($tieBreak, $event)))
            ->filter()
            ->map(fn ($event) => ['type' => ResultSheetFormulas::typeOf($event), 'id' => $event->id])
            ->values()
            ->all();
    }

    /**
     * Makes a sheet, or one for each League with Repeat for all Leagues, opening it (or the overview
     * for several)
     */
    public function store(Competition $comp, Request $request)
    {
        $data = $this->validated($comp, $request);
        $leagues = $request->boolean('repeat_for_all_leagues') ? $comp->getLeagues()->orderBy('id')->get()->toBase() : collect();
        if ($leagues->isEmpty()) {
            $leagues = collect([$comp->getLeagues()->find($data['league'])]);
        }

        $sheets = $leagues->map(function ($league) use ($comp, $data) {
            $sheet = new ResultSchema();
            $sheet->competition = $comp->id;

            return $this->fill($sheet, [...$data, 'league' => $league?->id], $data['name'] ?: ($league?->name ?? 'Overall'));
        });

        if ($sheets->count() == 1) {
            return redirect()->route('manage.competitions.results.show', [$comp, $sheets->first()])->with('success', 'Results sheet made');
        }

        return redirect()->route('manage.competitions.results', $comp)->with('success', "{$sheets->count()} Results sheets made");
    }

    public function update(Competition $comp, ResultSchema $sheet, Request $request)
    {
        $data = $this->validated($comp, $request);
        $league = $comp->getLeagues()->find($data['league']);

        $this->fill($sheet, $data, $data['name'] ?: ($league?->name ?? 'Overall'));
        $sheet->forgetResults();

        return redirect()->route('manage.competitions.results.show', [$comp, $sheet])->with('success', 'Results sheet saved');
    }

    private function validated(Competition $comp, Request $request): array
    {
        if (!DigitalJudge::canGenerateCompetitionResults($comp)) {
            throw ValidationException::withMessages(['sheet' => 'Results sheets can\'t be saved until every Digitally Judged event is confirmed.']);
        }

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'league' => ['nullable', 'integer', Rule::exists('leagues', 'id')->where('competition', $comp->id)],
            'events' => ['required', 'array', 'min:1'],
            'events.*.type' => ['required', 'in:speed,serc'],
            'events.*.id' => ['required', 'integer'],
            'events.*.weight' => ['required', 'numeric', 'min:0'],
            'equation' => ['required', 'string'],
            'global_variables' => ['present', 'array'],
            'global_variables.*.name' => ['required', 'string'],
            'global_variables.*.expression' => ['required', 'string'],
            'global_variables.*.order' => ['nullable', 'integer'],
            'group_on' => ['required', 'in:club,team,competitor'],
            'rank_higher' => ['required', 'boolean'],
            'ignore_dq' => ['required', 'boolean'],
            'break_ties' => ['present', 'array'],
            'break_ties.*.type' => ['required', 'in:speed,serc'],
            'break_ties.*.id' => ['required', 'integer'],
        ], [
            'events.required' => 'Pick at least one event.',
        ]);

        foreach ($data['events'] as $index => $event) {
            if (!ResultSheetFormulas::findEvent($comp, $event['type'], $event['id'])) {
                throw ValidationException::withMessages(["events.$index" => 'That event isn\'t in this competition.']);
            }
        }

        $onSheet = collect($data['events'])->map(fn ($event) => $event['type'] . ':' . $event['id']);
        foreach ($data['break_ties'] as $tieBreak) {
            if (!$onSheet->contains($tieBreak['type'] . ':' . $tieBreak['id'])) {
                throw ValidationException::withMessages(['break_ties' => 'Tie-breaks can only use events on the sheet.']);
            }
        }

        return $data;
    }

    /**
     * Saves a sheet's settings and replaces its Events
     */
    private function fill(ResultSchema $sheet, array $data, string $name): ResultSchema
    {
        $sheet->name = $name;
        $sheet->schema = [
            'equation' => $data['equation'],
            'global_variables' => collect($data['global_variables'])->values()->map(fn ($variable, $index) => [
                'name' => $variable['name'],
                'expression' => $variable['expression'],
                'order' => $variable['order'] ?? $index + 1,
            ])->all(),
            'league' => $data['league'],
            'ignore_dq' => (bool) $data['ignore_dq'],
            'group_on' => $data['group_on'],
            'rank_higher' => (bool) $data['rank_higher'],
            'break_ties' => collect($data['break_ties'])->map(fn ($tieBreak) => ['type' => $tieBreak['type'], 'id' => (int) $tieBreak['id']])->all(),
        ];

        DB::transaction(function () use ($sheet, $data) {
            $sheet->save();
            $sheet->getEvents()->delete();

            foreach ($data['events'] as $event) {
                $row = new ResultSchemaEvent();
                $row->schema = $sheet->id;
                $row->event_id = $event['id'];
                $row->event_type = $event['type'] === 'serc' ? '\App\Models\SERC' : '\App\Models\CompetitionSpeedEvent';
                $row->weight = $event['weight'];
                $row->save();
            }
        });

        return $sheet->unsetRelation('getEvents');
    }

    public function show(Competition $comp, ResultSchema $sheet, Request $request)
    {
        $isAdmin = (bool) Auth::user()->isAdmin();
        $error = null;

        try {
            $results = $sheet->getResults($isAdmin && $request->boolean('refresh_cache'));
        } catch (ScoringException $e) {
            Log::error('ScoringException on a Results sheet: ' . $e->getMessage(), $e->getContext());
            $error = ['message' => $e->getMessage(), 'context' => $e->getContext()];
            $results = collect();
        }

        $events = $sheet->getEvents->filter(fn (ResultSchemaEvent $row) => $row->getActualEvent);

        return Inertia::render('Manage/Competition/ResultSheet', [
            'competition' => CompetitionHeader::for($comp),
            'sheet' => [
                'id' => $sheet->id,
                'name' => $sheet->name,
                'viewable' => (bool) $sheet->viewable,
                'league' => $comp->getLeagues()->find($sheet->schema['league'] ?? null)?->name,
                'formula' => ResultSheetFormulas::labelFor($sheet->schema),
                'events' => $events->map(fn (ResultSchemaEvent $row) => [
                    'name' => $row->getActualEvent->getName(),
                    'weight' => (float) $row->weight,
                ])->values(),
                'breakTies' => collect($sheet->schema['break_ties'] ?? [])
                    ->map(fn ($tieBreak) => $events->first(fn (ResultSchemaEvent $row) => ResultSheetFormulas::tieBreakIs($tieBreak, $row->getActualEvent))?->getActualEvent->getName())
                    ->filter()
                    ->values(),
            ],
            'results' => $results->map(fn ($result) => [
                'key' => $result->entity->getMorphClass() . ':' . $result->entity->id,
                'name' => $result->entity->getName($comp),
                'points' => round($result->totalPoints, 1),
                'position' => $result->position,
            ])->values(),
            'error' => $error,
            'isAdmin' => $isAdmin,
            // The Master sheets built on it, which deleting it takes it out of
            'masters' => $this->mastersUsing($comp, $sheet)->pluck('name'),
            'printPlacesUrl' => route('comps.results.view-schema-print-basic', $sheet),
            'printDetailedUrl' => route('comps.results.view-schema-print', $sheet),
            ...self::confirmation($comp),
        ]);
    }

    public function visibility(Competition $comp, ResultSchema $sheet, Request $request)
    {
        $sheet->viewable = $request->validate(['viewable' => ['required', 'boolean']])['viewable'];
        $sheet->save();

        return back()->with('success', $sheet->viewable ? 'Shown in results' : 'Hidden from results');
    }

    /**
     * Deleting needs the sheet's name typed out, and takes it out of any Master sheet built on it
     */
    public function destroy(Competition $comp, ResultSchema $sheet, Request $request)
    {
        if ($request->validate(['name' => ['required', 'string']])['name'] != $sheet->name) {
            throw ValidationException::withMessages(['name' => "That isn't the sheet's name."]);
        }

        DB::transaction(function () use ($comp, $sheet) {
            foreach ($this->mastersUsing($comp, $sheet) as $master) {
                $master->schema = [
                    ...$master->schema,
                    'sheets' => collect($master->schema['sheets'])->reject(fn ($row) => $row['id'] == $sheet->id)->values()->all(),
                ];
                $master->save();
            }

            $sheet->forgetResults();
            $sheet->getEvents()->delete();
            $sheet->delete();
        });

        return redirect()->route('manage.competitions.results', $comp)->with('success', "{$sheet->name} deleted");
    }

    public function publish(Competition $comp, Request $request)
    {
        $comp->public_results = $request->validate(['published' => ['required', 'boolean']])['published'];
        $comp->save();

        return back()->with('success', $comp->public_results ? 'Results published' : 'Results unpublished');
    }

    public function finalise(Competition $comp, Request $request)
    {
        $comp->results_provisional = $request->validate(['provisional' => ['required', 'boolean']])['provisional'];
        $comp->save();

        return back()->with('success', $comp->results_provisional ? 'Results are provisional again' : 'Results finalised');
    }

    /**
     * The old /comp/results/view-schema/{schema} url, which had no competition in it
     */
    public function old(ResultSchema $sheet)
    {
        $comp = $sheet->getCompetition;
        Gate::authorize('access', [$comp, 'results']);

        return redirect()->route('manage.competitions.results.show', [$comp, $sheet], 301);
    }

    private function mastersUsing(Competition $comp, ResultSchema $sheet)
    {
        return $comp->getMasterSchemas->filter(fn (MasterSchema $master) => collect($master->schema['sheets'] ?? [])->contains('id', $sheet->id))->values();
    }

    /**
     * Whether sheets can be made and saved: not while a Digitally Judged Event waits for the Referee to
     * confirm it. `unconfirmed` lists those Events, linked to their pages. Master sheets send it too.
     */
    public static function confirmation(Competition $comp): array
    {
        if (DigitalJudge::canGenerateCompetitionResults($comp)) {
            return ['canCreate' => true, 'unconfirmed' => []];
        }

        $unconfirmed = DigitalJudge::getSpeedsRequiringConfirmation($comp)->toBase()
            ->map(fn ($event) => ['name' => $event->getName(), 'url' => route('manage.competitions.events.speeds.show', [$comp, $event])])
            ->merge(DigitalJudge::getSercsRequiringConfirmation($comp)
                ->map(fn ($serc) => ['name' => $serc->getName(), 'url' => route('manage.competitions.events.sercs.show', [$comp, $serc])]));

        return ['canCreate' => false, 'unconfirmed' => $unconfirmed->values()->all()];
    }
}
