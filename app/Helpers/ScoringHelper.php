<?php

namespace App\Helpers;

use App\Models\Competition;
use App\Models\League;
use App\Models\ResultSchema;
use App\Models\ResultSchemaEvent;
use App\Models\ResultSchemas\NationalsResultSchema;
use App\Models\Scoring\Bulsca\BulscaHeatGenerator;
use App\Models\Scoring\Bulsca\BulscaSercScoring;
use App\Models\Scoring\Bulsca\BulscaSpeedScoring;
use App\Models\Scoring\ClubSerc\ClubSercSercScoring;
use App\Models\Scoring\Nationals\NationalsHeatGenerator;
use App\Models\Scoring\Nationals\NationalsSercScoring;
use App\Models\Scoring\Nationals\NationalsSpeedScoring;
use stdClass;

class ScoringHelper
{


    static $availableTypes = ['bulsca' => ['name' => 'BULSCA', 'use_competitors' => false], 'rlss-nationals' => ['name' => 'RLSS Nationals', 'use_competitors' => true], 'rlss-cs' => ['name' => 'RLSS Club SERC', 'use_competitors' => false]];


    static function resolve($scoringType, $eventType)
    {

        return match ($scoringType) {
            'bulsca' => match ($eventType) {
                'speed' => new BulscaSpeedScoring(),
                'serc' => new BulscaSercScoring(),
                'heat' => new BulscaHeatGenerator(),
            },
            'rlss-nationals' => match ($eventType) {
                'speed' => new NationalsSpeedScoring(),
                'serc' => new NationalsSercScoring(),
                'heat' => new NationalsHeatGenerator(),
            },
            'rlss-cs' => match ($eventType) {
                'speed' => new BulscaSpeedScoring(),
                'serc' => new ClubSercSercScoring(),
                'heat' => new BulscaHeatGenerator(),
            }
        };
    }


    static function getCompetitionScoringDetails(Competition $competition)
    {

        return self::$availableTypes[$competition->scoring_type];
    }

    static function generateDefaultResultSheets(Competition $comp)
    {

        $type = $comp->scoring_type;

        function nationals(Competition $comp)
        {

            $leagues = League::where('scoring_type', 'rlss-nationals')->get();

            $overall = new stdClass();
            $overall->name = "Overall";
            $overall->id = "O";

            $overallMasters = new stdClass();
            $overallMasters->name = "Overall Masters";
            $overallMasters->id = "OM";

            $leagues->push($overall);
            $leagues->push($overallMasters);



            foreach ($leagues as $league) {
                $resultSchema = new NationalsResultSchema();
                $resultSchema->competition = $comp->id;
                $resultSchema->name = $league->name;
                $resultSchema->league = $league->id;
                $resultSchema->save();

                foreach ($comp->getSERCs as $serc) {
                    $rse = new ResultSchemaEvent();
                    $rse->schema = $resultSchema->id;
                    $rse->event_id = $serc->id;
                    $rse->event_type = "\App\Models\SERC";
                    $rse->weight = $serc->type == "DRY" ? 1 : 2;
                    $rse->save();
                }

                foreach ($comp->getSpeedEvents as $serc) {
                    $rse = new ResultSchemaEvent();
                    $rse->schema = $resultSchema->id;
                    $rse->event_id = $serc->id;
                    $rse->event_type = "\App\Models\CompetitionSpeedEvent";
                    $rse->weight = 1;
                    $rse->save();
                }
            }
        };

        function bulsca(Competition $comp)
        {
            $overall = new ResultSchema();
            $overall->competition = $comp->id;
            $overall->name = "Overall";
            $overall->league = "O";
            $overall->save();

            $a = new ResultSchema();
            $a->competition = $comp->id;
            $a->name = "A-League";
            $a->league = "A";
            $a->save();

            $b = new ResultSchema();
            $b->competition = $comp->id;
            $b->name = "B-League";
            $b->league = "B";
            $b->save();

            foreach ($comp->getSERCs as $serc) {
                $rse = new ResultSchemaEvent();
                $rse->schema = $overall->id;
                $rse->event_id = $serc->id;
                $rse->event_type = "\App\Models\SERC";
                $rse->weight = 2;
                $rse->save();
                $rse = new ResultSchemaEvent();
                $rse->schema = $a->id;
                $rse->event_id = $serc->id;
                $rse->event_type = "\App\Models\SERC";
                $rse->weight = 2;
                $rse->save();
                $rse = new ResultSchemaEvent();
                $rse->schema = $b->id;
                $rse->event_id = $serc->id;
                $rse->event_type = "\App\Models\SERC";
                $rse->weight = 2;
                $rse->save();
            }

            foreach ($comp->getSpeedEvents as $serc) {
                $rse = new ResultSchemaEvent();
                $rse->schema = $overall->id;
                $rse->event_id = $serc->id;
                $rse->event_type = "\App\Models\CompetitionSpeedEvent";
                $rse->weight = 1;
                $rse->save();
                $rse = new ResultSchemaEvent();
                $rse->schema = $a->id;
                $rse->event_id = $serc->id;
                $rse->event_type = "\App\Models\CompetitionSpeedEvent";
                $rse->weight = 1;
                $rse->save();
                $rse = new ResultSchemaEvent();
                $rse->schema = $b->id;
                $rse->event_id = $serc->id;
                $rse->event_type = "\App\Models\CompetitionSpeedEvent";
                $rse->weight = 1;
                $rse->save();
            }
        }

        switch ($type) {
            case 'bulsca':
                bulsca($comp);
                break;
            case 'rlss-nationals':
                nationals($comp);
                break;
            default:
                break;
        }
    }
}
