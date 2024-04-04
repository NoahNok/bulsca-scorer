<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\ResultSchema;
use App\Models\ResultSchemaEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OverallResultsController extends Controller
{


    public function computeResults(ResultSchema $schema)
    {

        $results = [];
        $final = $schema->getRawQuery(); 
        if ($final != null) {
            $results = DB::select($final);
        }

       
        return view('competition.results.view', ['results' => $results, 'schema' => $schema, 'comp' => $schema->getCompetition]);
    }

    public function viewForPrintBasic(ResultSchema $schema)
    {
        $results = DB::select($schema->getRawQuery());
        return view('competition.results.view-for-print-basic', ['results' => $results, 'schema' => $schema, 'comp' => $schema->getCompetition]);
    }

    public function viewForPrint(ResultSchema $schema)
    {
        $results = $results = DB::select($schema->getRawQuery());
        return view('competition.results.view-for-print', ['results' => $results, 'schema' => $schema, 'comp' => $schema->getCompetition]);
    }


    public function view(Competition $comp)
    {
        return view('competition.results', ['comp' => $comp]);
    }

    public function add(Competition $comp)
    {
        return view('competition.results.add', ['comp' => $comp]);
    }

    public function addPost(Competition $comp, Request $request)
    {

        $json = json_decode($request->input('data'));

        $errors = [];



        $schema_name = null;
        $schema_league = null;

        foreach ($json as $event) {

            if ($event->id == "name") {

                if ($event->values->name == '') {
                    array_push($errors, ['id' => "name", "option" => "name"]);
                    continue;
                }

                $schema_name = $event->values->name;
                continue;
            }

            if ($event->id == "league") {
                if ($event->values->league == '') {
                    array_push($errors, ['id' => "league", "option" => "league"]);
                    continue;
                }
                $schema_league = $event->values->league;
                continue;
            }

            if ($event->values->weight == '') {
                array_push($errors, ['id' => $event->id, "option" => "weight"]);
                continue;
            }
        }

        if (!empty($errors)) {
            return response()->json($errors, 500);
        }


        $rs = new ResultSchema();
        $rs->name = $schema_name;
        $rs->competition = $comp->id;
        $rs->league = $schema_league;
        $rs->save();

        foreach ($json as $event) {
            if ($event->id == "name" || $event->id == "league") continue;
            $rse = new ResultSchemaEvent();
            $rse->schema = $rs->id;
            $rse->event_id = $event->id;
            $rse->event_type = $event->values->type == "serc" ? "\App\Models\SERC" : "\App\Models\CompetitionSpeedEvent";
            $rse->weight = $event->values->weight;
            $rse->save();
        }

        return response()->json(['url' => route('comps.results.view-schema', $rs->id)]);
    }

    public function quickGen(Competition $comp)
    {
        $overall = new ResultSchema();
        $overall->competition = $comp->id;
        $overall->name = "Overall";
        $overall->league = "O";
        $overall->save();

        $a = new ResultSchema();
        $a->competition = $comp->id;
        $a->name = "A-League";
        $a->league = "A";
        $a->save();

        $b = new ResultSchema();
        $b->competition = $comp->id;
        $b->name = "B-League";
        $b->league = "B";
        $b->save();

        foreach ($comp->getSERCs as $serc) {
            $rse = new ResultSchemaEvent();
            $rse->schema = $overall->id;
            $rse->event_id = $serc->id;
            $rse->event_type = "\App\Models\SERC";
            $rse->weight = 2;
            $rse->save();
            $rse = new ResultSchemaEvent();
            $rse->schema = $a->id;
            $rse->event_id = $serc->id;
            $rse->event_type = "\App\Models\SERC";
            $rse->weight = 2;
            $rse->save();
            $rse = new ResultSchemaEvent();
            $rse->schema = $b->id;
            $rse->event_id = $serc->id;
            $rse->event_type = "\App\Models\SERC";
            $rse->weight = 2;
            $rse->save();
        }

        foreach ($comp->getSpeedEvents as $serc) {
            $rse = new ResultSchemaEvent();
            $rse->schema = $overall->id;
            $rse->event_id = $serc->id;
            $rse->event_type = "\App\Models\CompetitionSpeedEvent";
            $rse->weight = 1;
            $rse->save();
            $rse = new ResultSchemaEvent();
            $rse->schema = $a->id;
            $rse->event_id = $serc->id;
            $rse->event_type = "\App\Models\CompetitionSpeedEvent";
            $rse->weight = 1;
            $rse->save();
            $rse = new ResultSchemaEvent();
            $rse->schema = $b->id;
            $rse->event_id = $serc->id;
            $rse->event_type = "\App\Models\CompetitionSpeedEvent";
            $rse->weight = 1;
            $rse->save();
        }

        return redirect()->route('comps.view.results', $comp);
    }

    public function publishToggle(Competition $comp)
    {
        $comp->public_results = !$comp->public_results;
        $comp->save();

        return redirect()->back();
    }

    public function provToggle(Competition $comp)
    {
        $comp->results_provisional = !$comp->results_provisional;
        $comp->save();

        return redirect()->back();
    }

    public function delete(Competition $comp, ResultSchema $schema)
    {
        $schema->delete();
        return redirect()->route('comps.view.results', $comp);
    }

    public function hide(Competition $comp, ResultSchema $schema)
    {
        $schema->viewable = !$schema->viewable;
        $schema->save();
        return redirect()->back();
    }
}
