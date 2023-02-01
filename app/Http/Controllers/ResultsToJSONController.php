<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\SERC;
use Illuminate\Http\Request;

class ResultsToJSONController extends Controller
{
    public function viewSerc(Competition $comp_slug, SERC $event, Request $request)
    {




        $mpRow = [['value' => 'Team']];

        $weightRow = [['value' => '']];

        foreach ($event->getJudges as $judge) {
            foreach ($judge->getMarkingPoints as $mp) {
                array_push($mpRow, ['value' => $mp->name, 'vertical' => true]);
                array_push($weightRow, ['value' => $mp->weight]);
            }
        }


        $headers = [array_merge($mpRow, [['value' => 'DQ'], ['value' => 'Points'], ['value' => 'Position']]), array_merge($weightRow, [['value' => ''], ['value' => ''], ['value' => '']])];

        $rows = [];

        foreach ($event->getResults() as $row) {
            $data = [['value' => $row->team]];

            foreach ($event->getJudges as $judge) {
                foreach ($judge->getMarkingPoints as $mp) {
                    array_push($data, ['value' => round($mp->getScoreForTeam($row->tid)), 'editable' => true, 'type' => 'mp', 'id' => $mp->id]);
                }
            }

            array_push($rows, ['cells' => array_merge($data, [['value' => $event->getTeamDQ(\App\Models\CompetitionTeam::find($row->tid))?->code ?: '-', 'editable' => true, 'type' => 'dq', 'id' => '-1'], ['value' => round($row->points)], ['value' => $row->place]]), 'id' => $row->tid]);
        }





        $tableData = ['name' => $comp_slug->name . " " . $event->getName(), 'headers' => $headers, 'rows' => $rows, 'id' => $comp_slug->id];

        return response()->json($tableData);
    }
}
