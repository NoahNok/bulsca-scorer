<?php

namespace App\DTO;

class JudgeAuthUser
{
    public function __construct(
        public string $id,
        public string $name
    ) {}

    public function getAuthIdentifier()
    {
        return $this->id;
    }
}
