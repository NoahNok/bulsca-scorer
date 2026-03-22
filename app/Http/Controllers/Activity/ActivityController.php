<?php

namespace App\Http\Controllers\Activity;

use App\Http\Controllers\Controller;
use App\Models\Activity\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{

    public function admin()
    {


        return view('activity.admin');
    }
}
