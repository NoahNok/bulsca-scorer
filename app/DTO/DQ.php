<?php

namespace App\DTO;

use App\Models\AbstractClasses\Entity;
use App\Models\AbstractClasses\Event;

class DQ
{

    /**
     * @param int[] $penalties
     */
    public function __construct(
        public int $code,
        public $related
    ) {}
}
