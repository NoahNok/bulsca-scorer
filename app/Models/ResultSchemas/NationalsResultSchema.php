<?php

namespace App\Models\ResultSchemas;

use App\Models\Interfaces\IEvent;
use App\Models\League;
use App\Models\ResultSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NationalsResultSchema extends  ResultSchema
{
    protected $table = 'result_schemas';

    public function getResults() {

        if ($this->league == "O" || $this->league == "OM") { // overall and overall masters
            return $this->handleOveralls();
        }

        return $this->getBracketResults($this->league);


        
    }

    private function getBracketResults($bracket) {
        request()->merge(['bracket' => $bracket]);

      
        $results = $this->getEvents->map(function($revent) {
            $event = $revent->getActualEvent;

            $results = $event->getResults();

            foreach ($results as $result) {
                $result->weight = $revent->weight;
                $result->type = $event->getType();
                $result->sub_type = $event->getType() == "serc" ? $event->type : $event->getName();
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
            $result->region = $nameData->club_region;
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

        // sort results with same place first by wet score the nby dry score otherwise leave as is
        $finalResults = $finalResults->sort(function($result1, $result2) {
            
            if ($result1->place != $result2->place) {
                return 0;
            }

            $result1Wet = $result1->events->where('sub_type', 'WET')->first()->place ?? 0;
            $result2Wet = $result2->events->where('sub_type', 'WET')->first()->place ?? 0;

            $result1Dry = $result1->events->where('sub_type', 'DRY')->first()->place ?? 0;
            $result2Dry = $result2->events->where('sub_type', 'DRY')->first()->place ?? 0;

            if ($result1Wet == $result2Wet) {

   
                if ($result1Dry == $result2Dry) {
                    $result2->draw = true;
                    return 0;
                }
                
                return $result1Dry <=> $result2Dry;
            }

            return $result1Wet <=> $result2Wet;


        })->values();

        // re iterate the palces from 1 to 16 - there are 
        $currentPlace = 0;

        foreach ($finalResults as $result) {

            if ($result?->draw ?? false) {
                
                $result->place = $currentPlace;
                continue;
            }

            $currentPlace++;
            $result->place = $currentPlace;
        }


        $eventOrder = $this->getEvents->map(function($event) {
            return $event->getActualEvent->getName();
        });


        return ['results' => $finalResults, 'eventOrder' => $eventOrder];
    }

    private function handleOveralls(){

        $brackets = [];

        if ($this->league == "O") {
            $brackets = League::where('name', 'not like', 'Masters%')->where('scoring_type', 'rlss-nationals')->get();
        } else {
            $brackets = League::where('name', 'like', 'Masters%')->where('scoring_type', 'rlss-nationals')->get();
        }


  

        $resultsPerBracket = [];

        foreach ($brackets as $bracket) {
            $resultsPerBracket[$bracket->name] = $this->getBracketResults($bracket->id);
        }


   

        // join up the results via the region name. each regions final score is the sum of its brackets scores. if there are no entires for a bracket they get 16

        $allRegions = ['All Ireland', 'East Midlands', 'East', 'North East', 'North West', 'Scotland', 'South East', 'South', 'South West', 'Wales', 'West Midlands', 'West', 'Yorkshire'];

        $regionResults = [];

        foreach ($allRegions as $region) {
            $regionScore = 0;
            foreach ($resultsPerBracket as $bracketName => $results) {


                $regionScore += $results['results']->where('region', $region)->first()->place ?? 16;
            }

            $regionResults[$region] = $regionScore;
           
        }

        
        // sort the regions by score
        $regionResults = collect($regionResults)->sort()->toArray();

        // rank region results by their score, where same sores tie for palce but next plaec is skipped
        $currentPlace = 0;
        $previousResult = null;
        $skipBy = 0;

        $finalResults = [];

        foreach ($regionResults as $region => $score) {
            if ($score == $previousResult) { // same results given same place
                $skipBy++;
            } else {
                $currentPlace++;
                if ($skipBy > 0) {
                    $currentPlace += $skipBy;
                    $skipBy = 0;
                }
            }
            $previousResult = $score;

            $data = new \stdClass();
            $data->place = $currentPlace;
            $data->score = $score;
            $data->name = $region;
            $data->events = [];

            $finalResults[$region] = $data;
        }







        return ['results' => $finalResults, 'eventOrder' => [],  'overalls' => true];
    }

}
