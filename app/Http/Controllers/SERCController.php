<?php

namespace App\Http\Controllers;

use App\Helpers\ClassHelpers;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\Competitor;
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
        return view('competition.events.sercs.view', ['comp' => $comp, 'serc' => $serc]);
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

        return redirect()->route('comps.view.events', $comp)->with('success', 'SERC deleted!');
    }

    public function editResultsView(Competition $comp, SERC $serc, CompetitionTeam $team)
    {

        if ($comp->scoring_type == "rlss-nationals") {
            $team = ClassHelpers::castToClass($team, Competitor::class);
        }

        return view('competition.events.sercs.edit-team-results', ['comp' => $comp, 'serc' => $serc, 'team' => $team]);
    }

    public function updateTeamResults(Competition $comp, SERC $serc, CompetitionTeam $team, Request $request)
    {
        $json = json_decode($request->input('data'));

        $disSet = false;
        $penSet = false;

        foreach ($json as $mp) {

            if ($mp->id == "disqualification") {
                $sd = SERCDisqualification::firstOrNew(['team' => $team->id, 'serc' => $serc->id]);
                $sd->code = $mp->values->disqualification;
                $sd->save();
                $disSet = true;
                continue;
            }

            if ($mp->id == "penalties") {
                $sd = SERCPenalty::firstOrNew(['team' => $team->id, 'serc' => $serc->id]);
                $sd->codes = $mp->values->penalties;
                $sd->save();
                $penSet = true;
                continue;
            }


            $id = $mp->id;
            $score = $mp->values->score ?: 0;

            if ($score > 10) $score = 10;
            if ($score < 0) $score = 0;

            $result = SERCResult::firstOrNew(['marking_point' => $id, 'team' => $team->id]);

            $result->result = $score;

            $result->save();

            Cache::forget('mp.' . $id . '.team.' . $team->id);
        }

        if (!$disSet) {
            SERCDisqualification::where(['team' => $team->id, 'serc' => $serc->id])->delete();
        }
        if (!$penSet) {
            SERCPenalty::where(['team' => $team->id, 'serc' => $serc->id])->delete();
        }


        $teamIds = $serc->getTeams()->pluck('id')->toArray();
        $index = array_search($team->id, array_values($teamIds));



        if ($index + 2 > count($teamIds)) {

            return response()->json(['sid' => $serc->id]);
        }

        $nextTeamId = $teamIds[$index + 1];

        return response()->json(['url' => Route('comps.view.events.sercs.editResults', [$comp, $serc, $nextTeamId])]);
    }

    public function next(Competition $comp, SERC $serc, CompetitionTeam $team)
    {
        $teamIds = $serc->getTeams()->pluck('id')->toArray();
        $index = array_search($team->id, array_values($teamIds));



        if ($index + 2 > count($teamIds)) {

            return redirect()->route('comps.view.events.sercs.view', compact('comp', 'serc'));
        }

        $nextTeamId = $teamIds[$index + 1];

        return redirect()->route('comps.view.events.sercs.editResults', [$comp, $serc, $nextTeamId]);
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
}
