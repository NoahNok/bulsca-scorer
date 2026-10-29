<?php

namespace App\Http\Controllers\Manage;

use App\DigitalJudge\DigitalJudge;
use App\Helpers\CompetitionHeader;
use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\MasterSchema;
use App\Models\ResultSchema;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

/**
 * A Master sheet's page and its New/Edit form (Svelte, in CompetitionLayout). A Master sheet combines
 * Results sheets into one ranking of Clubs, and is a MasterSchema in code.
 */
class MasterSheetsController extends Controller
{
    private const SUM_OVER = ['position' => 'Position', 'totalPoints' => 'Points'];

    public function show(Competition $comp, MasterSchema $master)
    {
        $sheets = $master->getSheets();
        $excluded = $master->schema['exclude'] ?? [];

        return Inertia::render('Manage/Competition/MasterSheet', [
            'competition' => CompetitionHeader::for($comp),
            'master' => [
                'id' => $master->id,
                'name' => $master->name,
                'viewable' => (bool) $master->viewable,
                'sumOver' => self::SUM_OVER[$master->schema['sum_over'] ?? 'position'] ?? 'Position',
                'defaultValue' => $master->schema['default_value'] ?? 0,
                'sheets' => $sheets->map(fn (ResultSchema $sheet) => ['id' => $sheet->id, 'name' => $sheet->name])->values(),
                'excluded' => $comp->getClubs->whereIn('id', $excluded)->pluck('name')->values(),
            ],
            'results' => $master->getResults()->map(fn ($result) => [
                'key' => $result->entity->getMorphClass() . ':' . $result->entity->id,
                'name' => $result->entity->getName($comp),
                'sheetResults' => $sheets->map(fn (ResultSchema $sheet) => round($result->sheetResults[$sheet->id] ?? 0, 1))->values(),
                'total' => round($result->total, 1),
                'position' => $result->position,
            ])->values(),
            ...ResultSheetsController::confirmation($comp),
        ]);
    }

    public function create(Competition $comp)
    {
        if (!DigitalJudge::canGenerateCompetitionResults($comp)) {
            return redirect()->route('manage.competitions.results', $comp);
        }

        return $this->form($comp, null);
    }

    public function edit(Competition $comp, MasterSchema $master)
    {
        if (!DigitalJudge::canGenerateCompetitionResults($comp)) {
            return redirect()->route('manage.competitions.results.master.show', [$comp, $master]);
        }

        return $this->form($comp, $master);
    }

    private function form(Competition $comp, ?MasterSchema $master)
    {
        return Inertia::render('Manage/Competition/MasterSheetForm', [
            'competition' => CompetitionHeader::for($comp),
            'master' => $master ? [
                'id' => $master->id,
                'name' => $master->name,
                'sum_over' => $master->schema['sum_over'] ?? 'position',
                'default_value' => $master->schema['default_value'] ?? 0,
                'sheets' => array_column($master->schema['sheets'] ?? [], 'id'),
                'exclude' => $master->schema['exclude'] ?? [],
            ] : null,
            'sheets' => $comp->getResultSchemas()->orderBy('id')->get(['id', 'name']),
            'clubs' => $comp->getClubs()->orderBy('name')->get(['id', 'name']),
            'published' => (bool) $comp->public_results,
        ]);
    }

    public function store(Competition $comp, Request $request)
    {
        $master = new MasterSchema();
        $master->competition = $comp->id;
        $master->viewable = true;
        $this->fill($master, $this->validated($comp, $request));

        return redirect()->route('manage.competitions.results.master.show', [$comp, $master])->with('success', 'Master sheet made');
    }

    public function update(Competition $comp, MasterSchema $master, Request $request)
    {
        $this->fill($master, $this->validated($comp, $request));

        return redirect()->route('manage.competitions.results.master.show', [$comp, $master])->with('success', 'Master sheet saved');
    }

    private function validated(Competition $comp, Request $request): array
    {
        if (!DigitalJudge::canGenerateCompetitionResults($comp)) {
            throw ValidationException::withMessages(['sheet' => 'Master sheets can\'t be saved until every Digitally Judged event is confirmed.']);
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sum_over' => ['required', Rule::in(array_keys(self::SUM_OVER))],
            'default_value' => ['required', 'numeric'],
            'sheets' => ['required', 'array', 'min:1'],
            'sheets.*' => ['integer', Rule::exists('result_schemas', 'id')->where('competition', $comp->id)],
            'exclude' => ['present', 'array'],
            'exclude.*' => ['integer', Rule::exists('clubs', 'id')->where('competition', $comp->id)],
        ], [
            'sheets.required' => 'Pick at least one Results sheet.',
            'sheets.*.exists' => 'That Results sheet isn\'t in this competition.',
        ]);
    }

    /**
     * Saves a Master sheet. It's always by Club, and every sheet counts once: the weights it stores were
     * never used, so they're kept at 1.
     */
    private function fill(MasterSchema $master, array $data): void
    {
        $master->name = $data['name'];
        $master->schema = [
            'sum_over' => $data['sum_over'],
            'default_value' => $data['default_value'] * 1,
            'sheets' => collect($data['sheets'])->map(fn ($id) => ['id' => (int) $id, 'weight' => 1])->values()->all(),
            'group_on' => 'club',
            'exclude' => collect($data['exclude'])->map(fn ($id) => (int) $id)->values()->all(),
        ];
        $master->save();
    }

    public function visibility(Competition $comp, MasterSchema $master, Request $request)
    {
        $master->viewable = $request->validate(['viewable' => ['required', 'boolean']])['viewable'];
        $master->save();

        return back()->with('success', $master->viewable ? 'Shown in results' : 'Hidden from results');
    }

    /**
     * Deleting needs the Master sheet's name typed out
     */
    public function destroy(Competition $comp, MasterSchema $master, Request $request)
    {
        if ($request->validate(['name' => ['required', 'string']])['name'] != $master->name) {
            throw ValidationException::withMessages(['name' => "That isn't the Master sheet's name."]);
        }

        $master->delete();

        return redirect()->route('manage.competitions.results', $comp)->with('success', "{$master->name} deleted");
    }
}
