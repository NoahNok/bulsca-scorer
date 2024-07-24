<?php

namespace App\Models\Interfaces;

use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\Scoring\IScoring;
use App\Models\SERCJudge;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class IEvent extends Model {

    protected IScoring $scoring;

    public function __construct(IScoring $scoring) {
        $this->scoring = $scoring;
    }

    abstract public function getName(): string;
    abstract public function getCompetition();
    /**
     * @return CompetitionTeam[]
     */
    abstract public function getTeams();
    public function getResults(): array {
        return $this->scoring->getResults($this);
    }
    public function getResultQuery(): string {
        return $this->scoring->getResultQuery($this);
    }
    abstract public function getType(): string;
}
