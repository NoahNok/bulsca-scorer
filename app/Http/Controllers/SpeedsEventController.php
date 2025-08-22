<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddSpeedEventRequest;
use App\Http\Requests\Event\UpdateScoringSettings;
use App\Models\CompetitionSpeedEvent;
use App\Models\Competition;
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
            $sr->entity()->associate($team);
            $sr->event = $cse->id;
            $sr->save();
        }




        return redirect()->route('comps.events', $comp);
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
                if (preg_match("/^DQ[0-9]{1,4}$/", $row->values->disqualification) == 0) {
                    array_push($errors, ["id" => $id, "option" => "disqualification"]);
                    continue;
                } else {
                    $event->clearEntityDisqualifications($sr->entity);
                    $code = (int) str_replace('dq', '', strtolower($row->values->disqualification));

                    $event->addEntityDisqualification($sr->entity, $code);
                }
            } else {
                $event->clearEntityDisqualifications($sr->entity);
            }

            if (property_exists($row->values, "penalties")) {

                if ($row->values->penalties != "") {
                    $penaltiesSplit = explode(",", $row->values->penalties);

                    $hasError = false;
                    $valid = [];
                    foreach ($penaltiesSplit as $penalty) {
                        $penalty = trim($penalty);
                        if (preg_match("/^P[0-9]{1,3}$/", $penalty) == 0) {
                            $hasError = true;
                            break;
                        }
                        array_push($valid, $penalty);
                    }

                    if ($hasError) {
                        array_push($errors, ["id" => $id, "option" => "penalties"]);
                        continue;
                    }

                    $event->clearEntityPenalties($sr->entity);

                    foreach ($valid as $penalty) {
                        $code = (int) str_replace('p', '', strtolower($penalty));
                        $event->addEntityPenalty($sr->entity, $code);
                    }
                } else {
                    $event->clearEntityPenalties($sr->entity);
                }
            }


            if ($row->values->result == "") {

                $sr->result = null;
                $sr->save();
                continue;
            }


            if (str_starts_with($row->values->result, 'DN')) {
                $code = str_starts_with($row->values->result, 'DNS') ? 004 : 015;
                $event->addEntityDisqualification($sr->entity, $code);
                continue;
            }

            if (str_starts_with($row->values->result, 'OOT')) {
                $event->addEntityDisqualification($sr->entity, 1001);
                continue;
            }


            if ($event->getName() == "Rope Throw") {
                if (preg_match("/^[0-9]{1,2}:[0-9]{1,2}.[0-9]{2}|[0-3]$/", $row->values->result) == 0) {
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

                if (strlen($secMillisSplit[1]) == 2) {
                    $secMillisSplit[1] = $secMillisSplit[1] * 10;
                }

                $totalMillis = $min * 60000 + $secMillisSplit[0] * 1000 + $secMillisSplit[1];


                $sr->result = $totalMillis;
                $sr->save();
            } else {
                if (preg_match("/^[0-9]{1,2}:[0-9]{1,2}.[0-9]{2}$/", $row->values->result) == 0) {
                    array_push($errors, ["id" => $id, "option" => "result"]);
                    continue;
                }

                $minSecSplit = explode(":", $row->values->result);
                $min = $minSecSplit[0];
                $secMillisSplit = explode(".", $minSecSplit[1]);


                if (strlen($secMillisSplit[1]) == 2) {
                    $secMillisSplit[1] = $secMillisSplit[1] * 10;
                }

                $totalMillis = $min * 60000 + $secMillisSplit[0] * 1000 + $secMillisSplit[1];


                $sr->result = $totalMillis;
                $sr->save();
            }
        }




        if (!empty($errors)) {
            return response()->json($errors, 500);
        }
        session()->flash('success', 'Results saved');
    }

    public function delete(Competition $comp, CompetitionSpeedEvent $event, Request $request)
    {
        $eid = $request->input('eid');

        if ($eid != $event->id) {
            return;
        }

        $event->delete();

        return redirect()->route('comps.events', $comp);
    }

    public function hide(Competition $comp, CompetitionSpeedEvent $event)
    {
        $event->hide();
        return redirect()->back();
    }


    public function scoringSettings(Competition $comp, CompetitionSpeedEvent $event)
    {
        $route = route('comps.events.speeds.scoring-settings.save', [$comp, $event]);
        $returnRoute = route('comps.events.speeds.view', [$comp, $event]);
        return view('competition.events.event-settings', compact('comp', 'event', 'route', 'returnRoute'));
    }

    public function saveScoringSettings(Competition $comp, CompetitionSpeedEvent $event, UpdateScoringSettings $request)
    {
        $validated = $request->validated();

        $schema = $event->scoringSchema;

        $ss = ['equation' => $validated['equation'], 'global_variables' => $validated['global_variables']];

        if (array_key_exists('local_variables', $validated)) {
            $ss['local_variables'] = $validated['local_variables'];
        }

        if (array_key_exists('penalty_func', $validated)) {
            $ss['penalty_func'] = $validated['penalty_func'];
        }

        if (array_key_exists('auto_penalties', $validated)) {
            $ss['auto_penalties'] = $validated['auto_penalties'];
        }

        if (array_key_exists('auto_disqualifications', $validated)) {
            $ss['auto_disqualifications'] = $validated['auto_disqualifications'];
        }

        $schema->schema = $ss;



        $schema->Save();

        return response()->json([]);
    }
}
