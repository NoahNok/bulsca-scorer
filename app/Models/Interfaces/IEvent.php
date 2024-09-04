<?php

namespace App\Models\Interfaces;

use App\Helpers\ScoringHelper;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\Scoring\IScoring;
use App\Models\SERCJudge;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class IEvent extends Model {

    protected IScoring $scoring;


    private function initScoring() {

        if (isset($this->{'scoring'})) {
            return;
        }

        $this->scoring = ScoringHelper::resolve($this->getCompetition->scoring_type, $this->getType());
    }


    

    abstract public function getName(): string;
    abstract public function getCompetition();
    /**
     * @return CompetitionTeam[]
     */
    abstract public function getTeams();
    public function getResults(): array {

        $this->initScoring();

        return $this->scoring->getResults($this);
    }
    public function getResultQuery(): string {

        $this->initScoring();

        return $this->scoring->getResultQuery($this);
    }
    abstract public function getType(): string;
}
