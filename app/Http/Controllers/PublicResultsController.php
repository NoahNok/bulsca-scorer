<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\DigitalJudge\JudgeDQSubmission;
use App\Models\DQCode;
use App\Models\Penalty;
use App\Models\PenaltyCode;
use App\Models\ResultSchema;
use App\Models\SERC;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicResultsController extends Controller
{





    public function index()
    {



        return view('public-results.index');
    }

    public function viewComp(Competition $comp_slug)
    {
        return view("public-results.$comp_slug->scoring_type.view-comp", ['comp' => $comp_slug]);
    }

    public function viewSpeed(Competition $comp_slug, CompetitionSpeedEvent $event, Request $request)
    {
        if ($request->exists('dlCSV')) return $this->getSpeedAsCSV($event, $comp_slug);
        else return view("public-results.$comp_slug->scoring_type.view-speed", ['comp' => $comp_slug, 'event' => $event]);
    }

    public function viewSerc(Competition $comp_slug, SERC $event, Request $request)
    {

        if ($request->exists('dlCSV')) return $this->getSercAsCSV($event, $comp_slug);


        $fasterSercData = $event->getSERCData();


        return view("public-results.$comp_slug->scoring_type.view-serc", ['comp' => $comp_slug, 'event' => $event, 'fsd' => $fasterSercData]);
    }

    public function viewResults(Competition $comp_slug, ResultSchema $schema)
    {

        if (!$schema->viewable) return redirect()->route('public.results.comp', ['comp_slug' => $comp_slug->resultsSlug()]);


        $schema = $schema->autoCast();
        $results = $schema->getResults() ?? [];

        return view("public-results.$comp_slug->scoring_type.view-results", ['comp' => $comp_slug, 'schema' => $schema, 'results' => $results]);
    }


    private function getSercAsCSV(SERC $event, Competition $comp)
    {

        $rowIndex = 3;

        $this->convertToCSVAndDownload($event->getResults(), function () use ($event) {

            $headers = ['Team'];

            $weightRow = [''];

            foreach ($event->getJudges as $judge) {
                foreach ($judge->getMarkingPoints as $mp) {
                    array_push($headers, $judge->name . ' - ' . $mp->name);
                    array_push($weightRow, $mp->weight);
                }
            }



            return [array_merge($headers, ['DQ', 'Raw Score', 'Points', 'Position']), $weightRow];
        }, function ($row, $rowIndex) use ($event, $comp) {
            $data = [$row->team];

            $totalMarkingPointCols = 0;
            foreach ($event->getJudges as $judge) {
                foreach ($judge->getMarkingPoints as $mp) {
                    array_push($data, round($mp->getScoreForTeam($row->tid)));
                    $totalMarkingPointCols += 1;
                }
            }

            $rowIndex = $rowIndex + 2;

            $endLetter = $this->convertIndexToSheetLetter($totalMarkingPointCols);
            $scoreFT = "B" . $rowIndex . ":" . $endLetter . $rowIndex;
            $scoreWT = "B2" . ":" . $endLetter . "2";

            $excelSum = "=SUMPRODUCT(" . $scoreFT  . "," . $scoreWT . ")";

            $sumColIndex = 1 + $totalMarkingPointCols + 1;

            $sumColIndLetter = $this->convertIndexToSheetLetter($sumColIndex);

            $maxEnd = $sumColIndLetter . $comp->getCompetitionTeams->count() + 2;

            $max = "MAX(" . $sumColIndLetter . "3:" . $maxEnd . ")";

            $weightScore = "=ROUND(" . $sumColIndLetter . $rowIndex . "/" . $max . "*1000,0)";

            $rankLetter = $this->convertIndexToSheetLetter($sumColIndex + 1);

            $rankIndv = $rankLetter . $rowIndex;

            $rankRange = $rankLetter . "$3" . ":" . $rankLetter . "$" . $comp->getCompetitionTeams->count() + 2;

            $position = "=RANK.EQ(" . $rankIndv . ", " . $rankRange . ")";



            return array_merge($data, [$event->getTeamDQ(\App\Models\CompetitionTeam::find($row->tid))?->code ?: '-', $excelSum, $weightScore, $position]);
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

    /**
     * 0 = 'A'
     * 25 = 'Z'
     */
    public function convertIndexToSheetLetter($index)
    {
        $letters = str_split("ABCDEFGHIJKLMNOPQRSTUVWXYZ");

        $code = "";

        if ($index < 26) return $letters[$index];

        return $letters[floor($index / 26) - 1] . $letters[$index - floor($index / 26) * 26];
    }

    private function convertToCSVAndDownload($results, $colGenerator, $rowGenerator, $downloadName)
    {

        if (!headers_sent()) {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $downloadName . '.csv";');
        }
        $headers = $colGenerator();
        $rows = [];


        $indx = 1;
        foreach ($results as $result) {
            $row = $rowGenerator($result, $indx);
            $indx += 1;
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

    public function viewTeamSercNotes(Competition $comp_slug, SERC $event, CompetitionTeam $team)
    {
        return view("public-results.$comp_slug->scoring_type.view-team-serc-notes", ['comp' => $comp_slug, 'serc' => $event, 'team' => $team]);
    }

    public function resolve($date, $name)
    {

        $d = Carbon::parse($date);

        $comp = Competition::where('when', $d)->firstOrFail();

        return redirect()->route('public.results.comp', ['comp_slug' => $comp->resultsSlug()]);
    }

    public function viewDqPen(Competition $comp, CompetitionTeam $team, string $code)
    {


        $judgeSubmission = collect(DB::select("SELECT * FROM judge_dq_submissions jds INNER JOIN heats h ON h.id=jds.heat_lane WHERE jds.competition=? AND h.team=? AND code=?", [$comp->id, $team->id, $code]))->first();

        // $judgeSubmission = JudgeDQSubmission::with('getHeat')->where('competition', $comp->id)->whereHas('getHeat', function ($query) use ($team) {
        //     $query->where('team', $team->id)
        // })->where('code', $code)->first();

        if (!$judgeSubmission) {
            $code = strtoupper($code);


            if (str_starts_with($code, 'P')) {
                $number = substr($code, 1);

                return PenaltyCode::where('id', $number)->first()->description ?? "Unknown Penalty Code";
            } else {
                $number = substr($code, 2);
                return DQCode::where('id', $number)->first()->description ?? "Unknown DQ Code";
            }
        }

        return view('public-results.view-dqpen', ['dq' => $judgeSubmission]);
    }
}
