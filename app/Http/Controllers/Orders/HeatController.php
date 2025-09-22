<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use Illuminate\Http\Request;

class HeatController extends Controller
{
    public function index(Competition $comp)
    {


        return view('competition.heats-and-orders.index', ['comp' => $comp, 'heatEntries' => collect()]);
    }
}
