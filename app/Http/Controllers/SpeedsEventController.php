<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddSpeedEventRequest;
use App\Http\Requests\Event\UpdateScoringSettings;
use App\Models\CompetitionSpeedEvent;
use App\Models\Competition;
use App\Models\Event\ScoringSchema;
use App\Models\League;
use App\Models\SpeedEvent;
use App\Models\SpeedResult;
use App\Services\HeatService;
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
        $cse->scorable_entity = $data['target_entity'];

        $time = $data['record'];
        $minSecSplit = explode(":", $time);
        $min = $minSecSplit[0];
        $secMillisSplit = explode(".", $minSecSplit[1]);

        $totalMillis = $min * 60000 + $secMillisSplit[0] * 1000 + $secMillisSplit[1];




        $cse->record = SpeedEvent::find($data['event'])->record;
        $cse->weight = $data['weight'];

        $cse->save();


        // Need to add all teams to this new event
        $allTeams = $cse->getScorableEntities();
        foreach ($allTeams as $team) {
            $sr = new SpeedResult();
            $sr->entity()->associate($team);
            $sr->event = $cse->id;
            $sr->save();
        }




        return redirect()->route('comps.events', $comp);
    }

    public function view(Competition $comp, CompetitionSpeedEvent $event, HeatService $heatService)
    {

        $eventResults = null;
        $league = request()->query('league', null);

        $league = League::find($league);

        if ($event->scoring_schema) {
            $eventResults = $event->getRankedResults($league);
        }

        $show_dq_points = $event->scoringSchema->schema['allow_disqualified_to_rank'] ?? false;

        return view('competition.events.speeds.view', ['comp' => $comp, 'event' => $event, 'eventResults' => $eventResults, 'activeLeague' => $league, 'show_dq_points' => $show_dq_points, 'heatService' => $heatService]);
    }

    public function edit(Competition $comp, CompetitionSpeedEvent $event)
    {
        return view('competition.events.speeds.edit', compact('comp', 'event'));
    }


    public function editPost(Competition $comp, CompetitionSpeedEvent $event, Request $request)
    {

        $same = $request->input('target_entity', $event->scorable_entity) == $event->scorable_entity;

        if (!$same) {
            $event->scorable_entity = $request->input('target_entity', $event->scorable_entity);


            $event->save();

            $event->results()->delete();
            // Need to add all teams to this new event
            $allTeams = $event->getScorableEntities();
            foreach ($allTeams as $team) {
                $sr = new SpeedResult();
                $sr->entity()->associate($team);
                $sr->event = $event->id;
                $sr->save();
            }
        }

        $event->has_penalties = $request->input('has_penalties', false) == 'on';
        $event->save();







        return redirect()->route('comps.events.speeds.view', [$comp, $event])->with('success', 'Event Updated');
    }




    public function editResult(Competition $comp, CompetitionSpeedEvent $event)
    {
        return view('competition.events.speeds.edit-result', ['comp' => $comp, 'event' => $event]);
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
                $code = str_starts_with($row->values->result, 'DNS') ? 99904 : 99915;
                $event->addEntityDisqualification($sr->entity, $code);
                continue;
            }

            if (str_starts_with($row->values->result, 'OOT')) {
                $event->addEntityDisqualification($sr->entity, 99901);
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

        if (!$schema) {
            $schema = new ScoringSchema();
            $schema->name = "Scoring Schema for {$event->getType()}:{$event->id}";
            $schema->schema = [];
            $schema->save();
            $event->scoring_schema = $schema->id;
            $event->save();
        }

        $schema->editFromRequest($request);

        return response()->json([]);
    }

    public function printResults(Competition $comp, CompetitionSpeedEvent $event)
    {
        $data = [[
            'league' => 'Overall',
            'results' => $event->getRankedResults(),
        ]];

        foreach ($comp->getLeagues->sortBy('name') as $league) {
            $results = $event->getRankedResults($league);

            $data[] = [
                'league' => $league->name,
                'results' => $results,
            ];
        }


        $show_dq_points = $event->scoringSchema->schema['allow_disqualified_to_rank'] ?? false;

        return view('competition.events.speeds.print', ['comp' => $comp, 'event' => $event, 'data' => $data, 'show_dq_points' => $show_dq_points]);
    }
}
