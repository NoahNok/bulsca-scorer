<?php

namespace App\Http\Controllers\Brands;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BrandHomeController extends Controller
{
    public function index()
    {
        return view('brand.index');
    }

    public function createCompetition()
    {
        return "hi";
    }
}
