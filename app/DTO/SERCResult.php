<?php

namespace App\DTO;

use App\Models\AbstractClasses\Entity;
use App\Models\AbstractClasses\Event;
use App\Models\SERCMarkingPoint;
use App\Models\Event\Penalty;
use App\Models\Event\Disqualification;
use Illuminate\Database\Eloquent\Collection;

class SERCResult extends Result
{

    /**
     * @param Collection<int, Disqualification> $disqualifications
     * @param Collection<int, Penalty> $penalties
     */
    public function __construct(
        public int $id,
        public string|int $result,
        public SERCMarkingPoint $markingPoint,
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
