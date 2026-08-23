<?php

namespace App\Http\Controllers\DigitalJudge\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\DigitalJudge\Event\StoreHeatTimesRequest;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\SpeedResult;
use Illuminate\Http\Request;
use Inertia\Inertia;



class EventJudgeController extends Controller
{
    public function selectTimeHeat(Competition $competition, CompetitionSpeedEvent $event)
    {

        $heats = [];

        foreach ($event->getHeats()->with('entity.speedResults')->get()->sortBy('heat')->groupBy('heat') as $heat => $lanes) {


            $missingResult = false;

            // Code that checks if each team has a reuslt for the event
            foreach ($lanes as $team) {
                $sr = $team->entity->speedResults->where('event', $event->id)->first();

                if ($sr == null || $sr->result == null) {
                    $missingResult = true;
                    break;
                }
            }

            $heats[] = [
                'heat' => $heat,
                'complete' => !$missingResult
            ];
        }


        return Inertia::render('Judge/Competition/Speed/Time/SelectHeat', [
            'competition' => $competition->only(['id', 'name']),
            'event' => $event->jsonable(),
            'heats' => $heats,

        ]);
    }

    public function markTime(Competition $competition, CompetitionSpeedEvent $event, int $heat)
    {
        return Inertia::render('Judge/Competition/Speed/Time/MarkTime', [
            'competition' => $competition->only(['id', 'name']),
            'event' => $event->jsonable(),
            'heat' => ['heat' => $heat, 'complete' => false, 'lanes' => $event->getHeats()->where('heat', $heat)->orderBy('lane')->get()->map(function ($lane) {
                return [
                    'lane' => $lane->lane,
                    'entity' => $lane->entity->jsonable()
                ];
            })]
        ]);
    }

    public function storeTime(Competition $competition, CompetitionSpeedEvent $event, int $heat, StoreHeatTimesRequest $request)
    {
        $data = $request->validated();

        $marks = $data['mark']; // this is a key value array of entity_id -> time

        foreach ($marks as $entity_id => $value) {
            $entity = $event->getScorableEntity()::find($entity_id);

            $sr = SpeedResult::whereMorphedTo('entity', $entity)->where('event', $event->id)->first();

            if (str_starts_with($value, "DN")) {
                $event->clearEntityDisqualifications($entity);
                $code = str_starts_with($value, 'DNS') ? 99904 : 99915;
                $event->addEntityDisqualification($sr->entity, $code);
                $sr->result = 3599000;
                $sr->save();
                continue;
            }

            if (str_starts_with($value, "OOT")) {
                $event->clearEntityDisqualifications($entity);
                $event->addEntityDisqualification($sr->entity, 99901);
                $sr->result = 3599000;
                $sr->save();
                continue;
            }


            $fromResult = $sr->getResultAsString();
            // See if time value starts with DNF
            $minSecSplit = explode(":", $value);

            if ($event->getName() == "Rope Throw" && count($minSecSplit) == 1) {
                $sr->result = $minSecSplit[0];
            } else {

                if (count($minSecSplit) != 2) {
                    continue;
                }

                $min = $minSecSplit[0];
                $secMillisSplit = explode(".", $minSecSplit[1]);
                if (count($secMillisSplit) != 2) {
                    continue;
                }

                # Judge side no uses centiseconds, so multiply by 10 for millis
                if (strlen($secMillisSplit[1]) == 2) {
                    $secMillisSplit[1] = $secMillisSplit[1] * 10;
                }

                $totalMillis = $min * 60000 + $secMillisSplit[0] * 1000 + $secMillisSplit[1];


                $sr->result = $totalMillis;
            }



            $sr->save();
        }

        return response()->json(['hasNextHeat' => $heat < $event->getMaxHeats()]);
    }
}
