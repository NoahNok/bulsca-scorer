<?php

namespace App\Models\ResultSchemas;

use App\Models\ResultSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NationalsResultSchema extends  ResultSchema
{
    protected $table = 'result_schemas';

    public function getResults() {


        request()->merge(['bracket' => $this->league]);

      
        $results = $this->getEvents->map(function($revent) {
            $event = $revent->getActualEvent;

            $results = $event->getResults();

            foreach ($results as $result) {
                $result->weight = $revent->weight;
            }


            return ["name" => $event->getName(), "results" => $results, "event" => $event];
        }); // [{name =>, results => [{..., tid =>}]}] each result has a tid, want to match across





        // get all the unique tid that appear across all events
        $tids = $results->map(function($event) {
      
            if ($event['event']->getType() != "serc") {
                return [];
            }

            return collect($event['results'])->map(function($result) {
                return $result->tid;
            });
        })->flatten()->unique();

        // Now loop the tids to get the results
        $finalResults = $tids->map(function($tid) use ($results) {
            $result = new \stdClass();
            $result->tid = $tid;
            

            // attempt to find the team name from any of the events

            $nameData = collect($results->first(function ($event) use ($tid) {
                $found = collect($event['results'])->where('tid', $tid)->first();
                return $found != null && property_exists($found, 'club_name');
            })['results'])->where('tid', $tid)->first();
         
        

            $result->name = $nameData->team . (property_exists($nameData, 'pair') ? ' & ' . $nameData->pair : '') . " - " . $nameData->club_name . " (" . $nameData->club_region . ")";
            $result->events = $results->map(function($event) use ($tid) {

       

                return collect($event['results'])->where('tid', $tid)->first(default: $event['event']?->type ?? null);
            });

            // get final summed score across events
            $result->score = $result->events->sum(function($event) {


                $score = 16;
                

                if (!is_string($event) && $event != null) {
                    

                    if ($event?->skip ?? false) {
                        return 0;
                    }

                    $score = $event->place;
                }

                $score *= $event?->weight ?? 1;

                return $score;
            });

            
            return $result;
        });

        // Now rank the results via their score

        $finalResults = $finalResults->sortBy('score')->values();

        $currentPlace = 0;
        $previousResult = null;
        $skipBy = 0;
        foreach ($finalResults as $result) {
            if ($result->score == $previousResult) { // same results given same place
                $skipBy++;
            } else {
                $currentPlace++;
                if ($skipBy > 0) {
                    $currentPlace += $skipBy;
                    $skipBy = 0;
                }
            }
            $previousResult = $result->score;
            $result->place = $currentPlace;
        }


        $eventOrder = $this->getEvents->map(function($event) {
            $event = $event->getActualEvent->getName();
        });


        return ['results' => $finalResults, 'eventOrder' => $eventOrder];
    }

}
