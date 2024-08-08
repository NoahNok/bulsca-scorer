<?php

namespace App\Helpers;

use App\Models\Competition;
use App\Models\Scoring\Bulsca\BulscaSercScoring;
use App\Models\Scoring\Bulsca\BulscaSpeedScoring;
use App\Models\Scoring\Nationals\NationalsSercScoring;
use App\Models\Scoring\Nationals\NationalsSpeedScoring;

class ScoringHelper {


    static $availableTypes = ['bulsca' => ['name' => 'BULSCA', 'use_competitors' => false], 'rlss-nationals' => ['name' => 'RLSS Nationals', 'use_competitors' => true]];


    static function resolve($scoringType, $eventType) {

        return match ($scoringType) {
            'bulsca' => match ($eventType) {
                'speed' => new BulscaSpeedScoring(),
                'serc' => new BulscaSercScoring()
            },
            'rlss-nationals' => match ($eventType) {
                'speed' => new NationalsSpeedScoring(),
                'serc' => new NationalsSercScoring()
            },
        };

    }


    static function getCompetitionScoringDetails(Competition $competition) {

        return self::$availableTypes[$competition->scoring_type];

    }

    

    

}