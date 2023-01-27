<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddSpeedEventRequest;
use App\Models\CompetitionSpeedEvent;
use App\Models\Competition;
use App\Models\Penalty;
use App\Models\SpeedEvent;
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




        $cse->record = SpeedEvent::find($data['event'])->record;
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
            $sr = SpeedResult::find($id);



            if ($row->values->disqualification != "") {
                if (preg_match("/^DQ[0-9]{3}$/", $row->values->disqualification) == 0) {
                    array_push($errors, ["id" => $id, "option" => "disqualification"]);
                    continue;
                } else {
                    $sr->disqualification = $row->values->disqualification;
                }
            } else {
                $sr->disqualification = null;
            }

            if (property_exists($row->values, "penalties")) {

                if ($row->values->penalties != "") {
                    $penaltiesSplit = explode(",", $row->values->penalties);

                    $hasError = false;
                    $valid = [];
                    foreach ($penaltiesSplit as $penalty) {
                        $penalty = trim($penalty);
                        if (preg_match("/^P[0-9]{3}$/", $penalty) == 0) {
                            $hasError = true;
                            break;
                        }
                        array_push($valid, $penalty);
                    }

                    if ($hasError) {
                        array_push($errors, ["id" => $id, "option" => "penalties"]);
                        continue;
                    }

                    Penalty::where('speed_result', $sr->id)->delete();

                    foreach ($valid as $penalty) {
                        $p = new Penalty();
                        $p->speed_result = $sr->id;
                        $p->code = $penalty;
                        $p->save();
                    }
                } else {
                    Penalty::where('speed_result', $sr->id)->delete();
                }
            }






            if ($row->values->result == "") {

                $sr->result = null;
                $sr->save();
                continue;
            }


            if ($event->getName() == "Rope Throw") {
                if (preg_match("/^[0-9]{1,2}:[0-9]{1,2}.[0-9]{3}|[0-3]$/", $row->values->result) == 0) {
                    array_push($errors, ["id" => $id, "option" => "result"]);
                    continue;
                }

                $minSecSplit = explode(":", $row->values->result);

                if (count($minSecSplit) == 1) {
                    $sr->result = $row->values->result;
                    $sr->save();
                    continue;
                }

                $min = $minSecSplit[0];
                $secMillisSplit = explode(".", $minSecSplit[1]);

                $totalMillis = $min * 60000 + $secMillisSplit[0] * 1000 + $secMillisSplit[1];


                $sr->result = $totalMillis;
                $sr->save();
            } else {
                if (preg_match("/^[0-9]{1,2}:[0-9]{1,2}.[0-9]{3}$/", $row->values->result) == 0) {
                    array_push($errors, ["id" => $id, "option" => "result"]);
                    continue;
                }

                $minSecSplit = explode(":", $row->values->result);
                $min = $minSecSplit[0];
                $secMillisSplit = explode(".", $minSecSplit[1]);

                $totalMillis = $min * 60000 + $secMillisSplit[0] * 1000 + $secMillisSplit[1];


                $sr->result = $totalMillis;
                $sr->save();
            }
        }




        if (!empty($errors)) {
            return response()->json($errors, 500);
        }
    }

    public function delete(Competition $comp, CompetitionSpeedEvent $event, Request $request)
    {
        $eid = $request->input('eid');

        if ($eid != $event->id) {
            return;
        }

        $event->delete();

        return redirect()->route('comps.view.events', $comp);
    }
}
