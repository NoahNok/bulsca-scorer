<?php

namespace App\Scoring;

use App\Models\AbstractClasses\Event;
use App\Scoring\Engine\EventScorer;
use App\Scoring\Engine\ScoredResult;
use App\Scoring\Loading\EventLoader;
use App\Scoring\Loading\LoadedEvent;

/**
 * Events already loaded and scored in this request, so that every Results sheet and Master sheet
 * that includes an Event shares one loading of it and one scoring of it for each League.
 *
 * There is one per request (it is bound `scoped`), and it is emptied whenever a model is saved or
 * deleted (`AppServiceProvider`), so a change made in a request is seen by what is scored after it.
 * A change made without a model event (a pivot `attach()`, a query builder update) is not: call
 * `forget()` after one if results are scored later in the same request.
 *
 * `Event::getRankedResults()` does not use it, and scores afresh every time.
 */
final class ScoringRun
{
    /** @var array<string, ?LoadedEvent> */
    private array $loaded = [];

    /** @var array<string, ScoredResult[]> */
    private array $results = [];

    /**
     * The Event's scoring input and the models it was read from. Null for an Event with no formula.
     */
    public function loaded(Event $event): ?LoadedEvent
    {
        $key = self::key($event);

        if (!array_key_exists($key, $this->loaded)) {
            $this->loaded[$key] = $event->scoringSchema ? EventLoader::load($event) : null;
        }

        return $this->loaded[$key];
    }

    /**
     * The Event's results scored and placed, best first: everyone's, or a League's Entries scored
     * against each other. They are shared, so must not be changed. An Event without a formula has none.
     *
     * @return ScoredResult[]
     */
    public function results(Event $event, ?int $leagueId = null): array
    {
        $key = self::key($event) . '|' . ($leagueId ?? 'all');

        if (!array_key_exists($key, $this->results)) {
            $loaded = $this->loaded($event);

            $this->results[$key] = $loaded ? EventScorer::score($loaded->input, $leagueId) : [];
        }

        return $this->results[$key];
    }

    public function forget(): void
    {
        $this->loaded = [];
        $this->results = [];
    }

    private static function key(Event $event): string
    {
        return "{$event->getConnectionName()}|{$event->getType()}|{$event->id}";
    }
}
