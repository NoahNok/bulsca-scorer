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

        return view('competition.events.sercs.view', ['comp' => $comp, 'serc' => $serc, 'eventResults' => $eventResults, 'activeLeague' => $league]);
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

            if (!$result) {
                $result = new SERCResult();
                $result->marking_point = $id;
            }

            $result->result = $score;
            $result->entity()->associate($team);

            $result->save();

            Cache::forget('mp.' . $id . '.team.' . $team->id);
        }

        if (!$sawDQ) {
            $serc->clearEntityDisqualifications($team);
        }

        if (!$sawPen) {
            $serc->clearEntityPenalties($team);
        }


        $teamIds = $serc->getScorableEntities()->pluck('id')->toArray();
        $index = array_search($team->id, array_values($teamIds));



        if ($index + 2 > count($teamIds)) {

            return response()->json(['sid' => $serc->id]);
        }

        $nextTeamId = $teamIds[$index + 1];

        session()->flash('success', "Results updated for " . $team->getName());

        return response()->json(['url' => Route('comps.events.sercs.editResults', [$comp, $serc, $nextTeamId])]);
    }

    public function next(Competition $comp, SERC $serc, int $entity_id)
    {
        $team = $serc->getScorableEntity()->findOrFail($entity_id);

        $teamIds = $serc->getTeams()->pluck('id')->toArray();
        $index = array_search($team->id, array_values($teamIds));



        if ($index + 2 > count($teamIds)) {

            return redirect()->route('comps.events.sercs.view', compact('comp', 'serc'));
        }

        $nextTeamId = $teamIds[$index + 1];

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
}
