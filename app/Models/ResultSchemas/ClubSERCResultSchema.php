<?php

namespace App\Models\ResultSchemas;

use App\Models\Interfaces\IEvent;
use App\Models\League;
use App\Models\ResultSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use stdClass;

class ClubSERCResultSchema extends  ResultSchema
{
    protected $table = 'result_schemas';

    public function getResults()
    {

        $serc = $this->getEvents->first()->getActualEvent;

        $l = League::where('name', $this->league)->first();

        request()->merge(['league' => $l->id]);

        $results = $serc->getResults();

        return ["results" => $results, "eventOrder" => [$serc->getName()], 'maxMark' => $serc->getMaxMark()];
    }
}
