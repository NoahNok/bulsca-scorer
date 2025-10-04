<?php

namespace App\DTO;

use App\Models\AbstractClasses\Entity;
use App\Models\AbstractClasses\Event;
use Illuminate\Support\Collection;

/**
 * Represents a result that has been transformed into its final value based on any applied
 * disqualifications or penalties from the given scoring setup
 * 
 * Stores the same data as Result but with an additional resolvedResult value
 */
class EntityGrouping
{


    public function __construct(
        public ?int $club_id,
        public ?int $team_id,
        public ?int $competitior_id,
        public ?int $league_id
    ) {}
}
