<?php

namespace App\Support;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\Parser;
use Symfony\Component\ExpressionLanguage\SyntaxError;

/**
 * Checks the expressions a scoring saves (equation, penalty function, variables, auto penalties and DQs)
 * are written correctly, so one typo can't break every page that scores with it. Only the syntax is
 * checked: which variables and functions exist depends on the results it's run on.
 */
class ScoringExpressions
{
    /**
     * Why the first badly written expression in `data` doesn't work, naming which one, or null
     */
    public static function firstError(array $data): ?string
    {
        foreach (self::expressions($data) as $label => $expression) {
            if ($error = self::error($expression)) {
                return "{$label} isn't written correctly: {$error}";
            }
        }

        return null;
    }

    /**
     * Why `expression` isn't written correctly, or null
     */
    public static function error(string $expression): ?string
    {
        try {
            (new ExpressionLanguage())->lint($expression, [], Parser::IGNORE_UNKNOWN_VARIABLES | Parser::IGNORE_UNKNOWN_FUNCTIONS);
            return null;
        } catch (SyntaxError $error) {
            return rtrim($error->getMessage(), '.') . '.';
        }
    }

    /**
     * @return iterable<string, string> Each non-empty expression, keyed by what it's called on the page
     */
    private static function expressions(array $data): iterable
    {
        $named = [
            'equation' => 'The result equation',
            'penalty_func' => 'The penalty function',
            'rank_equation' => 'The rank equation',
        ];
        foreach ($named as $key => $label) {
            if (is_string($data[$key] ?? null) && trim($data[$key]) !== '') {
                yield $label => $data[$key];
            }
        }

        foreach (['global_variables' => 'Global variable', 'local_variables' => 'Local variable'] as $key => $label) {
            foreach ((array) ($data[$key] ?? []) as $index => $variable) {
                $expression = $variable['expression'] ?? null;
                if (is_string($expression) && trim($expression) !== '') {
                    $name = $variable['name'] ?? null;
                    yield ($name ? "{$label} {$name}" : "{$label} " . ($index + 1)) => $expression;
                }
            }
        }

        foreach (['auto_penalties' => 'Auto penalty', 'auto_disqualifications' => 'Auto DQ'] as $key => $label) {
            foreach ((array) ($data[$key] ?? []) as $index => $auto) {
                $code = ($auto['code'] ?? null) ?: ($index + 1);
                foreach (['condition' => 'condition', 'amount' => 'amount'] as $field => $part) {
                    $expression = $auto[$field] ?? null;
                    if (is_string($expression) && trim($expression) !== '') {
                        yield "{$label} {$code}'s {$part}" => $expression;
                    }
                }
            }
        }
    }
}
