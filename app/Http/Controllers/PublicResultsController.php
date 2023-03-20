<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\Penalty;
use App\Models\ResultSchema;
use App\Models\SERC;
use Illuminate\Http\Request;

class PublicResultsController extends Controller
{
    public function index()
    {

        $compsWithViewAbleResults = Competition::where('public_results', true)->get();

        return view('public-results.index', ['comps' => $compsWithViewAbleResults]);
    }

    public function viewComp(Competition $comp_slug)
    {
        return view('public-results.view-comp', ['comp' => $comp_slug]);
    }

    public function viewSpeed(Competition $comp_slug, CompetitionSpeedEvent $event, Request $request)
    {
        if ($request->exists('dlCSV')) return $this->getSpeedAsCSV($event, $comp_slug);
        else return view('public-results.view-speed', ['comp' => $comp_slug, 'event' => $event]);
    }

    public function viewSerc(Competition $comp_slug, SERC $event, Request $request)
    {

        if ($request->exists('dlCSV')) return $this->getSercAsCSV($event, $comp_slug);

        else return view('public-results.view-serc', ['comp' => $comp_slug, 'event' => $event]);
    }

    public function viewResults(Competition $comp_slug, ResultSchema $schema)
    {

        if (!$schema->viewable) return redirect()->route('public.results.comp', ['comp_slug' => $comp_slug->resultsSlug()]);

        return view('public-results.view-results', ['comp' => $comp_slug, 'schema' => $schema, 'results' => $schema->getDetailedPrint()]);
    }


    private function getSercAsCSV(SERC $event, Competition $comp)
    {



        $this->convertToCSVAndDownload($event->getResults(), function () use ($event) {

            $headers = ['Team'];

            $weightRow = [''];

            foreach ($event->getJudges as $judge) {
                foreach ($judge->getMarkingPoints as $mp) {
                    array_push($headers, $mp->name);
                    array_push($weightRow, $mp->weight);
                }
            }



            return [array_merge($headers, ['DQ', 'Points', 'Position']), $weightRow];
        }, function ($row) use ($event) {
            $data = [$row->team];

            foreach ($event->getJudges as $judge) {
                foreach ($judge->getMarkingPoints as $mp) {
                    array_push($data, round($mp->getScoreForTeam($row->tid)));
                }
            }

            return array_merge($data, [$event->getTeamDQ(\App\Models\CompetitionTeam::find($row->tid))?->code ?: '-', round($row->points), $row->place]);
        }, $event->name . " - " . $comp->name);
    }

    private function getSpeedAsCSV(CompetitionSpeedEvent $event, Competition $comp)
    {
        $this->convertToCSVAndDownload($event->getResults(), function () use ($event) {

            $headers = ['Team', ($event->getName() == "Rope Throw" ? "Ropes/Time" : "Time"), 'DQ'];

            if ($event->hasPenalties()) array_push($headers, "Penalties");



            return [array_merge($headers, ['Points', 'Position'])];
        }, function ($row) use ($event) {
            $data = [$row->team];

            $time = "";
            if ($row->result < 4) {
                $time = $row->result;
            } else {
                $mins = floor($row->result / 60000);
                $secs = (($row->result) - ($mins * 60000)) / 1000;
                $time = sprintf("%02d", $mins) . ':' . str_pad(number_format($secs, 3, '.', ''), 6, '0', STR_PAD_LEFT);
            }

            array_push($data, $time);

            array_push($data, $row->disqualification ?: '-');

            if ($event->hasPenalties()) {
                $penString = "";

                $penString .= Penalty::where('speed_result', $row->id)->get('code')->implode('code', ', ') ?: ($row->penalties == 0 ? '-' : '');
                if ($event->getName() == "Swim & Tow" && $row->penalties != 0) {
                    $penString .= "(P900 x" . $row->penalties - Penalty::where('speed_result', $row->id)->count() . ")";
                }
                $penString = ltrim($penString, " ");
                array_push($data, $penString);
            }

            return array_merge($data, [round($row->points), $row->place]);
        }, $event->getName() . " - " . $comp->name);
    }


    private function convertToCSVAndDownload($results, $colGenerator, $rowGenerator, $downloadName)
    {

        if (!headers_sent()) {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $downloadName . '.csv";');
        }
        $headers = $colGenerator();
        $rows = [];



        foreach ($results as $result) {
            $row = $rowGenerator($result);
            array_push($rows, $row);
        }

        $fp = fopen('php://output', 'w');

        foreach ($headers as $header) {
            fputcsv($fp, $header);
        }

        foreach ($rows as $row) {
            fputcsv($fp, $row);
        }





        fpassthru($fp);
        fclose($fp);
    }
}
