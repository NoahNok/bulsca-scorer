<?php

namespace App\Http\Controllers\Result;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\MasterSchema;
use Illuminate\Http\Request;

class MasterSchemaController extends Controller
{
    //


    public function add(Competition $comp)
    {
        return view('competition.results.master.add', compact('comp'));
    }

    public function addPost(Competition $comp, Request $request)
    {

        $name = $request->input('name');
        $sumOver = $request->input('sum_over', 'position');
        $groupOn = $request->input('group_on', 'club');
        $defaultValue = $request->input('default_value', 0) * 1;
        $sheets = $request->input('sheets', []);
        $exclude = $request->input('exclude', []);

        $sheets = array_map(function ($sheet) {
            return ['id' => $sheet['id'], 'weight' => $sheet['weight']];
        }, $sheets);

        $ms = new MasterSchema();
        $ms->name = $name;
        $ms->competition = $comp->id;
        $ms->viewable = true;
        $ms->schema = [
            'sum_over' => $sumOver,
            'default_value' => $defaultValue,
            'sheets' => $sheets,
            'group_on' => $groupOn,
            'exclude' => $exclude
        ];

        $ms->save();


        return response()->json(['url' => route('comps.results.master.view', [$comp, $ms])]);
    }

    public function view(Competition $comp, MasterSchema $schema)
    {
        $results = $schema->getResults() ?? [];

        return view('competition.results.master.view', ['results' => $results, 'schema' => $schema, 'comp' => $comp]);
    }

    public function delete(Competition $comp, MasterSchema $schema)
    {
        $schema->delete();
        return redirect()->route('comps.results', $comp);
    }

    public function hide(Competition $comp, MasterSchema $schema)
    {
        $schema->viewable = !$schema->viewable;
        $schema->save();
        return redirect()->back();
    }
}
