<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use Illuminate\Http\Request;

class SERCController extends Controller
{


    public function add(Competition $comp)
    {
        return view('competition.events.sercs.add', ['comp' => $comp]);
    }
}
