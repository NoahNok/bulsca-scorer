<?php

namespace App\Importer;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Protection;

use function Laravel\Prompts\alert;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\text;

class Loader
{

    private Spreadsheet $spreadsheet;
    private Command $command;
    private array $teams = [];

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    public function load(string $filePath)
    {

        info('Setup Import for ' . basename($filePath));
        $speedEvents = multiselect('Which speed events would you like to import?', ['Swim&Tow', 'RopeThrow', 'Medley', 'Obstacle', 'Manikin'], ['RopeThrow', 'Swim&Tow'], hint: "Select 'Medley' for Pool Lifesaver.");
        $sercs = multiselect('Which SERCs would you like to import?', ['Dry Incident', 'Wet Incident'], ['Dry Incident', 'Wet Incident']);



        spin(fn () => $this->loadSheet($filePath), 'Loading Spreadsheet');
        spin(fn () => $this->getTeams(), 'Loading Teams');

        $teamCount = count($this->teams);
        info('Loaded ' . $teamCount . ' teams');


        $allSercResults = [];

        if (!empty($sercs)) {
            foreach ($sercs as $serc) {
                $loader = new SercLoader($this->spreadsheet, $serc, $this->command, $teamCount);
                $allSercResults[] = [
                    'event' => $serc,
                    'results' => $loader->load()
                ];
            }
        }

        $allSpeedResults = [];

        if (!empty($speedEvents)) {
            $allSpeedResults = progress("Loading Speed Events", $speedEvents, function ($speedEvent, $progress) use ($teamCount) {
                $progress->label("Loading {$speedEvent}");
                $loader = new SpeedLoader($this->spreadsheet, $speedEvent, $this->command, $teamCount);
                return [
                    'event' => $speedEvent,
                    'results' => $loader->load()
                ];
            });
        }


        return [
            'teams' => $this->teams,
            'sercs' => $allSercResults,
            'speeds' => $allSpeedResults
        ];
    }



    private function loadSheet($filePath)
    {
        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $this->spreadsheet = $reader->load($filePath);
    }

    private function getTeams()
    {
        $setupSheet = $this->spreadsheet->getSheetByName('Set UP');

        $teamStartRow = 7;
        $teamColStart = "D";
        $teamColEnd = "H";

        $hasMoreTeams = true;

        $teams = [];

        while ($hasMoreTeams) {
            $teamRow = $setupSheet->rangeToArray($teamColStart . $teamStartRow . ":" . $teamColEnd . $teamStartRow, null, true, true, false);

            if (empty($teamRow[0][0])) {
                $hasMoreTeams = false;
                continue;
            }

            $teams[] = [
                'club' => $teamRow[0][0],
                'team' => $teamRow[0][1],
                'league' => $teamRow[0][2],
                's&t' => $teamRow[0][3] . ":" . $teamRow[0][4]
            ];

            $teamStartRow++;
        }

        $this->teams = $teams;
    }
}

class SercLoader
{

    private Spreadsheet $spreadsheet;
    private string $sheetName;
    private Command $command;
    private int $totalTeams;

    public function __construct($spreadsheet, $sheetName, $command, $totalTeams)
    {
        $this->spreadsheet = $spreadsheet;
        $this->sheetName = $sheetName;
        $this->command = $command;
        $this->totalTeams = $totalTeams;
    }

    private function prefix()
    {
        return "[" . $this->sheetName . "] ";
    }

    public function gatherJudgeInformation()
    {
        $judges = [];

        alert($this->prefix() . "Judge entry");

        while (true) {
            $name = text($this->prefix() . 'Enter a judge name or leave blank to continue');

            if (empty($name)) {
                break;
            }

            $judges[] = $this->gatherJudge($name);



            info($this->prefix() . 'Judge ' . $name . ' added');
        }

        return $judges;
    }

    private function gatherJudge(string $name)
    {


        return [
            'name' => $name,
            'startCol' => text($this->prefix() . 'Enter the column of the first marking point for ' . $name),
            'length' => text($this->prefix() . 'How many marking points does this judge have? ' . $name),
        ];
    }

