<?php

namespace App\Exceptions;

use App\Models\Interfaces\IScoringContextSimplify;
use Exception;

class ScoringException extends Exception
{
    //
    private array $context;

    public function __construct(string $message, array $context = [])
    {
        parent::__construct($message);
        $this->context = $context;

        $this->context = $this->simplifyContext();
    }

    public function getContext(): array
    {
        return $this->context;
    }

    private function simplifyContext()
    {
        if (key_exists('results', $this->context)) {
            foreach ($this->context['results'] as $key => $result) {
                if ($result instanceof IScoringContextSimplify) {
                    $this->context['results'][$key] = $result->simplifyContext();
                }
            }
        }
        if (key_exists('item', $this->context)) {
            $item = $this->context['item'];
            unset($this->context['item']);

            if ($item instanceof IScoringContextSimplify) {
                $this->context['item'] = $item->simplifyContext();
            }
        }
        return $this->context;
    }
}
