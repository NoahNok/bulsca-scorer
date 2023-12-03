<?php

namespace App\Http\Controllers\WhatIf;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\WhatIf\CompetitionCloner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class WhatIfController extends Controller
{
    public function index()
    {
        return view('whatif.index');
    }

    public function cloneAndStart(Request $request)
    {

        if (Session::has('whatif.competition')) {
            return redirect()->route('whatif.editor');
        }

        $comp = Competition::find($request->input('competition'));

        if (!$comp) return "invalid comp";

        $cloner = new CompetitionCloner();
        $newCompId = $cloner->clone($comp);

        Session::put('whatif.competition', $newCompId);

        return redirect()->route('whatif.editor');
    }

    public function editorIndex()
    {

        $comp = Competition::find(Session::get('whatif.competition'));


        return view('whatif.editor', [
            'comp' => $comp
        ]);
    }
}
