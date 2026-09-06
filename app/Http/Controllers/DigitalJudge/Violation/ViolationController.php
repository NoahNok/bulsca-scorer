<?php

namespace App\Http\Controllers\DigitalJudge\Violation;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ViolationController extends Controller
{
    public function submissions(Competition $competition)
    {
        return Inertia::render('Judge/Competition/Violation/Submissions', [
            'competition' => $competition
        ]);
    }
}
