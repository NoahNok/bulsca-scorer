<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LiveController extends Controller
{
    public function index()
    {

        $comp = Competition::orderBy(DB::raw('ABS(DATEDIFF(competitions.when, NOW()))'), 'asc')->first();

        return view('live.index', ['comp' => $comp]);
    }
}
