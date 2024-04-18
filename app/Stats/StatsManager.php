<?php

namespace App\Stats;

use App\Models\Club;
use App\Models\Competition;
use App\Models\Stats\ResultStat;
use App\Models\Stats\SercStat;
use App\Models\Stats\SpeedEventStat;
use Illuminate\Support\Facades\DB;

class StatsManager {

    private Competition $competition;

    public function __construct(Competition $competition) {
        $this->competition = $competition;
    }


    public function computeStats() {
        $this->removeOldStats();
        $this->computeSpeedStats();
        $this->computeSercStats();
        $this->computeResultStats();
    }

    private function removeOldStats() {
        SpeedEventStat::where('competition', $this->competition->id)->delete();
        SercStat::where('competition', $this->competition->id)->delete();
        ResultStat::where('competition', $this->competition->id)->delete();
    }

    private function computeSpeedStats() {

        foreach ($this->competition->getSpeedEvents as $event) {
            $results = $event->getResults();

            $data = [];

            foreach ($results as $result) {

                if ($result->result == null) {
                    continue;
                }

                $team = $result->tid;
                $time = $result->result;
                $points = $result->points;
                $place = $result->place;

                $data[] = [
                    'competition' => $this->competition->id,
                    'team' => $team,
                    'event' => $event->event,
                    'time' => $time,
                    'points' => $points,
                    'place' => $place
                ];

            }

            SpeedEventStat::insert($data);

        }

    }

    private function computeSercStats() {

        foreach ($this->competition->getSercs as $event) {
            $results = $event->getResults();

            $data = [];

            foreach ($results as $result) {
                $team = $result->tid;
                $score = $result->score;
                $points = $result->points;
                $place = $result->place;

                $data[] = [
                    'competition' => $this->competition->id,
                    'team' => $team,
                    'event' => $event->id,
                    'score' => $score,
                    'points' => $points,
                    'place' => $place
                ];

            }

            SercStat::insert($data);

        }

    }

    private function computeResultStats() {

        foreach ($this->competition->getResultSchemas as $schema) {

            if ($schema->league == 'F') continue;

            $results = $schema->getResults();

            $data = [];

            foreach ($results as $result) {
                $team = $result->tid;
                $points = $result->totalPoints;
                $place = $result->place;
                
                //dump($result);

                $data[] = [
                    'competition' => $this->competition->id,
                    'team' => $team,
                    'league' => $schema->league,
                    'points' => $points,
                    'place' => $place
                ];

            }

            ResultStat::insert($data);
        }


    }

    static function getStatableClubs() {
        
        return DB::select("SELECT DISTINCT c.name FROM clubs c INNER JOIN competition_teams ct ON c.id=ct.club INNER JOIN competitions cp ON cp.id=ct.competition WHERE cp.results_provisional=false ORDER BY c.name");
    }

    static function getClubFromName(string $clubName): ?Club {
        return Club::where('name', $clubName)->first();
    }

    static function getClubTeams(Club $club) {
        return DB::select("SELECT DISTINCT ct.team FROM competition_teams ct INNER JOIN competitions cp ON cp.id=ct.competition WHERE cp.results_provisional=false AND ct.club=? ORDER BY ct.team", [$club->id]);
    }

    static function getAllTeams(): array {
        return DB::select("SELECT DISTINCT ct.team AS team, c.name FROM competition_teams ct INNER JOIN clubs c ON c.id=ct.club INNER JOIN competitions cp ON cp.id=ct.competition WHERE cp.results_provisional=false ORDER BY c.name, ct.team");
    }


}