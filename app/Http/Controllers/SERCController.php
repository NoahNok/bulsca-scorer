<?php

namespace App\Http\Controllers;

use App\Helpers\ClassHelpers;
use App\Http\Requests\Event\UpdateScoringSettings;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\Competitor;
use App\Models\Event\ScoringSchema;
use App\Models\League;
use App\Models\SERC;
use App\Models\SERCDisqualification;
use App\Models\SERCJudge;
use App\Models\SERCMarkingPoint;
use App\Models\SERCPenalty;
use App\Models\SERCResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class SERCController extends Controller
{


    public function add(Competition $comp)
    {
        return view('competition.events.sercs.add', ['comp' => $comp]);
    }

    public function addPost(Competition $comp, Request $request)
    {

        $json = json_decode($request->input('data'));





        $serc = new SERC();
        $serc->name = $json->serc_name;
        $serc->competition = $comp->id;
        $serc->type = $json->serc_type;
        $serc->scorable_entity = $json->serc_target;
        $serc->save();

        foreach ($json->judges as $judge) {


            $j = new SERCJudge();
            $j->name = $judge->name;
            $j->serc = $serc->id;
            $j->description = $judge->description;
            $j->save();

            foreach ($judge->marking_points as $marking_point) {
                $mp = new SERCMarkingPoint();
                $mp->name = $marking_point->description;
                $mp->weight = $marking_point->weight;
                $mp->judge = $j->id;
                $mp->serc = $serc->id;
                $mp->save();
            }
        }

        $request->session()->flash('success', "SERC created!");

        return response()->json(['sid' => $serc->id]);
    }

    public function view(Competition $comp, SERC $serc)
    {

        $eventResults = null;
        $league = request()->query('league', null);

        $league = League::find($league);

        if ($serc->scoringSchema()) {
            $eventResults = $serc->getRankedResults($league);
        }

        $markingPoints = $serc->getMarkingPoints;
        $totalMPs = $markingPoints->count();

        $totalPossibleResults = $totalMPs * $serc->getScorableEntities()->count();
        $totalResults = SERCResult::whereIn('marking_point', $markingPoints->pluck('id'))->count('entity_id');

        return view('competition.events.sercs.view', ['comp' => $comp, 'serc' => $serc, 'eventResults' => $eventResults, 'activeLeague' => $league, 'totalMPs' => $totalMPs, 'totalResults' => $totalResults, 'totalPossibleResults' => $totalPossibleResults]);
    }

    public function restrictionMap(Competition $comp, SERC $serc)
    {
        $judges = $serc->getJudges()->with('restrictedLeagues')->get();
        $entities = $serc->getScorableEntities();

        $restrictionMap = [];

        foreach ($entities as $entity) {
            $leagueId = $entity->getLeague()?->id;
            $leagueName = $entity->getLeague()?->name;

            $visibleJudges = [];

            if ($serc->use_restricted_judges && $leagueId) {
                // If restrictions are enabled, find judges restricted to this league
                foreach ($judges as $judge) {
                    if ($judge->restrictedLeagues->isEmpty()) {
                        // Judges with no restrictions can see all entities
                        $visibleJudges[] = $judge->name;
                    } elseif ($judge->restrictedLeagues->contains('id', $leagueId)) {
                        // Judge is restricted to this league
                        $visibleJudges[] = $judge->name;
                    }
                }
            } else {
                // No restrictions enabled, all judges see all entities
                $visibleJudges = $judges->pluck('name')->toArray();
            }

            $restrictionMap[] = [
                'entity_name' => $entity->getName($comp),
                'league_name' => $leagueName,
                'judges' => $visibleJudges,
            ];
        }

        return view('competition.events.sercs.restriction-map', [
            'comp' => $comp,
            'serc' => $serc,
            'restrictionMap' => $restrictionMap,
            'totalEntities' => $entities->count()
        ]);
    }

    public function edit(Competition $comp, SERC $serc)
    {
        return view('competition.events.sercs.edit', ['comp' => $comp, 'serc' => $serc]);
    }

    public function editPost(Competition $comp, SERC $serc, Request $request)
    {
        $json = json_decode($request->input('data'));



        // Process deletions first
        SERCJudge::destroy($json->deleted->judges);
        SERCMarkingPoint::destroy($json->deleted->marking_points);



        $serc = SERC::find($json->serc_id);

        $serc->name = $json->serc_name;
        $serc->type = $json->serc_type;
        $serc->scorable_entity = $json->serc_target;
        $serc->use_restricted_judges = $json->use_restricted_judges;

        $serc->save();

        foreach ($json->judges as $judge) {

            $j = null;
            if ($judge->id == "null") {
                $j = new SERCJudge();
            } else {
                $j = SERCJudge::find($judge->id);
            }

            $j->name = $judge->name;
            $j->description = $judge->description;
            $j->serc = $serc->id;
            $j->save();

            $j->restrictedLeagues()->sync($judge->restricted_leagues);

            foreach ($judge->marking_points as $marking_point) {

                $mp = null;
                if ($marking_point->id == "null") {
                    $mp = new SERCMarkingPoint();
                } else {
                    $mp = SERCMarkingPoint::find($marking_point->id);
                }

                $mp->name = $marking_point->description;
                $mp->weight = $marking_point->weight;
                $mp->judge = $j->id;
                $mp->serc = $serc->id;
                $mp->save();
            }
        }

        $request->session()->flash('success', "SERC updated!");

        return response()->json(['sid' => $serc->id]);
    }

    public function delete(Competition $comp, SERC $serc, Request $request)
    {
        if ($serc->id != $request->input('sid')) return;

        $serc->delete();

        return redirect()->route('comps.events', $comp)->with('success', 'SERC deleted!');
    }

    public function editResultsView(Competition $comp, SERC $serc, int $entity_id)
    {

        $team = $serc->getScorableEntity()->findOrFail($entity_id);

        return view('competition.events.sercs.edit-team-results', ['comp' => $comp, 'serc' => $serc, 'team' => $team]);
    }

    public function updateTeamResults(Competition $comp, SERC $serc, int $entity_id, Request $request)
    {
        $team = $serc->getScorableEntity()->findOrFail($entity_id);

        $json = json_decode($request->input('data'));
        $sawDQ = false;
        $sawPen = false;

        $changes = [];

        $marking_points = $serc->getMarkingPoints()->select('serc_marking_points.id', 'serc_marking_points.name', 'serc_marking_points.judge', 'serc_judges.name AS judge_name')->get();

        foreach ($json as $mp) {

            if ($mp->id == "disqualification") {
                $sawDQ = true;
                $serc->clearEntityDisqualifications($team);
                $dq = str_replace('dq', '', strtolower(trim($mp->values->disqualification)));
                $serc->addEntityDisqualification($team, $dq);
                continue;
            }

            if ($mp->id == "penalties") {
                $sawPen = true;
                $serc->clearEntityPenalties($team);
                foreach (explode(',', $mp->values->penalties) as $pen) {
                    $pen = str_replace('p', '', strtolower(trim($pen)));
                    $serc->addEntityPenalty($team, $pen);
                }
                continue;
            }


            $id = $mp->id;
            $score = $mp->values->score ?: 0;

            if ($score > 10) $score = 10;
            if ($score < 0) $score = 0;

            $result = SERCResult::where('marking_point', $id)->forEntity($team)->first();

            $was = null;

            if (!$result) {
                $result = new SERCResult();
                $result->marking_point = $id;
            } else {
                $was = $result->result;
            }

            $result->result = $score;
            $result->entity()->associate($team);

            $result->save();

            if ($was != $score) {
                $mp_record = $marking_points->find($id);
                $changes[] = ['name' => "{$mp_record->judge_name} - {$mp_record->name}", 'old' => $was, 'new' => $score];
            }

            Cache::forget('mp.' . $id . '.team.' . $team->id);
        }

        if (!$sawDQ) {
            $serc->clearEntityDisqualifications($team);
        }

        if (!$sawPen) {
            $serc->clearEntityPenalties($team);
        }


        $nextTeamId = $this->nextTeamId($comp, $serc, $entity_id);

        session()->flash('success', "Results updated for " . $team->getName($comp));

        $changeCount = count($changes);
        if ($changeCount > 0) {
            $serc->recordActivity('SERC_RESULT_UPDATED', $changeCount > 1 ? "{$changeCount} results changed" : "1 result changed", context: [
                'changes' => $changes,
            ], related: [$serc, $team, $comp]);
        }


        if (!$nextTeamId) {
            return response()->json(['sid' => $serc->id]);
        }

        return response()->json(['url' => Route('comps.events.sercs.editResults', [$comp, $serc, $nextTeamId])]);
    }

    private function nextTeamId(Competition $comp, SERC $serc, int $entity_id): ?int
    {
        $team = $serc->getScorableEntity()->findOrFail($entity_id);

        $teamIds = $serc->getScorableEntities()->pluck('id')->toArray();

        $draw = $serc->getDraw->sortBy('draw')->sortBy('tank');

        if ($draw) {
            $teamIds = $draw->pluck('entity_id')->toArray();
        }
        $index = array_search($team->id, array_values($teamIds));



        if ($index + 2 > count($teamIds)) {

            return null;
        }

        return $teamIds[$index + 1];
    }

    public function next(Competition $comp, SERC $serc, int $entity_id)
    {
        $nextTeamId = $this->nextTeamId($comp, $serc, $entity_id);

        if (!$nextTeamId) {
            return redirect()->route('comps.events.sercs.view', compact('comp', 'serc'));
        }

        return redirect()->route('comps.events.sercs.editResults', [$comp, $serc, $nextTeamId]);
    }

    public function hide(Competition $comp, SERC $serc)
    {
        $serc->hide();
        return redirect()->back();
    }

    public function addSercImage(Competition $comp, SERC $serc, Request $request)
    {
        $request->validate([
            'image' => 'nullable|image',
        ]);

        $oldImage = $serc->image;
        if ($request->hasFile('image')) {
            // Remove old file



            if ($oldImage !== null) {
                unlink(public_path() . '/storage/' . $oldImage);
            }




            $serc->image = $request->file('image')->store('serc-images', 'public');
            $serc->save();
        }

        return redirect()->back();
    }

    public function removeSercImage(Competition $comp, SERC $serc)
    {
        unlink(public_path() . '/storage/' . $serc->image);
        $serc->image = null;
        $serc->save();
        return redirect()->back();
    }


    public function scoringSettings(Competition $comp, SERC $serc)
    {
        $event = $serc;
        $route = route('comps.events.sercs.scoring-settings.save', [$comp, $event]);
        $returnRoute = route('comps.events.sercs.view', [$comp, $event]);
        return view('competition.events.event-settings', compact('comp', 'event', 'route', 'returnRoute'));
    }

    public function saveScoringSettings(Competition $comp, SERC $serc, UpdateScoringSettings $request)
    {
        $validated = $request->validated();

        $schema = $serc->scoringSchema;

        if (!$schema) {
            $schema = new ScoringSchema();
            $schema->name = "Scoring Schema for {$serc->getType()}:{$serc->id}";
            $schema->schema = [];
            $schema->save();
            $serc->scoring_schema = $schema->id;
            $serc->save();
        }

        $schema->editFromRequest($request);

        // $ss = ['equation' => $validated['equation'], 'global_variables' => $validated['global_variables']];

        // if (array_key_exists('local_variables', $validated)) {
        //     $ss['local_variables'] = $validated['local_variables'];
        // }

        // if (array_key_exists('penalty_func', $validated)) {
        //     $ss['penalty_func'] = $validated['penalty_func'];
        // }

        // if (array_key_exists('auto_penalties', $validated)) {
        //     $ss['auto_penalties'] = $validated['auto_penalties'];
        // }

        // if (array_key_exists('auto_disqualifications', $validated)) {
        //     $ss['auto_disqualifications'] = $validated['auto_disqualifications'];
        // }

        // $schema->schema = $ss;



        // $schema->Save();

        return response()->json([]);
    }

    public function printResults(Competition $comp, SERC $serc)
    {
        $data = [[
            'league' => 'Overall',
            'results' => $serc->getRankedResults(),
        ]];

        foreach ($comp->getLeagues->sortBy('name') as $league) {
            $results = $serc->getRankedResults($league);

            $data[] = [
                'league' => $league->name,
                'results' => $results,
            ];
        }

        return view('competition.events.sercs.print', ['comp' => $comp, 'serc' => $serc, 'data' => $data]);
    }

    public function markSplits(Competition $comp, SERC $serc)
    {
        return view('competition.events.sercs.mark-splits', ['comp' => $comp, 'serc' => $serc]);
    }

    public function loadMarkSplit(Competition $comp, SERC $serc, SERCJudge $judge)
    {
        $marking_points = $serc->getMarkingPoints()->where('judge', $judge->id)->get();

        $results = SERCResult::with('entity')->whereIn('marking_point', $marking_points->pluck('id'))->get();
        $grouped = $results->groupBy('entity_id')->map(function ($item) {
            return $item->sortBy('marking_point')->mapWithKeys(function ($mp) {
                return [
                    $mp->marking_point => [
                        'result' => $mp->result,
                        'date_recorded' => $mp->created_at->toDateTimeString()
                    ],
                ];
            });
        });

        $draw = $serc->getDraw->sortBy('draw')->sortBy('tank');




        $data = [];

        foreach ($draw as $draw_team) {

            $entity = $draw_team->entity;
            $entity_results = $grouped->get($draw_team->entity_id, collect([]));
            $data[] = [
                'entity' => "{$draw_team->draw}. {$entity->getName($comp)}",
                'results' => $entity_results,
            ];
        }


        // group by the entity and then order by marking point id


        $marking_points = $marking_points->sortBy('id')->map(function ($mp) {
            return ['id' => $mp->id, 'name' => $mp->name];
        })->toArray();



        return response()->json(['marking_points' => $marking_points, 'results' => $data]);
    }
}
