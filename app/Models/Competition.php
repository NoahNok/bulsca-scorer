<?php

namespace App\Models;

use App\Models\DigitalJudge\JudgeLog;
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

    public function getHeatEntries()
    {
        return $this->hasMany(Heat::class, 'competition', 'id');
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

    public function getHeats()
    {
        return $this->hasMany(Heat::class, 'competition', 'id');
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
}
