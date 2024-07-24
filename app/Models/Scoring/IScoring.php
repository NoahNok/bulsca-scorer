<?php

namespace App\Models\Scoring;

use App\Models\Interfaces\IEvent;

interface IScoring {
    public function getResults(IEvent $event): array;
    public function getResultQuery(IEvent $event): string;

}