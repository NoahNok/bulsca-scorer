<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\Morphable;
use Illuminate\Database\Eloquent\Relations\Relation;

trait MorphableModel
{
    /**
     * Get the morph class for the model.
     * This will use the morph map alias if defined, otherwise the class name.
     */
    public function getMorphClass(): string
    {

        $map = Relation::morphMap();

        // Flip the map to get alias by class
        $alias = array_search(static::class, $map, true);

        return $alias ?: static::class;
    }

    /**
     * Get the morph type column name.
     * You can override this in your model if needed.
     */
    public function getMorphType(): string
    {
        return property_exists($this, 'morphType') ? $this->morphType : 'entity_type';
    }
}
