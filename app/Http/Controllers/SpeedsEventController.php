<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddSpeedEventRequest;
use App\Models\CompetitionSpeedEvent;
use App\Models\Competition;
use App\Models\SpeedResult;
use Illuminate\Http\Request;

class SpeedsEventController extends Controller
{
    public function add(Competition $comp)
    {
        return view('competition.events.speeds.add', ['comp' => $comp]);
    }

    public function addPost(Competition $comp, AddSpeedEventRequest $request)
    {

        $data = $request->validated();

        $cse = new CompetitionSpeedEvent();
        $cse->event = $data['event'];
        $cse->competition = $comp->id;

        $time = $data['record'];
        $minSecSplit = explode(":", $time);
        $min = $minSecSplit[0];
        $secMillisSplit = explode(".", $minSecSplit[1]);

        $totalMillis = $min * 60000 + $secMillisSplit[0] * 1000 + $secMillisSplit[1];

        $cse->record = $totalMillis;
        $cse->weight = $data['weight'];

        $cse->save();


        // Need to add all teams to this new event
        $allTeams = $comp->getCompetitionTeams;
        foreach ($allTeams as $team) {
            $sr = new SpeedResult();
            $sr->competition_team = $team->id;
            $sr->event = $cse->id;
            $sr->save();
        }




        return redirect()->route('comps.view.events', $comp);
    }

    public function view(Competition $comp, CompetitionSpeedEvent $event)
    {
        return view('competition.events.speeds.view', ['comp' => $comp, 'event' => $event]);
    }

    public function edit(Competition $comp, CompetitionSpeedEvent $event)
    {
        return view('competition.events.speeds.edit', ['comp' => $comp, 'event' => $event]);
    }

    public function updateResults(Competition $comp, CompetitionSpeedEvent $event, Request $request)
    {

        $errors = [];

        $json = json_decode($request->input('data'));
        foreach ($json as $row) {
            $id = $row->id;

            if ($row->values->result == "") {
                $sr = SpeedResult::find($id);
                $sr->result = null;
                $sr->save();
                continue;
            }

            if (preg_match("/^[0-9]{1,2}:[0-9]{1,2}.[0-9]{3}$/", $row->values->result) == 0) {
                array_push($errors, ["id" => $id, "option" => "result"]);
                continue;
            }

            $minSecSplit = explode(":", $row->values->result);
            $min = $minSecSplit[0];
            $secMillisSplit = explode(".", $minSecSplit[1]);

            $totalMillis = $min * 60000 + $secMillisSplit[0] * 1000 + $secMillisSplit[1];

            $sr = SpeedResult::find($id);
            $sr->result = $totalMillis;
            $sr->save();
        }

        if (!empty($errors)) {
            return response()->json($errors, 500);
        }
    }
}
