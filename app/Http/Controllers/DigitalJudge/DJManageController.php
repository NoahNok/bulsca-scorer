<?php

namespace App\Http\Controllers\DigitalJudge;

use App\Http\Controllers\Controller;
use App\Models\SERC;
use App\Models\SERCMarkingPoint;
use Illuminate\Http\Request;

class DJManageController extends Controller
{

    public function index()
    {
        return view('digitaljudge.manage.index');
    }

    public function manageSerc(SERC $serc)
    {
        return view('digitaljudge.manage.manage-serc', ['serc' => $serc]);
    }

    public function manageSercPost()
    {
        $mp = SERCMarkingPoint::find(request('id'));

        $mp->weight = request('weight');

        $mp->save();

        return redirect()->back()->with('success', "Updated weighting.");
    }
}
