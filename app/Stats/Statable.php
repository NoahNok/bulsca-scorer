<?php

namespace App\Stats;

use App\Models\Club;
use Illuminate\Support\Facades\Cache;

abstract class Statable
{

    private StatTarget $target;
    protected string $templateName;

    private ?Club $club;
    private ?string $team;

    private string $viewBase = "public-results.stats.templates.";

    abstract public function forGlobal(): array;
    abstract function forClub(Club $club): array;
    abstract function forTeam(Club $club, string $team): array;


    public function __construct(StatTarget $target, Club $club = null, string $team = null) {
        $this->target = $target;
   
        $this->club = $club;
        $this->team = $team;

        if (($this->target == StatTarget::CLUB || $this->target == StatTarget::TEAM) && $this->club == null) {
            throw new \Exception("Club must be provided when target is CLUB or TEAM");
        }

        if ($this->target == StatTarget::TEAM && $this->team == null) {
            throw new \Exception("Team must be provided when target is TEAM");
        }
    }

    

    public function computeAndRender(array $optionalData = []) {
        $data = $this->compute();
        return $this->render($data, $optionalData);
    }

    private function compute(): array {
        $data = [];

        switch ($this->target) {
            case StatTarget::GLOBAL:
                $data = $this->forGlobal();
                break;
            case StatTarget::CLUB:
                $data = $this->forClub($this->club);
                break;
            case StatTarget::TEAM:
                $data = $this->forTeam($this->club, $this->team);
                break;
        }

        return $data;

    }

    private function render(array $data, array $optionalData = []) {
        $viewData = array_merge(['data' => $data, 'stat_target' => $this->target], $optionalData);
        return view($this->viewBase . $this->templateName, $viewData);
    }
    
}

enum StatTarget {
    case GLOBAL;
    case CLUB;
    case TEAM;
}