    public function load()
    {
        $sheet = $this->spreadsheet->getSheetByName($this->sheetName);



        $mpNameIndex = 18;
        $mpWeightIndex = 19;

        $loadedJudges = [];

        $judges = $this->gatherJudgeInformation();

        foreach ($judges as $judge) {


            $namesStart = $judge['startCol'] . $mpNameIndex;
            $namesEnd = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($judge['startCol']) + $judge['length'] - 1) . $mpNameIndex;

            $weightsStart = $judge['startCol'] . $mpWeightIndex;
            $weightsEnd = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($judge['startCol']) + $judge['length'] - 1) . $mpWeightIndex;



            $mpNames = $sheet->rangeToArray($namesStart . ":" . $namesEnd, null, true, true, false);
            $mpWeights = $sheet->rangeToArray($weightsStart . ":" . $weightsEnd, null, true, true, false);

            $loadedMarkingPoints = [];

            foreach ($mpNames[0] as $index => $mpName) {
                $mpName = trim($mpName);
                $mpWeight = trim($mpWeights[0][$index]);

                if (empty($mpName)) {
                    continue;
                }

                $loadedMarkingPoints[] = [
                    'name' => $mpName,
                    'weight' => $mpWeight
                ];
            }

            $loadedJudges[] = [
                'name' => $judge['name'],
                'markingPoints' => $loadedMarkingPoints
            ];
        }

        // Now that we have the judge info, lets load the team marks where $i is the row number
        $max = 21 + $this->totalTeams;
        $teamCol = "D";
        $allTeamMarks = [];
        for ($i = 21; $i < $max; $i++) {
            $teamName = trim($sheet->getCell($teamCol . $i)->getCalculatedValue());




            $teamMarks = [];

            foreach ($judges as $judge) {
                $judgeName = $judge['name'];

                $judgeResults = $sheet->rangeToArray($judge['startCol'] . $i . ":" . Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($judge['startCol']) + $judge['length'] - 1) . $i, null, true, true, false)[0];

                $teamMarks[] = [
                    'judge' => $judgeName,
                    'results' => $judgeResults
                ];
            }

            $allTeamMarks[] = [
                'team' => $teamName,
                'marks' => $teamMarks
            ];
        }

        return [
            'judges' => $loadedJudges,
            'teamMarks' => $allTeamMarks
        ];
    }
}

class SpeedLoader
{

    private Spreadsheet $spreadsheet;
    private string $sheetName;
    private Command $command;
    private int $totalTeams;

    public function __construct($spreadsheet, $sheetName, $command, $totalTeams)
    {
        $this->spreadsheet = $spreadsheet;
        $this->sheetName = $sheetName;
        $this->command = $command;
        $this->totalTeams = $totalTeams;
    }

    public function load()
    {
        $sheet = $this->spreadsheet->getSheetByName($this->sheetName);


        $dqCol = "J";
        $penCols = $this->sheetName == "Swim&Tow" ? ["K", "L", "M", "N", "O"] : ["L", "M", "N", "O"];

        $teams = [];

        $max = 7 + $this->totalTeams;
        for ($i = 7; $i < $max; $i++) {
            $teamName = trim($sheet->getCell("D" . $i)->getCalculatedValue());

            $cellTimes = $sheet->rangeToArray("F" . $i . ":" . "H" . $i, null, true, true, false)[0];
            $time = $cellTimes[0] . ":" . $cellTimes[1] . "." . $cellTimes[2];


            $dq = trim($sheet->getCell($dqCol . $i)->getCalculatedValue());

            if ($dq == "Finished") {
                $dq = "";
            }
            if (str_starts_with($dq, "DNF")) {


                $splt = explode(" ", $dq);
                $dq = "";

                $time = $splt[2];
            }

            $penalties = [];

            if ($this->sheetName == "Swim&Tow" || $this->sheetName == "RopeThrow") {
                foreach ($penCols as $penCol) {
                    $pen = trim($sheet->getCell($penCol . $i)->getCalculatedValue());

                    if (str_starts_with($pen, "DQ")) {
                        continue;
                    }

                    if (!empty($pen)) {
                        $penalties[] = $pen;
                    }
                }
            }



            $teams[] = [
                'team' => $teamName,
                'time' => $time,
                'dq' => $dq,
                'penalties' => $penalties
            ];
        }

        return $teams;
    }
}
