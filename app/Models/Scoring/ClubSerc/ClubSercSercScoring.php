<?php

namespace App\Models\Scoring\ClubSerc;

use App\Models\Interfaces\IEvent;
use App\Models\Scoring\IScoring;
use Illuminate\Support\Facades\DB;

class ClubSercSercScoring implements IScoring
{
    public function getResults(IEvent $event): array
    {

        if (request()->has('league')) {
            return DB::select("SELECT *, RANK() OVER (ORDER BY points DESC) place, (SELECT code FROM serc_disqualifications WHERE serc=? AND team=tid LIMIT 1) AS disqualification FROM (SELECT *, IF(EXISTS (SELECT * FROM serc_disqualifications WHERE serc=? AND team=tid) , 0, (score/max)*1000) AS points FROM (WITH tbl AS (SELECT CONCAT(c.name, ' ', ct.team) AS team, sr.team AS tid, ct.club AS club, SUM(result*weight) as score FROM serc_results sr INNER JOIN serc_marking_points mp ON marking_point=mp.id INNER JOIN competition_teams ct ON ct.id=sr.team INNER JOIN clubs c ON c.id=ct.club WHERE mp.serc=? AND ct.league=? GROUP BY team, tid) SELECT *, (SELECT MAX(score) FROM tbl) AS max FROM tbl) AS t) AS f;", [$event->id, $event->id, $event->id, request()->get('league')]);
        }

        $results = DB::select("SELECT *, RANK() OVER (ORDER BY points DESC) place, (SELECT code FROM serc_disqualifications WHERE serc=? AND team=tid LIMIT 1) AS disqualification FROM (SELECT *, IF(EXISTS (SELECT * FROM serc_disqualifications WHERE serc=? AND team=tid) , 0, (score/max)*1000) AS points FROM (WITH tbl AS (SELECT CONCAT(c.name, ' ', ct.team) AS team, sr.team AS tid, ct.club AS club, SUM(result*weight) as score FROM serc_results sr INNER JOIN serc_marking_points mp ON marking_point=mp.id INNER JOIN competition_teams ct ON ct.id=sr.team INNER JOIN clubs c ON c.id=ct.club WHERE mp.serc=? GROUP BY team, tid) SELECT *, (SELECT MAX(score) FROM tbl) AS max FROM tbl) AS t) AS f;", [$event->id, $event->id, $event->id]);

        return $results;
    }

    public function getResultQuery(IEvent $event): string
    {
        return str_replace("?", $event->id, "SELECT *, RANK() OVER (ORDER BY points DESC) place FROM (SELECT *, (SELECT id FROM serc_disqualifications WHERE serc=? AND team=tid ) AS disqualification, IF(EXISTS (SELECT * FROM serc_disqualifications WHERE serc=? AND team=tid) , 0, (score/max)*1000) AS points FROM (WITH tbl AS (SELECT CONCAT(c.name, ' ', ct.team) AS team, sr.team AS tid, ct.club AS club, SUM(result*weight) as score FROM serc_results sr INNER JOIN serc_marking_points mp ON marking_point=mp.id INNER JOIN competition_teams ct ON ct.id=sr.team INNER JOIN clubs c ON c.id=ct.club INNER JOIN leagues l on l.id=ct.league WHERE mp.serc=? :league_conds: GROUP BY team, tid) SELECT *, (SELECT MAX(score) FROM tbl) AS max FROM tbl) AS t) AS f;");
    }
}
