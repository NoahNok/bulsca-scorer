<?php

namespace App\Http\Controllers\DigitalJudge;

use App\DigitalJudge\DigitalJudge;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SpeedJudgingController extends Controller
{
    public function index()
    {

        return view('digitaljudge.speeds.index', DigitalJudge::getSpeedBladeProps());
    }
}
