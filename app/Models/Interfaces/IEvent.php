<?php

namespace App\Models\Interfaces;

use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\SERCJudge;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

interface IEvent {
    public function getName(): string;
    public function getCompetition();
    /**
     * @return CompetitionTeam[]
     */
    public function getTeams();
    public function getResults(): array;
    public function getType(): string;
}
