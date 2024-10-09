<?php

namespace App\Models;

use App\Models\Brands\Brand;
use App\Models\DigitalJudge\JudgeLog;
use App\Stats\StatsManager;
use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Competition extends Model
{
    use HasFactory, Cloneable;

    protected $casts = [
        'when' => 'datetime',
        'serc_start_time' => 'datetime',
    ];

    public function getSpeedEvents()
    {
        return $this->hasMany(CompetitionSpeedEvent::class, 'competition', 'id');
        //return $this->hasMany(CompetitionSpeedEvent::class, 'event', 'id');
    }

    public function getSERCs()
    {
        return $this->hasMany(SERC::class, 'competition', 'id');
        //return $this->hasMany(CompetitionSpeedEvent::class, 'event', 'id');
    }

    public function getCompetitionTeams()
    {
        return $this->hasMany(CompetitionTeam::class, 'competition', 'id')->orderBy('serc_order');
    }

    public function getResultSchemas()
    {
        return $this->hasMany(ResultSchema::class, 'competition', 'id');
    }

    public function getAllEvents()
    {
        $sercs = $this->getSERCs()->get();
        $speeds = $this->getSpeedEvents()->get();

        $merged = $sercs->merge($speeds);

        return $merged;
    }

    public function getUser()
    {
        return $this->hasOne(User::class, 'competition', 'id');
    }

    public function areResultsPublic()
    {

        return $this->public_results;
    }


    public function areResultsProvisional()
    {

        return $this->results_provisional;
    }


    public function resultsSlug()
    {

        return Str::lower(str_replace(" ", "-", $this->name)) . "." . $this->id;
    }

    public function needsToRegenerateSERCDraw(): bool
    {

        if ($this->scoring_type == 'rlss-nationals') {
            $tanks = $this->getCompetitionTeams->unique('serc_tank')->pluck('serc_tank')->toArray();
            return count($tanks) == 1 && $tanks[0] == '0';
        }

        return $this->getCompetitionTeams()->where('serc_order', 0)->exists();
    }

    public function getMaxHeats(): int
    {
        return $this->getHeatEntries->max('heat') ?: -1;
    }

    public function getMaxLanes(): int
    {
        return $this->max_lanes;
    }

    public function getSeason()
    {
        return $this->hasOne(Season::class, 'id', 'season');
    }

    public function getHeatEntries()
    {
        return $this->hasMany(Heat::class, 'competition', 'id');
    }

    public function getBrand()
    {
        return $this->hasOne(Brand::class, 'id', 'brand');
    }



    public function howManySercsHasEachTeamFinished()
    {

        // This is much faster than doing it via ORM models. Reduce to one query instead of sercs * teams
        $res = DB::select('SELECT sr.team AS team, COUNT(DISTINCT smp.serc) AS total FROM serc_results sr INNER JOIN serc_marking_points smp ON smp.id=sr.marking_point INNER JOIN sercs s ON s.id=smp.serc WHERE s.competition=? GROUP BY sr.team;', [$this->id]);

        $teamsFinished = [];

        foreach ($res as $row) {
            $teamsFinished[$row->team] = $row->total;
        }

        return $teamsFinished;
    }

    public function whichSpeedEventHeatsHaveFinished()
    {

        // This is much faster than doing it via ORM models. Reduce to one query instead of sercs * teams
        $res = DB::select('SELECT heat, COUNT(DISTINCT sr.event) AS done FROM speed_results sr INNER JOIN heats h ON sr.competition_team=h.team WHERE competition=? AND result IS NOT NULL GROUP BY heat;', [$this->id]);

        $heatsFinished = [];

        foreach ($res as $row) {
            $heatsFinished[$row->heat] = $row->done;
        }

        return $heatsFinished;
    }

    public function resolveJudgeLogVersionUrl()
    {
        return JudgeLog::where('competition', $this->id)->exists() ? route('dj.judgeLog', $this) : route('dj.betterJudgeLog', $this);
    }

    public function generateStats()
    {
        $manager = new StatsManager($this);
        $manager->computeStats();
    }

    public function getCompetitorsPerLeague()
    {
        return DB::select('WITH totals AS (SELECT league, COUNT(*) as count FROM competition_teams WHERE competition=? GROUP BY league) SELECT t.league, l.name, t.count FROM totals t INNER JOIN leagues l ON l.id=t.league', [$this->id]);
    }

    public function getTanks()
    {

        $data = collect(DB::select('WITH totals AS (SELECT serc_tank, league, COUNT(*) AS count FROM competition_teams WHERE competition=? AND serc_tank>0 GROUP BY league, serc_tank ORDER BY serc_tank) SELECT t.league, l.name, t.count, t.serc_tank FROM totals t INNER JOIN leagues l ON l.id=t.league', [$this->id]));
        $return = [];

        foreach ($data->groupBy('serc_tank') as $group) {
            $return[] = $group;
        }

        return $return;
    }

    // Like above but for just simple listing of names
    public function getSercTanks()
    {

        if ($this->scoring_type == 'rlss-nationals') {
            return collect(DB::select('SELECT ct.team, ct.id AS tid, l.name AS league, c.name AS club, c.region, ct.serc_tank, ct.serc_order FROM competition_teams ct INNER JOIN clubs c ON c.id=ct.club INNER JOIN leagues l ON l.id=ct.league WHERE competition=? AND serc_tank > 0 ORDER BY serc_tank, serc_order;', [$this->id]));
        } else {
            return collect(DB::select('SELECT ct.team, ct.id AS tid, l.name AS league, c.name AS club, c.region, ct.serc_tank, ct.serc_order FROM competition_teams ct INNER JOIN clubs c ON c.id=ct.club INNER JOIN leagues l ON l.id=ct.league WHERE competition=? ORDER BY serc_tank, serc_order;', [$this->id]));
        }
    }

    public function getHeats()
    {
        return collect(DB::select('SELECT h.id, h.heat, h.lane, ct.team, l.name AS league, c.name AS club, c.region FROM heats h INNER JOIN competition_teams ct ON ct.id=h.team INNER JOIN leagues l ON l.id=ct.league INNER JOIN clubs c ON c.id=ct.club WHERE h.competition = ? ORDER BY heat, lane;', [$this->id]));
    }
}
