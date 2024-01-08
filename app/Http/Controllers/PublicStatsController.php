<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicStatsController extends Controller
{


    public function clubs()
    {
        return view('public-results.stats.clubs');
    }

    public function club(string $clubName)
    {
        $club = \App\Models\Club::where('name', 'LIKE', '%' . $clubName . '%')->firstOrFail();

        return view('public-results.stats.club', ['club' => $club]);
    }
}
