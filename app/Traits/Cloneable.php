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

    public function cloneOnce($conditions = []): int
    {

        $clone = $this->replicate();
        $clone->setConnection('whatif');

        // First check to see if an instance with the specified conditions already exists
        $existing = $this->setConnection('whatif')->where($conditions)->first();

        // If it exists return the id of the existing instance
        if ($existing != null) {
            return $existing->id;
        }

        $clone->save();

        return $clone->id;
    }
}
