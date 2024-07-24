<?php

namespace App\Helpers;

use App\Models\Scoring\Bulsca\BulscaSercScoring;
use App\Models\Scoring\Bulsca\BulscaSpeedScoring;

class ScoringHelper {


    static $availableTypes = ['bulsca' => 'BULSCA', 'rlss-nationals' => 'RLSS Nationals'];


    static function resolve($scoringType, $eventType) {

        return match ($scoringType) {
            'bulsca' => match ($eventType) {
                'speed' => new BulscaSpeedScoring(),
                'serc' => new BulscaSercScoring()
            }
        };

    }

    

    

}