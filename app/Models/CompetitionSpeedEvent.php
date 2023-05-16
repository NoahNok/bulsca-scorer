<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompetitionSpeedEvent extends Model
{
    use HasFactory;

    public function getName()
    {
        return $this->hasOne(SpeedEvent::class, 'id', 'event')->first()->name;
    }

    public function getCompetition()
    {
        return $this->belongsTo(Competition::class, 'competition', 'id');
    }

    public function getTeams()
    {
        return $this->getCompetition->getCompetitionTeams();
    }

    public function getResults()
    {

        $record = 377030;



        $results = null;

        if ($this->getName() == "Swim & Tow") {
            $results = DB::select('SELECT *, RANK() OVER (ORDER BY points DESC) place FROM (SELECT *, IF(disqualification IS NOT NULL, 0, IF(result, (1-((result-record)/(record))) * 1000, 0)) AS points FROM (SELECT *, base_result + penalties*15000 AS result FROM (SELECT *, IF(penalties > 5, "DQ501", base_disqualification) AS disqualification FROM (SELECT sr.id, CONCAT(c.name, " ", ct.team) AS team, se.name as event, cse.record, sr.result AS base_result, ct.st_time*1000 AS st_time, sr.disqualification AS base_disqualification, ((SELECT COUNT(*) FROM speed_result_penalties WHERE speed_result=sr.id) + IF(result > (st_time*1.1*1000), FLOOR((result - (st_time*1.1*1000))/15000), 0)) AS penalties FROM speed_results sr INNER JOIN competition_speed_events cse ON cse.id=sr.event INNER JOIN speed_events se ON cse.event=se.id INNER JOIN competition_teams ct ON ct.id=sr.competition_team INNER JOIN clubs c ON c.id=ct.club WHERE cse.id=? ORDER BY result) AS tbl) AS rs) AS pts) AS final ORDER BY place;', [$this->id]);
        } elseif ($this->getName() == "Rope Throw") {
            //echo "i ran";
            //$results = DB::select('SELECT *, RANK() OVER (ORDER BY points DESC) place FROM (SELECT *, IF( disqualification IS NOT NULL, 0, IF( result_penalties = 0, alpha / 2, IF( result_penalties = 1, alpha + beta / 2, IF( result_penalties = 2, alpha + beta + gamma / 2, IF( result_penalties = 3, alpha + beta + gamma + delta / 2, alpha + beta + gamma + delta + ( 1 -( (result - fastest)/( GREATEST(slowest - fastest, 0) ) ) )*( 1000 -(alpha + beta + gamma + delta) ) ) ) ) ) ) AS points FROM ( WITH tbl AS ( WITH h AS ( SELECT *, cast( IF( penalties > 0, GREATEST( IF( result > 3, 4 - penalties, result - penalties ), 0 ), result ) AS unsigned ) AS result_penalties FROM ( SELECT sr.event AS event_id, sr.id, CONCAT(c.name, " ", ct.team) AS team, se.name as event, sr.result, sr.disqualification, ( SELECT COUNT(*) FROM speed_result_penalties WHERE speed_result = sr.id ) AS penalties, ( 1000 /( SELECT COUNT(*) FROM speed_results WHERE event = cse.id AND disqualification IS NULL ) ) AS pointsPerTeam FROM speed_results sr INNER JOIN competition_speed_events cse ON cse.id = sr.event INNER JOIN speed_events se ON cse.event = se.id INNER JOIN competition_teams ct ON ct.id = sr.competition_team INNER JOIN clubs c ON c.id = ct.club WHERE cse.id = ? ORDER BY result ) AS b ) SELECT *, ( SELECT MAX( cast(result as unsigned) ) FROM h WHERE penalties = 0 ) AS slowest, ( SELECT MIN( cast(result as unsigned) ) FROM h WHERE result > 3 AND penalties = 0 ) AS fastest FROM h ) SELECT *, ( pointsPerTeam * ( SELECT COUNT(*) FROM tbl WHERE result_penalties = 0 AND disqualification IS NULL ) ) AS alpha, ( pointsPerTeam * ( SELECT COUNT(*) FROM tbl WHERE result_penalties = 1 AND disqualification IS NULL ) ) AS beta, ( pointsPerTeam * ( SELECT COUNT(*) FROM tbl WHERE result_penalties = 2 AND disqualification IS NULL ) ) AS gamma, ( pointsPerTeam * ( SELECT COUNT(*) FROM tbl WHERE result_penalties = 3 AND disqualification IS NULL ) ) AS delta FROM tbl ) AS b) AS p;', [$this->id]);
            $result = DB::raw('SELECT *, RANK() OVER (ORDER BY points DESC) place FROM (SELECT *, IF( disqualification IS NOT NULL, 0, IF( result_penalties = 0, alpha / 2, IF( result_penalties = 1, alpha + beta / 2, IF( result_penalties = 2, alpha + beta + gamma / 2, IF( result_penalties = 3, alpha + beta + gamma + delta / 2, IF(slowest=fastest, 1000, alpha + beta + gamma + delta + ( 1 -( (result - fastest)/( GREATEST(slowest - fastest, 0) ) ) )*( 1000 -(alpha + beta + gamma + delta)) ) ) ) ) ) ) AS points FROM ( WITH tbl AS ( WITH h AS ( SELECT *, cast( IF( penalties > 0, GREATEST( IF( result > 3, 4 - penalties, result - penalties ), 0 ), result ) AS unsigned ) AS result_penalties FROM ( SELECT sr.event AS event_id, sr.id, CONCAT(c.name, " ", ct.team) AS team, se.name as event, sr.result, sr.disqualification, ( SELECT COUNT(*) FROM speed_result_penalties WHERE speed_result = sr.id ) AS penalties, ( 1000 /( SELECT COUNT(*) FROM speed_results WHERE event = cse.id AND disqualification IS NULL ) ) AS pointsPerTeam FROM speed_results sr INNER JOIN competition_speed_events cse ON cse.id = sr.event INNER JOIN speed_events se ON cse.event = se.id INNER JOIN competition_teams ct ON ct.id = sr.competition_team INNER JOIN clubs c ON c.id = ct.club WHERE cse.id = ' . $this->id . ' ORDER BY result ) AS b ) SELECT *, ( SELECT MAX( cast(result as unsigned) ) FROM h WHERE penalties = 0 ) AS slowest, ( SELECT MIN( cast(result as unsigned) ) FROM h WHERE result > 3 AND penalties = 0 ) AS fastest FROM h ) SELECT *, ( pointsPerTeam * ( SELECT COUNT(*) FROM tbl WHERE result_penalties = 0 AND disqualification IS NULL ) ) AS alpha, ( pointsPerTeam * ( SELECT COUNT(*) FROM tbl WHERE result_penalties = 1 AND disqualification IS NULL ) ) AS beta, ( pointsPerTeam * ( SELECT COUNT(*) FROM tbl WHERE result_penalties = 2 AND disqualification IS NULL ) ) AS gamma, ( pointsPerTeam * ( SELECT COUNT(*) FROM tbl WHERE result_penalties = 3 AND disqualification IS NULL ) ) AS delta FROM tbl ) AS b) AS p;', [2]);
            $results = DB::select($result);
            //results = DB::select('SELECT *, RANK() OVER (ORDER BY points DESC) place FROM (SELECT *, IF(result_penatlies=0, alpha/2, IF(result_penatlies=1, alpha + beta/2, IF(result_penatlies=2, alpha+beta+gamma/2, IF(result_penatlies=3, alpha+beta+gamma+delta/2, alpha + beta + gamma + delta + (1-((result-fastest)/(GREATEST(slowest-fastest,0))))*(1000-(alpha + beta + gamma + delta)) )))) AS points FROM (SELECT *, IF(penalties>0, GREATEST(IF(result>3, 4 - penalties, result - penalties), 0), result) AS result_penatlies FROM (WITH tbl AS (SELECT sr.event AS event_id, sr.id, CONCAT(c.name, " ", ct.team) AS team, se.name as event, sr.result, (SELECT MAX(cast(result as unsigned)) FROM speed_results WHERE event=cse.id) AS slowest, (SELECT MIN(cast(result as unsigned)) FROM speed_results WHERE event=cse.id AND result>3) AS fastest, sr.disqualification, (SELECT COUNT(*) FROM speed_result_penalties WHERE speed_result=sr.id) AS penalties, (1000/(SELECT COUNT(*) FROM speed_results WHERE event=cse.id AND disqualification IS NULL)) AS pointsPerTeam FROM speed_results sr INNER JOIN competition_speed_events cse ON cse.id=sr.event INNER JOIN speed_events se ON cse.event=se.id INNER JOIN competition_teams ct ON ct.id=sr.competition_team INNER JOIN clubs c ON c.id=ct.club WHERE cse.id=? ORDER BY result) SELECT *, (pointsPerTeam * (SELECT COUNT(*) FROM tbl WHERE result=0)) AS alpha, (pointsPerTeam * (SELECT COUNT(*) FROM tbl WHERE result=1)) AS beta, (pointsPerTeam * (SELECT COUNT(*) FROM tbl WHERE result=2)) AS gamma, (pointsPerTeam * (SELECT COUNT(*) FROM tbl WHERE result=3)) AS delta FROM tbl) AS p) AS tp) AS qr;', [$this->id]);
        } else {
            //echo "i also ran";
            $results = DB::select('SELECT *, RANK() OVER (ORDER BY points DESC) place FROM (SELECT sr.id, CONCAT(c.name, " ", ct.team) AS team, se.name as event, sr.result, sr.disqualification, IF(sr.disqualification IS NOT NULL, 0, IF(sr.result, (1-((sr.result-cse.record)/(cse.record))) * 1000, 0)) AS points FROM speed_results sr INNER JOIN competition_speed_events cse ON cse.id=sr.event INNER JOIN speed_events se ON cse.event=se.id INNER JOIN competition_teams ct ON ct.id=sr.competition_team INNER JOIN clubs c ON c.id=ct.club WHERE cse.id=? ORDER BY result) t ORDER BY place;', [$this->id]);
        }



        return $results;
    }

    public function getSimpleResults()
    {
        return $this->hasMany(SpeedResult::class, 'event', 'id');
    }

    public function hasPenalties()
    {
        return $this->hasOne(SpeedEvent::class, 'id', 'event')->first()->has_penalties;
    }

    public function getResultQuery()
    {
        $results = null;
        if ($this->getName() == "Swim & Tow") {
            $results = 'SELECT *, RANK() OVER (ORDER BY points DESC) place FROM (SELECT *, IF(disqualification IS NOT NULL, 0, IF(result, (1-((result-record)/(record))) * 1000, 0)) AS points FROM (SELECT *, base_result + penalties*15000 AS result FROM (SELECT *, IF(penalties > 5, "DQ501", base_disqualification) AS disqualification FROM (SELECT sr.id, CONCAT(c.name, " ", ct.team) AS team, ct.team AS ctteam, se.name as event, cse.record, sr.result AS base_result, ct.st_time*1000 AS st_time, sr.disqualification AS base_disqualification, ((SELECT COUNT(*) FROM speed_result_penalties WHERE speed_result=sr.id) + IF(result > (st_time*1.1*1000), FLOOR((result - (st_time*1.1*1000))/15000), 0)) AS penalties FROM speed_results sr INNER JOIN competition_speed_events cse ON cse.id=sr.event INNER JOIN speed_events se ON cse.event=se.id INNER JOIN competition_teams ct ON ct.id=sr.competition_team INNER JOIN clubs c ON c.id=ct.club INNER JOIN leagues l on l.id=ct.league WHERE cse.id=? :league_conds: ORDER BY result) AS tbl) AS rs) AS pts) AS final ORDER BY place;';
        } elseif ($this->getName() == "Rope Throw") {

            $results = 'SELECT *, RANK() OVER ( ORDER BY points DESC ) place FROM ( SELECT *, IF( disqualification IS NOT NULL, 0, IF( result_penalties = 0, alpha / 2, IF( result_penalties = 1, alpha + beta / 2, IF( result_penalties = 2, alpha + beta + gamma / 2, IF( result_penalties = 3, alpha + beta + gamma + delta / 2, IF(slowest=fastest, 1000, alpha + beta + gamma + delta + ( 1 -( (result - fastest)/( GREATEST(slowest - fastest, 0) ) ) )*( 1000 -(alpha + beta + gamma + delta) )) ) ) ) ) ) AS points FROM ( WITH tbl AS ( WITH h AS ( SELECT *, cast( IF( penalties > 0, GREATEST( IF( result > 3, 4 - penalties, result - penalties ), 0 ), result ) AS unsigned ) AS result_penalties FROM ( SELECT sr.event AS event_id, sr.id, CONCAT(c.name, " ", ct.team) AS team, ct.team AS ctteam, se.name as event, sr.result, sr.disqualification, ( SELECT COUNT(*) FROM speed_result_penalties WHERE speed_result = sr.id ) AS penalties FROM speed_results sr INNER JOIN competition_speed_events cse ON cse.id = sr.event INNER JOIN speed_events se ON cse.event = se.id INNER JOIN competition_teams ct ON ct.id = sr.competition_team INNER JOIN clubs c ON c.id = ct.club INNER JOIN leagues l on l.id=ct.league WHERE cse.id = ' . $this->id . ' :league_conds: ORDER BY result ) AS b ) SELECT *, ( 1000 /( SELECT COUNT(*) FROM h WHERE disqualification IS NULL  ) ) AS pointsPerTeam, ( SELECT MAX( cast(result as unsigned) ) FROM h WHERE penalties = 0 ) AS slowest, ( SELECT MIN( cast(result as unsigned) ) FROM h WHERE result > 3 AND penalties = 0 ) AS fastest FROM h ) SELECT *, ( pointsPerTeam * ( SELECT COUNT(*) FROM tbl WHERE result_penalties = 0 AND disqualification IS NULL ) ) AS alpha, ( pointsPerTeam * ( SELECT COUNT(*) FROM tbl WHERE result_penalties = 1 AND disqualification IS NULL ) ) AS beta, ( pointsPerTeam * ( SELECT COUNT(*) FROM tbl WHERE result_penalties = 2 AND disqualification IS NULL ) ) AS gamma, ( pointsPerTeam * ( SELECT COUNT(*) FROM tbl WHERE result_penalties = 3 AND disqualification IS NULL ) ) AS delta FROM tbl ) AS b ) AS p;';
        } else {
            $results = 'SELECT *, RANK() OVER (ORDER BY points DESC) place FROM (SELECT sr.id, CONCAT(c.name, " ", ct.team) AS team, ct.team AS ctteam, se.name as event, sr.result, sr.disqualification, IF(sr.disqualification IS NOT NULL, 0, IF(sr.result, (1-((sr.result-cse.record)/(cse.record))) * 1000, 0)) AS points FROM speed_results sr INNER JOIN competition_speed_events cse ON cse.id=sr.event INNER JOIN speed_events se ON cse.event=se.id INNER JOIN competition_teams ct ON ct.id=sr.competition_team INNER JOIN clubs c ON c.id=ct.club INNER JOIN leagues l on l.id=ct.league WHERE cse.id=? :league_conds: ORDER BY result) t ORDER BY place;';
        }

        return str_replace("?", $this->id, $results);
    }

    public function getType()
    {
        return 'speed';
    }
}
