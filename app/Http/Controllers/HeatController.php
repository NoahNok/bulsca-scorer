<?php

namespace App\Http\Controllers;

use App\Helpers\ScoringHelper;
use App\Jobs\WebPush;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\Heat;
use App\Notifications\General\SercOrderGenerated;
use App\Notifications\General\SercOrderRegenerated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HeatController extends Controller
{

    public function index(Competition $comp)
    {




        $heatEntries = collect(DB::select('SELECT h.id, h.event, h.heat, h.lane, ct.team, l.name AS league, c.name AS club, c.region FROM heats h INNER JOIN competition_teams ct ON ct.id=h.team INNER JOIN leagues l ON l.id=ct.league INNER JOIN clubs c ON c.id=ct.club WHERE h.competition = ? ORDER BY heat, lane;', [$comp->id]));

        //$heatEntries = $comp->getHeatEntries;

        return view('competition.heats-and-orders.index', ['comp' => $comp, 'heatEntries' => $heatEntries]);
    }

    public function createDefaultHeatsForComp(Competition $comp, Request $request)
    {
        ScoringHelper::resolve($comp->scoring_type, 'heat')->generate($comp);


        return redirect()->route('comps.view.heats', $comp);
    }



    public function edit(Competition $comp, Request $request)
    {
        $heatEntries = $comp->getHeatEntries;

        if ($request->input('event', null) != null) {
            $heatEntries = $heatEntries->where('event', $request->input('event'));
        }

        return view('competition.heats-and-orders.heats.edit', ['comp' => $comp, 'heatEntries' => $heatEntries]);
    }

    public function editPost(Competition $comp, Request $request)
    {
        $team = $request->input('team', -1);
        $lane = $request->input('target-lane');
        $heat = $request->input('target-heat');

        $eventId = $request->input('event', null);

        // Check if a team exists as the target location, if so we need to swap teams
        $foundHeat = Heat::where('competition', $comp->id)->where('lane', $lane)->where('heat', $heat)->where('event', $eventId)->first();

        if ($foundHeat == null) {
            // No team at target location simple update
            $theat = Heat::where('team', $team)->where('event', $eventId)->first();
            $theat->lane = $lane;
            $theat->heat = $heat;
            $theat->event = $eventId;
            $theat->save();


            if ($eventId) {
                return redirect()->route('comps.view.heats.edit', ['comp' => $comp, 'event' => $eventId]);
            } else {
                return redirect()->route('comps.view.heats.edit', $comp);
            }
        }

        $theat = Heat::where('team', $team)->where('event', $eventId)->first();
        $toriglane = $theat->lane;
        $torigheat = $theat->heat;

        $theat->lane = $lane;
        $theat->heat = $heat;
        $theat->event = $eventId;
        $theat->save();

        $foundHeat->lane = $toriglane;
        $foundHeat->heat = $torigheat;
        $foundHeat->event = $eventId;
        $foundHeat->save();



        if ($eventId) {
            return redirect()->route('comps.view.heats.edit', ['comp' => $comp, 'event' => $eventId]);
        } else {
            return redirect()->route('comps.view.heats.edit', $comp);
        }
    }


    private function createDefaultSERCorderForComp(Competition $comp)
    {

        if ($comp->scoring_type == 'rlss-nationals') {
            return view('competition.heats-and-orders.serc-order.tank-based', ['comp' => $comp]);
        }


        $index = 1;

        foreach ($comp->getCompetitionTeams->shuffle() as $team) {
            $team->serc_order = $index;
            $team->save();
            $index++;
        }
    }

    public function regenSERCOrder(Competition $comp)
    {
        $ret = $this->createDefaultSERCorderForComp($comp);


        $s = new SercOrderRegenerated($comp);
        WebPush::dispatch($s);


        if ($ret instanceof View) {
            return $ret;
        }

        return redirect()->route('comps.view.heats', $comp);
    }

    public function editSERCOrder(Competition $comp)
    {

        if ($comp->scoring_type == 'rlss-nationals') {
            return view('competition.heats-and-orders.serc-order.tank-based', ['comp' => $comp]);
        }

        return view('competition.heats-and-orders.serc-order.edit', ['comp' => $comp]);
    }

    public function editSERCOrderPost(Competition $comp, Request $request)
    {
        $teamFrom = CompetitionTeam::find($request->input('teamFrom'));
        $teamTo = CompetitionTeam::find($request->input('teamTo'));

        if ($teamFrom->serc_order == 0 || $teamTo->serc_order == 0) {
            $this->createDefaultSERCorderForComp($comp);
        }

        $temp = $teamFrom->serc_order;

        $teamFrom->serc_order = $teamTo->serc_order;
        $teamTo->serc_order = $temp;

        $teamFrom->save();
        $teamTo->save();

        return redirect()->route('comps.view.serc-order.edit', $comp);
    }

    public function editTanksPost(Competition $comp, Request $request)
    {
        $data = $request->json()->all();

        $comp->getCompetitionTeams()->update(['serc_order' => 0, 'serc_tank' => 0]);



        foreach ($data as $ind => $tank) {
            $tankTotal = 0;
            foreach ($tank as $bracket) {
                // Get all teams/competitors for this bracket and assign them this tank and random order
                $bracketId = $bracket['league'];

                $competitors = $comp->getCompetitionTeams()->where('league', $bracketId)->get()->unique('club');

                $competitors->shuffle();



                foreach ($competitors as $competitor) {



                    echo ($competitor);
                    $tankTotal++;

                    $competitor->serc_tank = $ind + 1;
                    $competitor->serc_order = $tankTotal;



                    $competitor->save();
                }
            }
        }



        return response()->json();
    }

    public function swapHeats(Competition $comp, Request $request)
    {
        $first = $request->input('first', -1);
        $second = $request->input('second', -1);
        $eventId = $request->input('event', null);

        if ($first == -1 || $second == -1) {
            return response()->json(['result' => 'error'], 500);
        }


        $firstLanes = Heat::where('competition', $comp->id)->where('heat', $first)->where('event', $eventId)->get();
        $secondLanes = Heat::where('competition', $comp->id)->where('heat', $second)->where('event', $eventId)->get();


        $firstLanes->each(function ($lane) {
            $lane->heat = -3;
            $lane->save();
        });

        $secondLanes->each(function ($lane) use ($first) {
            $lane->heat = $first;
            $lane->save();
        });


        $firstLanes->each(function ($lane) use ($second) {
            $lane->heat = $second;
            $lane->save();
        });

        return response()->json(['result' => 'ok']);
    }

    public function remHeat(Competition $comp, Request $request)
    {
        $heatNo = $request->input('heat', null);
        $eventId = $request->input('event', null);

        if ($comp->scoring_type == 'rlss-nationals') {
            Heat::where('competition', $comp->id)->where('heat', $heatNo)->where('event', $eventId)->delete();

            if ($heatNo % 2 == 0) {
                Heat::where('competition', $comp->id)->where('heat', '>', $heatNo)->whereRaw('MOD(heat, 2) = 0')->where('event', $eventId)->decrement('heat', 2);
            } else {
                Heat::where('competition', $comp->id)->where('heat', '>', $heatNo)->whereRaw('MOD(heat, 2) = 1')->where('event', $eventId)->decrement('heat', 2);
            }
        } else {
            // BULSCA
            Heat::where('competition', $comp->id)->where('heat', $heatNo)->where('event', $eventId)->delete();

            Heat::where('competition', $comp->id)->where('heat', '>', $heatNo)->where('event', $eventId)->decrement('heat');
        }

        return response()->json(['result' => 'ok']);
    }
}
