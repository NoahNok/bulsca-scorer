<?php

namespace App\Http\Controllers\Manage;

use App\Helpers\ManagedCompetitions;
use App\Helpers\OrganisationHeader;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organisation\Championship\CreateChampionshipRequest;
use App\Models\Championship;
use App\Models\Competition;
use App\Models\Organisation\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

/**
 * An organisation's Championships (Svelte, its Organisers only): the list, making one, and a
 * Championship's page, where its competitions are added with their type and removed, and it's deleted.
 */
class ChampionshipController extends Controller
{
    public function index(Organisation $organisation)
    {
        return Inertia::render('Manage/Organisation/Championships', [
            'organisation' => OrganisationHeader::for($organisation),
            'championships' => $organisation->championships()->withCount('competitions')->get()
                ->map(fn (Championship $championship) => [
                    'id' => $championship->id,
                    'name' => $championship->name,
                    'start' => $championship->start_date->toDateString(),
                    'end' => $championship->end_date->toDateString(),
                    'competitions' => $championship->competitions_count,
                    'url' => route('manage.organisations.championships.show', [$organisation, $championship]),
                ]),
        ]);
    }

    public function store(CreateChampionshipRequest $request, Organisation $organisation)
    {
        $championship = $organisation->championships()->create($request->validated());

        return redirect()->route('manage.organisations.championships.show', [$organisation, $championship])
            ->with('success', 'Championship created');
    }

    public function show(Organisation $organisation, Championship $championship)
    {
        $this->ensureOwns($organisation, $championship);

        return Inertia::render('Manage/Organisation/Championship', [
            'organisation' => OrganisationHeader::for($organisation),
            'championship' => [
                'id' => $championship->id,
                'name' => $championship->name,
                'start' => $championship->start_date->toDateString(),
                'end' => $championship->end_date->toDateString(),
            ],
            'competitions' => $championship->competitions->map(fn (Competition $comp) => [
                ...ManagedCompetitions::row($comp),
                'type' => $comp->type,
            ]),
            'available' => $this->available($organisation)->map(fn (Competition $comp) => [
                'id' => $comp->id,
                'name' => $comp->name,
                'when' => $comp->when->toDateString(),
            ]),
            'types' => collect($this->types())->mapWithKeys(fn ($type) => [$type => ucfirst(strtolower($type))]),
        ]);
    }

    /**
     * Deletes it, its competitions going back to standalone. Their foreign key cascades, so they're let
     * go first, or deleting it would delete them too.
     */
    public function destroy(Organisation $organisation, Championship $championship)
    {
        $this->ensureOwns($organisation, $championship);

        DB::transaction(function () use ($championship) {
            Competition::where('championship_id', $championship->id)->update(['championship_id' => null, 'type' => 'STANDALONE']);
            $championship->delete();
        });

        return redirect()->route('manage.organisations.championships', $organisation)->with('success', "{$championship->name} deleted");
    }

    public function addCompetition(Request $request, Organisation $organisation, Championship $championship)
    {
        $this->ensureOwns($organisation, $championship);

        $validated = $request->validate([
            'competition_id' => ['required', 'integer', Rule::in($this->available($organisation)->pluck('id'))],
            'type' => ['required', Rule::in($this->types())],
        ], [
            'competition_id.in' => "Pick one of {$organisation->name}'s competitions that isn't in a championship yet.",
        ]);

        $comp = Competition::findOrFail($validated['competition_id']);
        $comp->championship()->associate($championship);
        $comp->type = $validated['type'];
        $comp->save();

        return back()->with('success', "{$comp->name} added");
    }

    public function removeCompetition(Organisation $organisation, Championship $championship, Competition $competition)
    {
        $this->ensureOwns($organisation, $championship);
        abort_unless($competition->championship_id === $championship->id, 404);

        $competition->championship()->dissociate();
        $competition->type = 'STANDALONE';
        $competition->save();

        return back()->with('success', "{$competition->name} removed");
    }

    private function ensureOwns(Organisation $organisation, Championship $championship): void
    {
        abort_unless($championship->organisation_id === $organisation->id, 404);
    }

    /**
     * The organisation's competitions that aren't in a championship, soonest first.
     */
    private function available(Organisation $organisation)
    {
        return Competition::where('organisation', $organisation->id)->whereNull('championship_id')->orderBy('when')->get();
    }

    /** A competition's types in a championship: all but STANDALONE */
    private function types(): array
    {
        return array_values(array_diff(Competition::$types, ['STANDALONE']));
    }
}
