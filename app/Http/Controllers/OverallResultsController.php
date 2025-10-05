<?php

namespace App\Http\Controllers;

use App\Helpers\ScoringHelper;
use App\Models\Competition;
use App\Models\League;
use App\Models\ResultSchema;
use App\Models\ResultSchemaEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OverallResultsController extends Controller
{


    public function computeResults(ResultSchema $schema)
    {



        $results = $schema->getResults() ?? [];
        // if ($final != null) {
        //     $results = DB::select($final);
        // }


        return view('competition.results.view', ['results' => $results, 'schema' => $schema, 'comp' => $schema->getCompetition]);
    }

    public function viewForPrintBasic(ResultSchema $schema)
    {

        $results = $schema->getResults() ?? [];
        $comp = $schema->getCompetition;
        return view("competition.results.print.$comp->scoring_type.view-for-print-basic", ['results' => $results, 'schema' => $schema, 'comp' => $comp]);
    }

    public function viewForPrint(ResultSchema $schema)
    {

        $results = $schema->getResults() ?? [];
        $comp = $schema->getCompetition;
        return view("competition.results.print.$comp->scoring_type.view-for-print", ['results' => $results, 'schema' => $schema, 'comp' => $comp]);
    }

    public function printAll(Competition $comp)
    {
        $data = [];
        foreach ($comp->getResultSchemas as $schema) {

            $results = $schema->getResults() ?? [];
            array_push($data, ['results' => $results, 'schema' => $schema]);
        }

        return view("competition.results.print.all", ['data' => $data, 'comp' => $comp]);
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


        function createForLeague($name, ?League $league, string $group_on, array $events, bool $rank_higher, bool $ignore_disqualified, array $break_ties, string $equation, array $global_vars, Competition $comp): ResultSchema
        {
            $rs = new ResultSchema();
            $rs->name = $name == '' || !$name ? $league->name : $name;
            $rs->competition = $comp->id;
            $rs->schema = [
                'equation' => $equation,
                'global_variables' => $global_vars,
                'league' => $league?->id ?? null,
                'ignore_dq' => $ignore_disqualified,
                'group_on' => $group_on,
                'rank_higher' => $rank_higher,
                'break_ties' => $break_ties,
            ];

            $rs->save();

            foreach ($events as $event) {
                $rse = new ResultSchemaEvent();
                $rse->schema = $rs->id;
                $rse->event_id = $event['id'];
                $rse->event_type = $event['type'] == "serc" ? "\App\Models\SERC" : "\App\Models\CompetitionSpeedEvent";
                $rse->weight = $event['weight'];
                $rse->save();
            }

            return $rs;
        }

        $name = $request->input('name', '');

        $league = $request->input('league');
        $group_on = $request->input('group_on');
        $events = $request->input('events');
        $rank_higher = $request->input('rank_higher');
        $ignore_disqualified = $request->input('ignore_disqualified');
        $repeat_for_all_leagues = $request->input('repeat_for_all_leagues');
        $equation = $request->input('equation', 'item.points');
        $global_vars = $request->input('global_variables', []);

        $break_ties = collect($events)->filter(fn($event) => $event['break_ties'])->sortBy('break_ties')->pluck('id')->all();


        $league = League::find($league);

        $repeat_for = collect([$league]);
        $created = [];

        if ($repeat_for_all_leagues) {
            $repeat_for = $comp->getLeagues;
        }

        foreach ($repeat_for as $league) {
            $created[] = createForLeague($name, $league, $group_on, $events, $rank_higher, $ignore_disqualified, $break_ties, $equation, $global_vars, $comp);
        }

        // $rs->schema = [
        //     "equation" => "(item.points - minPoints) * multFac + minScore",
        //     "global_variables" => [
        //         [
        //             "name" => "valid_teams",
        //             "order" => 1,
        //             "expression" => "FILTER(results, '!item.isDisqualified()')",
        //         ],
        //         [
        //             "name" => "minPoints",
        //             "order" => 2,
        //             "expression" => "MINIMUM(valid_teams, 'points')",
        //         ],
        //         [
        //             "name" => "maxPoints",
        //             "order" => 3,
        //             "expression" => "MAXIMUM(valid_teams, 'points')",
        //         ],
        //         [
        //             "name" => "minScore",
        //             "order" => 4,
        //             "expression" => "100",
        //         ],
        //         [
        //             "name" => "spread",
        //             "order" => 5,
        //             "expression" => "1000 - minScore",
        //         ],
        //         [
        //             "name" => "multFac",
        //             "order" => 6,
        //             "expression" => "spread / (maxPoints - minPoints)",
        //         ],
        //     ],
        //     "league" => $schema_league,
        // ];

        if (count($created) == 1) {
            return response()->json(['url' => route('comps.results.view-schema', $created[0]->id)]);
        } else {
            return response()->json(['url' => route('comps.results', $comp)]);
        }
    }

    public function quickGen(Competition $comp)
    {
        ScoringHelper::generateDefaultResultSheets($comp);

        return redirect()->route('comps.results', $comp);
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

        // if (!$comp->results_provisional && $comp->isLeague) { // Only generate stats for league comps
        //     $comp->generateStats();
        //     return redirect()->back()->with("success", "Generated stats");
        // }

        return redirect()->back();
    }

    public function delete(Competition $comp, ResultSchema $schema)
    {
        $schema->delete();
        return redirect()->route('comps.results', $comp);
    }

    public function hide(Competition $comp, ResultSchema $schema)
    {
        $schema->viewable = !$schema->viewable;
        $schema->save();
        return redirect()->back();
    }
}
