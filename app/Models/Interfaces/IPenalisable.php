<?php

namespace App\Models\Interfaces;

use App\Models\CompetitionTeam;
use App\Models\SERCJudge;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

interface IPenalisable
{
    public function addTeamPenalty($teamId, $code);
    public function addTeamDQ($teamId, $code);
}
