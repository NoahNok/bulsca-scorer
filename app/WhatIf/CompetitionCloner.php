<?php

namespace App\WhatIf;

use App\Models\Club;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\League;
use App\Models\ResultSchemaEvent;
use App\Models\SERC;
use App\Models\SERCDisqualification;
use App\Models\SERCJudge;
use App\Models\SERCPenalty;
use App\Models\SERCResult;
use App\Models\SpeedEvent;
use App\Models\SpeedResult;
use App\Models\User;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CompetitionCloner
{

    private int $newCompId;

    private $clubIdMap = [];
    private $teamIdMap = [];
    private $speedEventIdMap = [];
    private $competitionSpeedEventIdMap = [];
    private $sercIdMap = [];
    private $leagueIdMap = [];



    public function clone(Competition $comp): int
    {
        $this->newCompId = $comp->clone(['season' => null]);
        $this->cloneClubs();
        $this->cloneLeagues();
        $this->cloneTeams($comp);
        $this->cloneSpeedEvents($comp);
        $this->cloneCompetitionSpeedEvents($comp);
        $this->cloneSercs($comp);
        $this->cloneResultSchemas($comp);


        return $this->newCompId;
    }

    private function cloneClubs()
    {
        foreach (Club::all() as $club) {
            $newClubId = $club->cloneOnce(['name' => $club->name]);
            $this->clubIdMap[$club->id] = $newClubId;
        }
    }

    public function cloneLeagues()
    {
        foreach (League::all() as $league) {
            $newLeagueId = $league->cloneOnce(['name' => $league->name]);
            $this->leagueIdMap[$league->id] = $newLeagueId;
        }
    }

    private function cloneTeams(Competition $comp)
    {
        $teams = $comp->getCompetitionTeams()->get();
        foreach ($teams as $team) {
            $newTeamId = $team->clone(['competition' => $this->newCompId, 'club' => $this->clubIdMap[$team->club], 'league' => $this->leagueIdMap[$team->league]]);
            $this->teamIdMap[$team->id] = $newTeamId;
        }
    }

    public function cloneSpeedEvents(Competition $comp)
    {
        $speedEvents = SpeedEvent::all();
        foreach ($speedEvents as $speedEvent) {
            $newSpeedEventId = $speedEvent->cloneOnce(['name' => $speedEvent->name]);
            $this->speedEventIdMap[$speedEvent->id] = $newSpeedEventId;
        }
    }

    public function cloneCompetitionSpeedEvents(Competition $comp)
    {

        foreach ($comp->getSpeedEvents as $competitionSpeedEvent) {
            $newCompetitionSpeedEventId = $competitionSpeedEvent->clone(['competition' => $this->newCompId, 'event' => $this->speedEventIdMap[$competitionSpeedEvent->event]]);
            $this->competitionSpeedEventIdMap[$competitionSpeedEvent->id] = $newCompetitionSpeedEventId;
            $this->cloneSpeedResults($competitionSpeedEvent->id);
        }
    }

    public function cloneSpeedResults($eventId)
    {
        $cse = CompetitionSpeedEvent::find($eventId);

        foreach ($cse->getSimpleResults as $result) {
            $newId = $result->clone(['event' => $this->competitionSpeedEventIdMap[$result->event], 'competition_team' => $this->teamIdMap[$result->competition_team]]);
            $this->cloneSpeedResultPens($result->id, $newId);
        }
    }

    public function cloneSpeedResultPens($speedResultId, $newSpeedsResultId)
    {

        $sr = SpeedResult::find($speedResultId);

        foreach ($sr->getPenalties as $pen) {
            $pen->clone(['speed_result' => $newSpeedsResultId]);
        }
    }


    public function cloneSercs(Competition $comp)
    {

        foreach ($comp->getSERCs as $serc) {
            $newSercId = $serc->clone(['competition' => $this->newCompId]);
            $this->sercIdMap[$serc->id] = $newSercId;
            $this->cloneSercJudges($serc->id);
            $this->cloneSercDqsAndPens($serc->id);
        }
    }

    public function cloneSercJudges($sercId)
    {

        $serc = SERC::find($sercId);

        foreach ($serc->getJudges as $judge) {
            $newJudgeId = $judge->clone(['serc' => $this->sercIdMap[$sercId]]);
            $this->cloneSercJudgeMarkingPoints($sercId, $judge->id, $newJudgeId);
        }
    }

    public function cloneSercJudgeMarkingPoints($sercId, $judgeId, $newJudgeId)
    {

        $sercJudge = SERCJudge::find($judgeId);

        foreach ($sercJudge->getMarkingPoints as $mp) {
            $newMpId = $mp->clone(['serc' => $this->sercIdMap[$sercId], 'judge' => $newJudgeId]);
            $this->cloneSercResults($mp->id, $newMpId);
        }
    }

    public function cloneSercDqsAndPens($sercId)
    {
        foreach (SERCDisqualification::where('serc', $sercId)->get() as $dq) {
            $dq->clone(['serc' => $this->sercIdMap[$sercId], 'team' => $this->teamIdMap[$dq->team]]);
        }

        foreach (SERCPenalty::where('serc', $sercId)->get() as $pen) {
            $pen->clone(['serc' => $this->sercIdMap[$sercId], 'team' => $this->teamIdMap[$pen->team]]);
        }
    }

    public function cloneSercResults($mpId, $newMpId)
    {

        foreach (SERCResult::where('marking_point', $mpId)->get() as $result) {
            $result->clone(['marking_point' => $newMpId, 'team' => $this->teamIdMap[$result->team]]);
        }
    }

    public function cloneResultSchemas(Competition $comp)
    {
        foreach ($comp->getResultSchemas as $schema) {
            $newSchemaId = $schema->clone(['competition' => $this->newCompId]);
            $this->cloneResultSchemaEvents($schema->id, $newSchemaId);
        }
    }

    public function cloneResultSchemaEvents($schemaId, $newSchemaId)
    {

        foreach (ResultSchemaEvent::where('schema', $schemaId)->get() as $event) {

            $newEventId = str_ends_with($event->event_type, 'SpeedEvent') ? $this->competitionSpeedEventIdMap[$event->event_id] : $this->sercIdMap[$event->event_id];


            $event->clone(['schema' => $newSchemaId, 'event_id' => $newEventId]);
        }
    }
}
