<?php

namespace App\Http\Controllers\Manage\Concerns;

use App\Models\AbstractClasses\Entity;
use App\Models\Competition;
use App\Traits\RecordActivity;
use Illuminate\Support\Str;

/**
 * What the heat and draw editors do after every edit: clear the cached Heats or Draw the public pages
 * read, and record the edit as Activity on the competition and the Entities it moved.
 */
trait RecordsOrderEdits
{
    use RecordActivity;

    /**
     * Clears whichever cache the edit made stale
     */
    abstract protected function clearCache(Competition $comp): void;

    /**
     * @param array<int, ?Entity> $entities
     */
    protected function changed(Competition $comp, string $activity, ?string $description = null, array $context = [], array $entities = []): void
    {
        $this->clearCache($comp);
        $this->recordActivity($activity, $description, context: $context, related: [$comp, ...array_filter($entities)]);
    }

    /**
     * "and unassigned 3 entries", for Activity
     */
    protected function unassignedCount(int $count): string
    {
        return "and unassigned {$count} " . Str::plural('entry', $count);
    }
}
