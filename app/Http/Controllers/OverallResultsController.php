<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\ResultSchema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OverallResultsController extends Controller
{


    public function computeResults(ResultSchema $schema)
    {

        $events = $schema->getEvents;

        $finalQuery = "WITH ";

        $mysqlEventNames = [];
        $mysqlEventNamesArray = [];


        foreach ($events as $event) {
            $actualEvent = $event->getActualEvent;
            $query = $actualEvent->getResultQuery();
            $eventName = $actualEvent->getName();



            $eventMysqlName = str_replace("&", "", str_replace(" ", "_", Str::lower($eventName))) . "_" . $actualEvent->id;
            //echo $eventMysqlName;

            $mysqlEventNames[$event->id] = $eventMysqlName;
            array_push($mysqlEventNamesArray, $eventMysqlName);

            $finalQuery .= $eventMysqlName . " AS (" . rtrim($query, ";") . "), ";
        }

        $finalQuery = rtrim($finalQuery, ", ");
        $finalQuery .= " SELECT " . $mysqlEventNamesArray[0] . ".team, ";

        foreach ($events as $event) {
            $mysqlTableName = $mysqlEventNames[$event->id];
            $finalQuery .= $mysqlTableName . ".points AS " . $mysqlTableName . "_points, ";
            $finalQuery .=  $event->weight . " AS " . $mysqlTableName . "_weight, ";
            $finalQuery .= "(SELECT MIN(points) FROM " . $mysqlTableName . " WHERE points>0) AS " . $mysqlTableName . "_min, ";
            $finalQuery .= "(SELECT MAX(points) FROM " . $mysqlTableName . ") AS " . $mysqlTableName . "_max, ";
            $finalQuery .= "900/((SELECT MAX(points) FROM " . $mysqlTableName . ") - (SELECT MIN(points) FROM " . $mysqlTableName . " WHERE points > 0)) AS " . $mysqlTableName . "_mult_factor, ";
            $finalQuery .=  "IF(" . $mysqlTableName . ".points = 0,0,(" . $mysqlTableName . ".points" . "-" . "(SELECT MIN(points) FROM " . $mysqlTableName . " WHERE points > 0))" . "*" . "(900/((SELECT MAX(points) FROM " . $mysqlTableName . ") - (SELECT MIN(points) FROM " . $mysqlTableName . " WHERE points>0)))+100) * " . $event->weight . " AS " . $mysqlTableName . "_rsp, ";
        }

        $finalQuery = rtrim($finalQuery, ", ");

        $finalQuery .= " FROM " . $mysqlEventNamesArray[0];

        $first = true;

        $prev = $mysqlEventNamesArray[0];
        foreach ($mysqlEventNamesArray as $event) {
            if ($first) {
                $first = false;
                continue;
            }

            $finalQuery .= " INNER JOIN " . $event . " ON " . $event . ".team=" . $prev . ".team ";
            $prev = $event;
        }

        $finalQuery = rtrim($finalQuery, " ");

        $final = "SELECT team, ";
        foreach ($mysqlEventNamesArray as $event) {
            $final .= $event . "_rsp + ";
        }

        $final = rtrim($final, "+ ") . " AS totalPoints FROM (" . $finalQuery . ") AS bb";

        $final = "SELECT *, RANK() OVER(ORDER BY totalPoints DESC) place FROM (" . $final . ") AS bbb;";

        //echo $final;
        //return;




        $results = DB::select($final);

        return view('competition.results.view', ['results' => $results, 'schema' => $schema, 'comp' => $schema->getCompetition]);
    }

    public function view(Competition $comp)
    {
        return view('competition.results', ['comp' => $comp]);
    }
}
