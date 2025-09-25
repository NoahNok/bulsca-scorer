<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\Orders\Heat;
use App\Services\HeatService;
use Illuminate\Http\Request;
use Nette\NotImplementedException;

class HeatController extends Controller
{
    public function index(Competition $comp)
    {


        return view('competition.heats-and-orders.index', ['comp' => $comp]);
    }

    public function generate(Competition $comp, HeatService $heatService)
    {

        if ($comp->heats_per_event) {
            throw new NotImplementedException("Heats per event not implemented");
        }


        $heatService->generateHeatsForEvent($comp->getSpeedEvents()->orderBy('id')->first());

        return redirect()->back()->with('success', 'Heats Generated');
    }

    public function edit(Competition $comp, CompetitionSpeedEvent $event)
    {
        return view('competition.heats-and-orders.heats.edit', ['comp' => $comp, 'event' => $event]);
    }

    public function swap(Competition $comp, CompetitionSpeedEvent $event, Request $request)
    {
        $currentAllocation = Heat::find($request->input('team'));
        $targetAllocation = Heat::find($request->input('target-heat'));

        // If target is a blank space
        if ($targetAllocation == null) {
            $heatLaneSplit = explode(':', $request->input('target-heatlane'));
            $currentAllocation->heat = $heatLaneSplit[0];
            $currentAllocation->lane = $heatLaneSplit[1];
            $currentAllocation->save();

            return redirect()->back();
        }


        $currentHeat = $currentAllocation->heat;
        $currentLane = $currentAllocation->lane;

        $currentAllocation->heat = -1;
        $currentAllocation->lane = -1;
        $currentAllocation->save();

        $currentAllocation->heat = $targetAllocation->heat;
        $currentAllocation->lane = $targetAllocation->lane;

        $targetAllocation->heat = $currentHeat;
        $targetAllocation->lane = $currentLane;

        $currentAllocation->save();
        $targetAllocation->save();

        return redirect()->back();
    }

    public function swapHeats(Competition $comp, CompetitionSpeedEvent $event, Request $request)
    {
        $first = $request->input('first');
        $second = $request->input('second');


        $firstLanes = $event->heats()->where('heat', $first)->get();
        $secondLanes = $event->heats()->where('heat', $second)->get();

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

    public function reset(Competition $comp, CompetitionSpeedEvent $event, HeatService $heatService)
    {
        $heatService->generateHeatsForEvent($event);

        return redirect()->back()->with('success', 'Heats Reset');
    }

    public function deleteHeat(Competition $comp, CompetitionSpeedEvent $event, Request $request)
    {
        $event->heats()->where('heat', $request->input('heat'))->delete();

        return response()->json(['result' => 'ok']);
    }
}
