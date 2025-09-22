<?php

namespace App\Models;

use App\Mail\CompetitionAccountCreated;
use App\Mail\CompetitionAccountInvite;
use App\Models\Competition\CompetitionScoringSettings;
use App\Models\DigitalJudge\JudgeLog;
use App\Models\Interfaces\IInvitable;
use App\Models\Organisation\Organisation;
use App\Stats\StatsManager;
use App\Traits\Cloneable;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class Competition extends Model implements IInvitable
{
    use HasFactory, Cloneable;

    public static $accessTypes = [
        'admin' => 'Admin',
        'view' => 'Overview',
        'teams' => 'Teams/Competitors',
        'heats_and_draws' => 'Heats and Draws',
        'printables' => 'Printables',
        'serc' => 'SERCs',
        'speed' => 'Speeds',
        'results' => 'Results',
        'serc_writer' => 'SERC Writer',
    ];

    protected $casts = [
        'when' => 'datetime',
        'serc_start_time' => 'datetime',
        'data' => 'array'
    ];


    public function getSlug()
    {
        return str_replace(' ', '-', $this->name) . "." . $this->id;
    }

    public function resolveRouteBinding($value, $field = null)
    {

        if (!is_numeric($value)) {
            $arr = explode('.', $value);
            $value = end($arr);
        }

        return parent::resolveRouteBinding($value, $field);
    }

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

    public function getLeagues()
    {
        return $this->hasMany(League::class, 'competition', 'id');
    }

    public function getCompetitionTeams()
    {
        return $this->hasMany(CompetitionTeam::class, 'competition', 'id');
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

    public function getHeats(int|null $eventId = null)
    {

        if ($eventId == null) {
            return collect(DB::select('SELECT h.id, h.heat, h.lane, ct.team, l.name AS league, c.name AS club, c.region FROM heats h INNER JOIN competition_teams ct ON ct.id=h.team INNER JOIN leagues l ON l.id=ct.league INNER JOIN clubs c ON c.id=ct.club WHERE h.competition = ? AND h.event IS NULL ORDER BY heat, lane;', [$this->id]));
        }

        return collect(DB::select('SELECT h.id, h.heat, h.lane, ct.team, l.name AS league, c.name AS club, c.region FROM heats h INNER JOIN competition_teams ct ON ct.id=h.team INNER JOIN leagues l ON l.id=ct.league INNER JOIN clubs c ON c.id=ct.club WHERE h.competition = ? AND h.event = ? ORDER BY heat, lane;', [$this->id, $eventId]));
    }

    public function getTotalDQs()
    {
        $speedDQs = DB::select("SELECT COUNT('disqualification') AS total FROM speed_results sr INNER JOIN competition_speed_events cse ON cse.id=sr.event WHERE sr.disqualification IS NOT NULL AND cse.competition=?", [$this->id])[0]->total;
        $sercDQs = DB::select("SELECT COUNT('code') AS total FROM serc_disqualifications sd INNER JOIN sercs s ON s.id=sd.serc WHERE s.competition=?", [$this->id])[0]->total;



        return ["speed" => $speedDQs, "serc" => $sercDQs, "total" => $speedDQs + $sercDQs];
    }



    public function getTotalPens()
    {
        $speedPens = DB::select("SELECT code FROM speed_result_penalties p INNER JOIN speed_results sr ON sr.id=p.speed_result INNER JOIN competition_speed_events cse ON cse.id=sr.event WHERE cse.competition=?", [$this->id]);
        $sercPens = DB::select("SELECT codes FROM serc_penalties sp INNER JOIN sercs s ON s.id=sp.serc WHERE s.competition=?", [$this->id]);


        $speedTotal = 0;
        foreach ($speedPens as $pen) {
            $speedTotal += count(explode(',', $pen->code));
        }

        $sercTotal = 0;
        foreach ($sercPens as $pen) {
            $sercTotal += count(explode(',', $pen->codes));
        }




        return ["speed" => $speedTotal, "serc" => $sercTotal, "total" => $speedTotal + $sercTotal];
    }

    public function getEventsInDQFormat()
    {


        $events = [];

        $this->getSpeedEvents()->each(function ($event) use (&$events) {
            $events['sp:' . $event->id] = $event->getName();
        });

        $this->getSERCs()->each(function ($event) use (&$events) {
            $events['se:' . $event->id] = $event->name;
        });

        return $events;
    }


    public function canUser(User $user, array|string $access_to): bool
    {
        // If the user is the owner of the competition or an admin, they have access
        if ($user->competition == $this->id || $user->isAdmin()) {

            return true;
        }

        if (is_string($access_to)) {
            $access_to = [$access_to];
        }

        // If access_to is an array, check if the user has any of the specified access types
        $access = UserCompetitionAccess::where('user', $user->id)
            ->where('competition', $this->id)
            ->get();

        // Check if user has admin access
        if ($access->contains('access_to', 'admin') || $access->contains('access_to', 'owner')) {

            return true;
        }

        // Check if user has any of the specified access types
        foreach ($access as $a) {
            if (in_array($a->access_to, $access_to)) {

                return true;
            }
        }


        // If no access found, return false

        return false;
    }

    public function userBelongsToCompetition(User $user): bool
    {
        $access = UserCompetitionAccess::where('user', $user->id)
            ->where('competition', $this->id)
            ->first();

        return $access !== null;
    }



    public function addAccount(User $account, string|array $access_to = 'view')
    {

        if (is_string($access_to)) {
            $access_to = [$access_to];
        }

        if (in_array('admin', $access_to)) {
            # If given admin it will auto apply all others
            $access_to = ['admin'];
        }

        // Remove any existing access for this user in this competition
        UserCompetitionAccess::where('user', $account->id)
            ->where('competition', $this->id)
            ->delete();

        // Create an access record for each access type
        foreach ($access_to as $accessType) {
            $access = new UserCompetitionAccess();
            $access->user = $account->id;
            $access->competition = $this->id;
            $access->access_to = $accessType;
            $access->save();
        }

        // Send email
        $accessDisplay = collect($access_to)->map(function ($type) {
            return self::$accessTypes[$type] ?? $type;
        })->toArray();

        if (in_array('owner', $access_to)) {
            return;
        }
    }

    public function editCompetitionAccount(User $account, array $access_to)
    {
        if (!$this->userBelongsToCompetition($account)) {
            return "Account does not belong to this competition.";
        }

        if (in_array('admin', $access_to)) {
            # If given admin it will auto apply all others
            $access_to = ['admin'];
        }

        // Remove all existing access for this user in this competition
        UserCompetitionAccess::where('user', $account->id)
            ->where('competition', $this->id)
            ->delete();

        // Add new access
        foreach ($access_to as $accessType) {
            $access = new UserCompetitionAccess();
            $access->user = $account->id;
            $access->competition = $this->id;
            $access->access_to = $accessType;
            $access->save();
        }
    }

    public function deleteCompetitionAccount(User $account)
    {
        if (!$this->userBelongsToCompetition($account)) {
            return "Account does not belong to this competition.";
        }

        // Remove all access for this user in this competition
        UserCompetitionAccess::where('user', $account->id)
            ->where('competition', $this->id)
            ->delete();

        // If the user has no other competitions, delete the user
        if (UserCompetitionAccess::where('user', $account->id)->count() == 0) {
            $account->delete();
        }
    }

    public function getOrganisation()
    {
        return $this->belongsTo(Organisation::class, 'organisation');
    }

    public function getScoringSettings()
    {
        return $this->hasOne(CompetitionScoringSettings::class, 'competition');
    }

    public function drawTemplate()
    {
        return $this->getScoringSettings->use_tanks ? 'competition.heats-and-orders.serc_list_templates.tanks' : 'competition.heats-and-orders.serc_list_templates.single';
    }

    public function getInvites()
    {
        return $this->morphMany(AccountInvite::class, 'to');
    }

    public function applyInvite(AccountInvite $invite)
    {

        $user = $invite->getUser();

        if (!$user) {
            throw new Exception("Expected loggedin user during applyInvite");
        }

        $details = $invite->details;

        if (!array_key_exists('access', $details)) {
            throw new Exception("Organisation invite missing 'access' details");
        }

        $this->addAccount($user, $details['access']);

        return redirect()->route('comps.view', $this->id);
    }
}
