<?php

namespace App\Http\Controllers\Manage;

use App\Helpers\CompetitionHeader;
use App\Http\Controllers\Controller;
use App\Http\Requests\Entries\SaveEntryRequest;
use App\Models\AbstractClasses\Entity;
use App\Models\Club;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\Competitor;
use App\Models\League;
use App\Models\SpeedResult;
use App\Services\CompetitionEntries;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

/**
 * A competition's Entries & Leagues page (Svelte, in CompetitionLayout): its clubs, teams and competitors
 * as one tree, and its leagues. Each entry is added, changed or removed on its own from a dialog, so every
 * write here is one entry, answered with back(). Leagues are saved by LeagueController.
 */
class CompetitionEntriesController extends Controller
{
    public function index(Competition $comp)
    {
        $seedable = collect($comp->getSeedableEvents())->pluck('id');

        // An entry as the page lists it: seed times keyed by speed event id, only for events that take them
        $entry = fn (Entity $entity) => [
            'id' => $entity->id,
            'name' => $entity instanceof CompetitionTeam ? $entity->team : $entity->name,
            'leagues' => $entity->leagues->pluck('id'),
            'seeds' => (object) $entity->getSeeds
                ->filter(fn ($seed) => $seedable->contains($seed->speed_event))
                ->mapWithKeys(fn ($seed) => [$seed->speed_event => SpeedResult::prettyTime($seed->seed)])
                ->all(),
        ];

        $team = fn (CompetitionTeam $team) => $entry($team) + [
            'competitors' => $team->getCompetitors->map($entry)->values(),
        ];

        $byName = fn ($query) => $query->orderBy('name');
        $teamRelations = ['leagues', 'getSeeds', 'getCompetitors' => $byName, 'getCompetitors.leagues', 'getCompetitors.getSeeds'];

        return Inertia::render('Manage/Competition/Entries', [
            'competition' => CompetitionHeader::for($comp),
            'leagues' => $comp->getLeagues()->withCount('teams', 'competitors')->orderBy('name')->get()
                ->map(fn (League $league) => [
                    'id' => $league->id,
                    'name' => $league->name,
                    'teams' => $league->teams_count,
                    'competitors' => $league->competitors_count,
                ]),
            'clubs' => $comp->getClubs()->orderBy('name')
                ->with([
                    'getTeams' => fn ($query) => $query->orderBy('team'),
                    'getTeams.leagues', 'getTeams.getSeeds',
                    'getTeams.getCompetitors' => $byName, 'getTeams.getCompetitors.leagues', 'getTeams.getCompetitors.getSeeds',
                ])
                ->get()
                ->map(fn (Club $club) => [
                    'id' => $club->id,
                    'name' => $club->name,
                    'teams' => $club->getTeams->map($team)->values(),
                ]),
            'teams' => $comp->getCompetitionTeams()->whereNull('club')->orderBy('team')->with($teamRelations)->get()->map($team),
            'competitors' => $comp->getCompetitors()->whereNull('team')->orderBy('name')->with('leagues', 'getSeeds')->get()->map($entry),
            // Null without access to Heats & Draws, where they regenerate after adding entries
            'heatsUrl' => Gate::allows('access', [$comp, 'heats_and_draws']) ? route('comps.heats_and_draws', $comp) : null,
            // Null when the competition doesn't use seed times
            'seeds' => $comp->use_seeds ? [
                'perEvent' => (bool) $comp->seed_per_event,
                'events' => collect($comp->getSeedableEvents())->values(),
            ] : null,
        ]);
    }

    public function storeClub(Competition $comp, SaveEntryRequest $request)
    {
        $entries = new CompetitionEntries($comp);

        DB::transaction(function () use ($comp, $request, $entries) {
            $club = Club::create(['competition' => $comp->id, 'name' => $request->validated('name')]);
            $entries->created($club);
        });
        $entries->changed();

        return back()->with('success', 'Club added');
    }

    public function updateClub(Competition $comp, Club $club, SaveEntryRequest $request)
    {
        $club->update(['name' => $request->validated('name')]);
        (new CompetitionEntries($comp))->changed();

        return back()->with('success', 'Club saved');
    }

    public function destroyClub(Competition $comp, Club $club)
    {
        // One by one, so each entry's results go with it
        DB::transaction(function () use ($club) {
            foreach ($club->getTeams as $team) {
                $team->getCompetitors->each->delete();
                $team->delete();
            }
            $club->delete();
        });
        (new CompetitionEntries($comp))->changed();

        return back()->with('success', 'Club removed');
    }

    public function storeTeam(Competition $comp, SaveEntryRequest $request)
    {
        $entries = new CompetitionEntries($comp);

        DB::transaction(function () use ($comp, $request, $entries) {
            $team = CompetitionTeam::create(['competition' => $comp->id] + $this->teamAttributes($request));
            $this->saveEntry($entries, $team, $request);
            $entries->created($team);
        });
        $entries->changed();

        return back()->with('success', 'Team added');
    }

    public function updateTeam(Competition $comp, CompetitionTeam $team, SaveEntryRequest $request)
    {
        $entries = new CompetitionEntries($comp);

        DB::transaction(function () use ($team, $request, $entries) {
            $team->update($this->teamAttributes($request));
            $this->saveEntry($entries, $team, $request);
        });
        $entries->changed();

        return back()->with('success', 'Team saved');
    }

    public function destroyTeam(Competition $comp, CompetitionTeam $team)
    {
        DB::transaction(function () use ($team) {
            $team->getCompetitors->each->delete();
            $team->delete();
        });
        (new CompetitionEntries($comp))->changed();

        return back()->with('success', 'Team removed');
    }

    public function storeCompetitor(Competition $comp, SaveEntryRequest $request)
    {
        $entries = new CompetitionEntries($comp);

        DB::transaction(function () use ($comp, $request, $entries) {
            $competitor = Competitor::create(['competition' => $comp->id] + $this->competitorAttributes($request));
            $this->saveEntry($entries, $competitor, $request);
            $entries->created($competitor);
        });
        $entries->changed();

        return back()->with('success', 'Competitor added');
    }

    public function updateCompetitor(Competition $comp, Competitor $competitor, SaveEntryRequest $request)
    {
        $entries = new CompetitionEntries($comp);

        DB::transaction(function () use ($competitor, $request, $entries) {
            $competitor->update($this->competitorAttributes($request));
            $this->saveEntry($entries, $competitor, $request);
        });
        $entries->changed();

        return back()->with('success', 'Competitor saved');
    }

    public function destroyCompetitor(Competition $comp, Competitor $competitor)
    {
        $competitor->delete();
        (new CompetitionEntries($comp))->changed();

        return back()->with('success', 'Competitor removed');
    }

    private function teamAttributes(SaveEntryRequest $request): array
    {
        return ['team' => $request->validated('name'), 'club' => $request->validated('club')];
    }

    private function competitorAttributes(SaveEntryRequest $request): array
    {
        return ['name' => $request->validated('name'), 'team' => $request->validated('team')];
    }

    /**
     * A team's or competitor's leagues and seed times.
     */
    private function saveEntry(CompetitionEntries $entries, Entity $entity, SaveEntryRequest $request): void
    {
        $entity->leagues()->sync($request->validated('leagues') ?? []);
        $entries->saveSeeds($entity, $request->validated('seeds') ?? []);
    }
}
