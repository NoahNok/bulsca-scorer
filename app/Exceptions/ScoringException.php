<?php

namespace App\Exceptions;

use App\Models\Interfaces\IScoringContextSimplify;
use Exception;

/**
 * A formula failed. Carries what the formula could see at the time (`results`, `item`, its
 * variables) so an Organiser can be shown which result broke it.
 */
class ScoringException extends Exception
{
    private ?array $simplified = null;

    public function __construct(string $message, private array $context = [])
    {
        parent::__construct($message);
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
                $results[$key] = $result instanceof IScoringContextSimplify ? $result->simplifyContext() : $result;
            }
            $context['results'] = $results;
        }

        if (array_key_exists('item', $context)) {
            $item = $context['item'];
            unset($context['item']);

            if ($item instanceof IScoringContextSimplify) {
                $context['item'] = $item->simplifyContext();
            }
        }

        return $context;
    }
}
