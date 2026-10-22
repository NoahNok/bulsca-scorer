<?php

namespace App\Services;

use App\Models\AbstractClasses\Entity;
use App\Models\Competition;
use App\Models\Orders\EntityEventSeed;
use App\Models\SpeedResult;

/**
 * What adding, changing or removing a competition's entries (clubs, teams, competitors) has to keep in
 * step: the empty speed results a new entry needs, its seed times, and the cached names, heats and draws.
 */
class CompetitionEntries
{
    /** A seed time as Organisers type it: 01:23.456, or 1:23.45 */
    public const SEED_FORMAT = '/^\d{1,2}:[0-5]\d\.\d{2,3}$/';

    public function __construct(private Competition $comp) {}

    /**
     * Give a new entry an empty result in each speed event that scores its kind of entry.
     */
    public function created(Entity $entity): void
    {
        foreach ($this->comp->getSpeedEvents as $event) {
            if (get_class($entity) != get_class($event->getScorableEntity())) {
                continue;
            }

            $result = new SpeedResult();
            $result->entity()->associate($entity);
            $result->event = $event->id;
            $result->save();
        }
    }

    /**
     * Save an entry's seed times, keyed by speed event id. A blank one removes that seed, and events
     * that can't be seeded (seeds off, or only the first event when they aren't per event) are ignored.
     *
     * @param  array<int|string, ?string>  $seeds
     */
    public function saveSeeds(Entity $entity, array $seeds): void
    {
        foreach (collect($this->comp->getSeedableEvents())->pluck('id') as $eventId) {
            if (!array_key_exists($eventId, $seeds)) {
                continue;
            }

            $where = ['speed_event' => $eventId, 'entity_type' => $entity->getMorphClass(), 'entity_id' => $entity->id];

            if (blank($seeds[$eventId])) {
                EntityEventSeed::where($where)->delete();
                continue;
            }

            EntityEventSeed::updateOrCreate($where, ['seed' => self::seedToMillis($seeds[$eventId])]);
        }
    }

    /**
     * Names, heats and draws are cached, so drop them after any change to the entries.
     */
    public function changed(): void
    {
        $this->comp->clearEntityNameCache();
        $this->comp->clearDrawCache();
        $this->comp->clearHeatCache();
    }

    /**
     * A seed time in SEED_FORMAT as milliseconds. Two decimals are hundredths.
     */
    public static function seedToMillis(string $seed): int
    {
        [$minutes, $rest] = explode(':', $seed);
        [$seconds, $fraction] = explode('.', $rest);

        return (int) $minutes * 60000 + (int) $seconds * 1000 + (int) str_pad($fraction, 3, '0');
    }
}
