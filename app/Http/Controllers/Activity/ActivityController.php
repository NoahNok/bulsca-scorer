<?php

namespace App\Http\Controllers\Activity;

use App\Http\Controllers\Controller;
use App\Models\Activity\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{

    public function admin()
    {
        $activities = Activity::with('user', 'related')->latest()->paginate(50);

        return view('activity.admin', compact('activities'));
    }
}
