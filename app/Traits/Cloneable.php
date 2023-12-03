<?php

namespace App\Traits;

trait Cloneable
{

    public function clone($overrides = []): int
    {

        $clone = $this->replicate();
        $clone->setConnection('whatif');

        foreach ($overrides as $key => $value) {
            $clone->{$key} = $value;
        }

        $clone->save();

        return $clone->id;
    }
}
