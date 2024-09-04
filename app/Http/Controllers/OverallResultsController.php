<?php

namespace App\Http\Controllers;

use App\Helpers\ScoringHelper;
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

        $schema = $schema->autoCast();

        $results = $schema->getResults() ?? [];
        // if ($final != null) {
        //     $results = DB::select($final);
        // }

       
        return view('competition.results.view', ['results' => $results, 'schema' => $schema, 'comp' => $schema->getCompetition]);
    }

    public function viewForPrintBasic(ResultSchema $schema)
    {
        $schema = $schema->autoCast();
        $results = $schema->getResults() ?? [];
        return view('competition.results.view-for-print-basic', ['results' => $results, 'schema' => $schema, 'comp' => $schema->getCompetition]);
    }

    public function viewForPrint(ResultSchema $schema)
    {
        $schema = $schema->autoCast();
        $results = $schema->getResults() ?? [];
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
        ScoringHelper::generateDefaultResultSheets($comp);

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

        if (!$comp->results_provisional && $comp->isLeague) { // Only generate stats for league comps
            $comp->generateStats();
            return redirect()->back()->with("success","Generated stats");
        }

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
