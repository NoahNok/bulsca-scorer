<?php

namespace App\Models\Scoring;

use App\Models\Competition;

interface IHeatGenerator
{
    public function generate(Competition $comp): void;
}
