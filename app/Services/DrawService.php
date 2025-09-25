<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\Orders\Draw;
use App\Models\Orders\Heat;
use App\Models\SERC;
use Exception;

class DrawService
{

    public function generateDrawForSERC(SERC $serc)
    {

        // Delete heats that exist
        $serc->draw()->delete();

        $entities = $serc->getScorableEntities();

        $tank = 0;
        $draw = 1;

        foreach ($entities as $entity) {
            $d = new Draw();
            $d->entity_id = $entity->id;
            $d->entity_type = $entity->getMorphClass();
            $d->serc = $serc->id;
            $d->tank = $tank;
            $d->draw = $draw;
            $d->save();

            $draw++;
        }
    }

    public function swapInDraw(Draw $drawA, Draw $drawB)
    {
        $temp_tank = $drawA->tank;
        $temp_draw = $drawA->draw;

        $drawA->tank = $drawB->tank;
        $drawA->draw = $drawB->draw;
        $drawA->save();

        $drawB->tank = $temp_tank;
        $drawB->draw = $temp_draw;
        $drawB->save();
    }
}
