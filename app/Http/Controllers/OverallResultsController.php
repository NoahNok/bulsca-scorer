<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\ResultSchema;

/**
 * The Results sheets' print pages (Blade). The Results pages themselves are Manage\ResultSheetsController.
 */
class OverallResultsController extends Controller
{
    public function viewForPrintBasic(ResultSchema $schema)
    {

        $results = $schema->getResults() ?? [];
        $comp = $schema->getCompetition;
        return view("competition.results.print.view-for-print-basic", ['results' => $results, 'schema' => $schema, 'comp' => $comp]);
    }

    public function viewForPrint(ResultSchema $schema)
    {

        $results = $schema->getResults() ?? [];
        $comp = $schema->getCompetition;
        return view("competition.results.print.view-for-print", ['results' => $results, 'schema' => $schema, 'comp' => $comp]);
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
}
