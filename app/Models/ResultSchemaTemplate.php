<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultSchemaTemplate extends Model
{
    use HasFactory;

    protected $casts = [
        'schema' => 'array'
    ];


    public function editFromRequest($request)
    {
        $validated = $request->validated();


        if (array_key_exists('name', $validated) && $validated['name']) {
            $this->name = $validated['name'];
        }

        $ss = ['equation' => $validated['equation'], 'global_variables' => $validated['global_variables']];



        //rank_higher rank_equation allow_disqualified_to_rank

        $this->schema = $ss;



        $this->save();
    }
}
