<?php

namespace App\DTO;

use App\Models\AbstractClasses\Entity;
use App\Models\AbstractClasses\Event;
use App\Models\Event\Penalty;
use App\Models\Event\Disqualification;
use Illuminate\Database\Eloquent\Collection;

/**
 * Represents a result that has been transformed into its final value based on any applied
 * disqualifications or penalties from the given scoring setup
 * 
 * Stores the same data as Result but with an additional resolvedResult value
 */
class ResolvedResult extends Result
{

    /**
     * @param Collection<int, Disqualification> $disqualifications
     * @param Collection<int, Penalty> $penalties
     */
    public function __construct(
        public int $id,
        public int $resolvedResult,
        public string|int $result,
        public Entity $entity,
        public Event $event,
        public ?Collection $disqualifications = null,
        public ?Collection $penalties = null,
    ) {
        $this->disqualifications ??= new Collection();
        $this->penalties ??= new Collection();
        parent::__construct($id, $result, $entity, $event, $disqualifications, $penalties);
    }
}
