<?php

namespace App\Models;

use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ResultSchema extends Model
{
    use HasFactory, Cloneable;

    public function getEvents()
    {
        return $this->hasMany(ResultSchemaEvent::class, 'schema', 'id');
    }

    public function getCompetition()
    {
        return $this->hasOne(Competition::class, 'id', 'competition');
    }

    public function getTargetLeagueQueryExtra()
    {

        switch (Str::lower($this->league)) {
            case "o":
                return "";

            case "a":
                return " AND ct.team = 'A' AND (l.name = 'S' OR l.name = 'F') ";

            case "b":
                return " AND ct.team !='A' AND l.name = 'S' ";

            case "f":
                return " AND l.name = 'F' ";

            case "nc":
                return " AND l.name ='NC' ";

            case "ns":
                return " AND l.name = 'NS' "; // Will probably never get used

            case "ob":
                return " AND l.name = 'OB' "; // Will probably never get used

            default:
                return "";
        }
    }

    /**
     * I'm aware that this looks like alot of sphagetti
     * but it works well and it quite fast, and it the best way I could think of doing it
     * 
     * Doing it with ORM would be absolutely ass
     */
    public function getRawQuery(): ?string
    {
        $events = $this->getEvents;
        $targetLeagueQueryExtra = $this->getTargetLeagueQueryExtra();

        $finalQuery = "WITH ";

        $mysqlEventNames = [];
        $mysqlEventNamesArray = [];

        // Buildup CTE's with each events result query
        foreach ($events as $event) {
            $actualEvent = $event->getActualEvent;

            // If you remove an event from a created RS it gets a null, so skip it
            if (!$actualEvent) continue;

            $query = $actualEvent->getResultQuery();
            $query = str_replace(":league_conds:", $targetLeagueQueryExtra, $query);
            $eventName = $actualEvent->getName();

            $eventMysqlName = str_replace("&", "", str_replace(" ", "_", Str::lower($eventName))) . "_" . $actualEvent->id;

            $mysqlEventNames[$event->id] = $eventMysqlName;
            array_push($mysqlEventNamesArray, $eventMysqlName);

            $finalQuery .= $eventMysqlName . " AS (" . rtrim($query, ";") . "), ";
        }


        if (count($mysqlEventNamesArray) == 0) return null;


        $finalQuery = rtrim($finalQuery, ", ");
        $finalQuery .= " SELECT " . $mysqlEventNamesArray[0] . ".team, " . $mysqlEventNamesArray[0] . ".tid, " . $mysqlEventNamesArray[0] . ".club, ";

        // Buildup linear redistirbution of points
        foreach ($events as $event) {
            // If you remove an event from a created RS it gets a null, so skip it
            if (!array_key_exists($event->id, $mysqlEventNames)) continue;
            $mysqlTableName = $mysqlEventNames[$event->id];
            $finalQuery .= $mysqlTableName . ".points AS " . $mysqlTableName . "_points, ";
            $finalQuery .=  $event->weight . " AS " . $mysqlTableName . "_weight, ";
            $finalQuery .= "(SELECT MIN(points) FROM " . $mysqlTableName . " WHERE " . $mysqlTableName . ".disqualification IS NULL ) AS " . $mysqlTableName . "_min, ";
            $finalQuery .= "(SELECT MAX(points) FROM " . $mysqlTableName . ") AS " . $mysqlTableName . "_max, ";
            $finalQuery .= "900/((SELECT MAX(points) FROM " . $mysqlTableName . ") - (SELECT MIN(points) FROM " . $mysqlTableName . " WHERE " . $mysqlTableName . ".disqualification IS NULL)) AS " . $mysqlTableName . "_mult_factor, ";
            $finalQuery .=  "IF(" . $mysqlTableName . ".points = 0,IF(" . $mysqlTableName . ".disqualification IS NULL, 100, 0)   ,(" . $mysqlTableName . ".points" . "-" . "(SELECT MIN(points) FROM " . $mysqlTableName . " WHERE " . $mysqlTableName . ".disqualification IS NULL))" . "*" . "(900/((SELECT MAX(points) FROM " . $mysqlTableName . ") - (SELECT MIN(points) FROM " . $mysqlTableName . " WHERE " . $mysqlTableName . ".disqualification IS NULL)))+100) * " . $event->weight . " AS " . $mysqlTableName . "_rsp, ";
        }

        $finalQuery = rtrim($finalQuery, ", ");

        $finalQuery .= " FROM " . $mysqlEventNamesArray[0];

        $first = true;

        $prev = $mysqlEventNamesArray[0];
        // Can't remember what this is doing
        foreach ($mysqlEventNamesArray as $event) {
            if ($first) {
                $first = false;
                continue;
            }

            $finalQuery .= " INNER JOIN " . $event . " ON " . $event . ".team=" . $prev . ".team ";
            $prev = $event;
        }

        $finalQuery = rtrim($finalQuery, " ");

        // Calculate totals
        $final = "SELECT *, ";
        foreach ($mysqlEventNamesArray as $event) {
            $final .= $event . "_rsp + ";
        }

        $final = rtrim($final, "+ ") . " AS totalPoints, ";

        // Calculate places for each individual event
        foreach ($mysqlEventNamesArray as $event) {
            $final .= "RANK() OVER (ORDER BY " . $event . "_rsp DESC) AS " . $event . "_rsp_places, ";
        }

        $final = rtrim($final, ", ")  . " FROM (" . $finalQuery . ") AS bb";

        // Calculate overall place
        $final = "SELECT *, RANK() OVER(ORDER BY totalPoints DESC) place FROM (" . $final . ") AS bbb;";
        return $final;
    }

    public function getResults()
    {
        $query = $this->getRawQuery();
        if (!$query) return null;
        return DB::select($query);
    }

}
