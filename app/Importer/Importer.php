<?php

namespace App\Importer;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\League;
use App\Models\Penalty;
use App\Models\SERC;
use App\Models\SpeedEvent;

use function Laravel\Prompts\select;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;

class Importer
{

    private Competition $competition;

    private $teamNameIdMap = [];

    public function import($competitionDetails, $teams, $speedsData, $sercData)
    {

        $this->competition = new Competition();
        $this->competition->name = $competitionDetails['name'];
        $this->competition->when = $competitionDetails['when'];
        $this->competition->max_lanes = 8;
        $this->competition->save();


        spin(fn () => $this->importTeams($teams), 'Importing Teams');

        spin(fn () => $this->importSpeeds($speedsData), 'Importing Speed Events');

        spin(fn () => $this->importSercs($sercData), 'Importing SERCs');

        info("Import Complete");

        return $this->competition;
    }

    private function importTeams($teams)
    {

        foreach ($teams as $team) {
            $club = \App\Models\Club::firstOrCreate(['name' => $team['club']]);

            $ct = new CompetitionTeam();
            $ct->competition = $this->competition->id;
            $ct->club = $club->id;
            $ct->team = $team['team'];
            $ct->league = League::where('name', $team['league'])->first()->id;

            $timeParts = explode(":", $team['s&t']);
            $seconds = $timeParts[0] * 60 + $timeParts[1];
            $ct->st_time = $seconds;

            $ct->save();

            $this->teamNameIdMap[$team['club'] . " " . $team['team']] = $ct->id;
        }
    }

    private function importSpeeds($speedsData)
    {

        foreach ($speedsData as $speed) {
            $this->importSpeed($speed['event'], $speed['results']);
        }
    }


    private function getSpeedEventFromName($eventName): SpeedEvent
    {

        if ($eventName === 'Swim&Tow') {
            return SpeedEvent::where('name', 'Swim & Tow')->first();
        } else if ($eventName === 'RopeThrow') {
            return SpeedEvent::where('name', 'Rope Throw')->first();
        } else if ($eventName === 'Medley') {

            $actual = select('Which Medley Relay?', ['Medley', 'Pool Lifesaver'], 'Medley Relay');

            return SpeedEvent::where('name', $actual)->first();
        } else if ($eventName === 'Obstacle') {
            return SpeedEvent::where('name', 'Obstacle')->first();
        } else if ($eventName === 'Manikin') {
            return SpeedEvent::where('name', 'Manikin')->first();
        } else {
            return null; # Turn this into an option select
        }
    }


    private function importSpeed($eventName, $speedData)
    {
        $speedEvent = $this->getSpeedEventFromName($eventName);

        $cse = new CompetitionSpeedEvent();
        $cse->competition = $this->competition->id;
        $cse->event = $speedEvent->id;



        $minSecSplit = explode(":", text("What was the record for {$speedEvent->name}?"));
        $min = $minSecSplit[0];
        $secMillisSplit = explode(".", $minSecSplit[1]);

        $totalMillis = $min * 60000 + $secMillisSplit[0] * 1000 + $secMillisSplit[1];


        $cse->record = $totalMillis;

        $cse->save();

        foreach ($speedData as $speedResult) {
            $ct = $this->teamNameIdMap[$speedResult['team']];

            $sr = new \App\Models\SpeedResult();
            $sr->disableLogging();
            $sr->competition_team = $ct;
            $sr->event = $cse->id;

            $minSecSplit = explode(":", $speedResult['time']);

            if (count($minSecSplit) == 1) {
                $sr->result = $speedResult['time'];
            } else {

                $min = $minSecSplit[0];
                $secMillisSplit = explode(".", $minSecSplit[1]);

                $totalMillis = $min * 60000 + $secMillisSplit[0] * 1000 + ($secMillisSplit[1] * 10);


                $sr->result = $totalMillis;
            }

            $sr->disqualification = empty($speedResult['dq']) ? null : $speedResult['dq'];
            $sr->save();

            if ($speedEvent->has_penalties) {

                foreach ($speedResult['penalties'] as $penalty) {
                    $srp = new Penalty();
                    $srp->speed_result = $sr->id;
                    $srp->code = $penalty;
                    $srp->save();
                }
            }
        }
    }

    private function importSercs($sercsData)
    {
        foreach ($sercsData as $serc) {
            $this->importSerc($serc['event'], $serc['results']);
        }
    }

    private function importSerc($sercName, $sercData)
    {

        $serc = new SERC();
        $serc->competition = $this->competition->id;
        $serc->name = $sercName;
        $serc->save();

        $mpIds = [];

        foreach ($sercData['judges'] as $judge) {
            $sercJudge = new \App\Models\SERCJudge();
            $sercJudge->serc = $serc->id;
            $sercJudge->name = $judge['name'];
            $sercJudge->save();

            $mpIds[$judge['name']] = [];

            foreach ($judge['markingPoints'] as $markingPoint) {
                $sercMP = new \App\Models\SERCMarkingPoint();
                $sercMP->judge = $sercJudge->id;
                $sercMP->name = $markingPoint['name'];
                $sercMP->weight = $markingPoint['weight'];
                $sercMP->serc = $serc->id;
                $sercMP->save();

                $mpIds[$judge['name']][] = $sercMP->id;
            }
        }


        foreach ($sercData['teamMarks'] as $teamMark) {
            $ct = $this->teamNameIdMap[$teamMark['team']];

            foreach ($teamMark['marks'] as $mark) {
                $judgeName = $mark['judge'];

                foreach ($mark['results'] as $index => $result) {
                    $sercResult = new \App\Models\SERCResult();
                    $sercResult->team = $ct;
                    $sercResult->marking_point = $mpIds[$judgeName][$index];
                    $sercResult->result = $result;
                    $sercResult->disableLogging();
                    $sercResult->save();
                }
            }
        }
    }
}
