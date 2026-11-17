<?php

namespace App\Exceptions;

use App\Models\Interfaces\IScoringContextSimplify;
use Closure;
use Exception;

/**
 * A formula failed. Carries what the formula could see at the time (`results`, `item`, its
 * variables) so an Organiser can be shown which result broke it.
 *
 * The engine has no database, so a result describes its Entry and Event by key (`team:12`,
 * `speed:3`). Whoever loaded them can hand over how to name them (`nameWith()`).
 */
class ScoringException extends Exception
{
    private ?array $simplified = null;

    /** @var ?Closure(string): ?string An Entry's name from its key */
    private ?Closure $entryName = null;

    /** @var ?Closure(string): ?string An Event's name from its key */
    private ?Closure $eventName = null;

    public function __construct(string $message, private array $context = [])
    {
        parent::__construct($message);
    }

    /**
     * How to name the Entries (`team:12`) and Events (`speed:3`) the results describe. Each is
     * given a key and returns a name, or null to leave the key. Only called when the context is
     * asked for.
     *
     * @param ?Closure(string): ?string $entries
     * @param ?Closure(string): ?string $events
     */
    public function nameWith(?Closure $entries = null, ?Closure $events = null): static
    {
        $this->entryName = $entries ?? $this->entryName;
        $this->eventName = $events ?? $this->eventName;
        $this->simplified = null;

        return $this;
    }

    /**
     * What the formula could see, with each result reduced to its names and numbers. Worked out
     * when first asked for, not when the exception is made: naming every Entity takes queries, and
     * most failures are only ever reported by their message.
     */
    public function getContext(): array
    {
        return $this->simplified ??= $this->simplifyContext();
    }

    private function simplifyContext(): array
    {
        $context = $this->context;

        if (is_iterable($context['results'] ?? null)) {
            $results = [];
            foreach ($context['results'] as $key => $result) {
                $results[$key] = $result instanceof IScoringContextSimplify ? $this->describe($result) : $result;
            }
            $context['results'] = $results;
        }

        if (array_key_exists('item', $context)) {
            $item = $context['item'];
            unset($context['item']);

            if ($item instanceof IScoringContextSimplify) {
                $context['item'] = $this->describe($item);
            }
        }

        return $context;
    }

    private function describe(IScoringContextSimplify $result): array
    {
        $described = $result->simplifyContext();

        foreach (['entity' => $this->entryName, 'event' => $this->eventName] as $field => $name) {
            if ($name && is_string($described[$field] ?? null)) {
                $described[$field] = $name($described[$field]) ?? $described[$field];
            }
        }

        return $described;
    }
}
