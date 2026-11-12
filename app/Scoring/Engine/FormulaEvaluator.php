<?php

namespace App\Scoring\Engine;

use App\Exceptions\ScoringException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\ParsedExpression;

/**
 * The formula language: Symfony ExpressionLanguage plus FILTER, MAXIMUM, MINIMUM, COUNT, CEIL,
 * FLOOR, RANK and RANKMEDIAN. `EventScorer` runs an Event's formula with it, and a Results sheet
 * its Formula over each Event's results (`process`).
 *
 * It knows nothing of models or the database: a result is any object with the properties a
 * formula reads. Quirks are kept as they were (see docs/scoring-engine.md): results that have been
 * published depend on what it works out, so change nothing here without the golden tests and a
 * `scoring:snapshot --compare`.
 */
class FormulaEvaluator
{
    private ExpressionLanguage $el;

    /** The sorted values RANK has worked out, by the results' ids and the attribute (but not the direction: Q2) */
    private array $rankCache = [];

    /** The places RANKMEDIAN has worked out for each value, keyed as `$rankCache` is */
    private array $rankMedianCache = [];

    public function __construct()
    {
        $this->el = new ExpressionLanguage();

        $this->registerBuiltins();
    }

    /**
     * One expression for one result, which it reads as `item`, alongside `$variables`
     */
    public function evaluateInstance(string $equation, $item, array $variables)
    {
        $variables['item'] = $item;

        $equation = $this->el->parse($equation, array_keys($variables));

        return $this->safeEval($equation, $variables, "equation");
    }

    /**
     * An expression parsed once, to be evaluated for many results. `$names` are the variables it
     * may read.
     */
    public function parse(string $expression, array $names): ParsedExpression
    {
        return $this->el->parse($expression, $names);
    }

    /**
     * Evaluates an expression with these variables. A failure is thrown as it is, not as a
     * `ScoringException`.
     */
    public function evaluate(Expression|string $expression, array $values): mixed
    {
        return $this->el->evaluate($expression, $values);
    }

    /**
     * Works out a formula for each result and writes the answer to its `$targetField`.
     *
     * Global variables are worked out once, in the order they are stored, each able to read
     * `results` and the globals before it. Local variables are worked out for each result, in the
     * order they are stored, and can read `item` and the locals before them. The equation can read
     * all of them. A variable's `order` is not read.
     *
     * @param array{equation?: string, global_variables?: array, local_variables?: array} $payload
     * @return Collection The results it was given
     */
    public function process(array $payload, Collection $results, string $targetField = 'points'): Collection
    {
        $equation = $payload['equation'] ?? null;

        $globalVars = $payload['global_variables'] ?? [];
        $localVars  = $payload['local_variables'] ?? [];

        $localVars = array_filter($localVars, fn($item) => $item['expression'] != null);

        if (!is_string($equation) || $equation === '') {
            throw new InvalidArgumentException("Missing or invalid 'equation'.");
        }

        $globalContext = [
            'results' => $results,
        ];

        foreach ($globalVars as $def) {
            $name = $def['name'] ?? null;

            if (!$name) {
                continue;
            }

            // One without an expression fails here as a syntax error
            $globalContext[$name] = $this->safeEval((string) ($def['expression'] ?? null), $globalContext, "global variable '{$name}'");
        }

        $allLocalVariableNames = array_map(fn($item) => $item['name'], $localVars);
        array_push($allLocalVariableNames, 'item');
        foreach ($localVars as $index => $var) {
            $localVars[$index]['expression'] = $this->el->parse($var['expression'], $allLocalVariableNames);
        }

        $equation = $this->el->parse($equation, array_merge($allLocalVariableNames, array_keys($globalContext)));

        foreach ($results as $result) {
            $perResultContext = $this->buildPerResultContext($result, $globalContext);

            foreach ($localVars as $def) {
                $name = $def['name'] ?? null;
                $expr = $def['expression'] ?? null;

                if (!$name || !$expr) {
                    throw new InvalidArgumentException("Each local variable must have 'name' and 'expression'.");
                }

                $perResultContext[$name] = $this->safeEval($expr, $perResultContext, "local variable '{$name}'");
            }

            $result->{$targetField} = $this->safeEval($equation, $perResultContext, "equation");
        }

        return $results;
    }

    private function buildPerResultContext($result, array $globalContext): array
    {
        $ctx = $globalContext;

        $ctx['item'] = $result;

        if (!isset($ctx['results'])) {
            $ctx['results'] = collect([]);
        }

        return $ctx;
    }

    private function registerBuiltins(): void
    {
        // FILTER(results, 'item.condition'): the results the condition holds for
        $this->el->register(
            'FILTER',
            fn($array, $condition) => sprintf('FILTER(%s, %s)', $array, $condition),
            function ($arguments, $array, $condition) {
                return $this->toCollection($array)
                    ->filter(function ($item) use ($condition) {
                        $vars = $this->buildPerResultContext($item, ['item' => $item]);

                        return (bool) $this->el->evaluate($condition, $vars);
                    })
                    ->values()
                    ->all();
            }
        );

        // MAXIMUM(results, 'attribute'), where 'result' means the resolved result
        $this->el->register(
            'MAXIMUM',
            fn($array, $attribute) => sprintf('MAXIMUM(%s, %s)', $array, $attribute),
            function ($arguments, $array, $attribute) {
                if ($attribute == 'result') {
                    $attribute = 'resolvedResult';
                }

                return $this->toCollection($array)->max($attribute);
            }
        );

        // MINIMUM(results, 'attribute'), where 'result' means the resolved result
        $this->el->register(
            'MINIMUM',
            fn($array, $attribute) => sprintf('MINIMUM(%s, %s)', $array, $attribute),
            function ($arguments, $array, $attribute) {
                if ($attribute == 'result') {
                    $attribute = 'resolvedResult';
                }

                return $this->toCollection($array)->min($attribute);
            }
        );

        $this->el->register(
            'COUNT',
            fn($array) => sprintf('COUNT(%s)', $array),
            fn($arguments, $array) => count($array)
        );

        $this->el->register(
            'CEIL',
            fn($number) => sprintf('CEIL(%s)', $number),
            fn($arguments, $number) => ceil($number)
        );

        $this->el->register(
            'FLOOR',
            fn($number) => sprintf('FLOOR(%s)', $number),
            fn($arguments, $number) => floor($number)
        );

        // RANK(results, 'attribute', item, 'DESC'|'ASC'): the item's place, ties sharing the better one
        $this->el->register(
            'RANK',
            fn($array, $attribute, $item, $direction = 'DESC') => sprintf('RANK(%s, %s, %s, %s)', $array, $attribute, $item, $direction),
            function ($arguments, $array, $attribute, $item, $direction = 'DESC') {
                $ids = collect($array)->pluck('id')->sort()->values()->all();
                $cacheKey = md5(json_encode($ids) . $attribute);

                if (!isset($this->rankCache[$cacheKey])) {
                    $items = $this->toCollection($array);

                    if ($direction == 'ASC') {
                        $sorted = $items->sortBy($attribute)->values();
                    } else {
                        $sorted = $items->sortByDesc($attribute)->values();
                    }

                    $this->rankCache[$cacheKey] = $sorted->pluck($attribute)->all();
                }

                $values = $this->rankCache[$cacheKey];

                $itemValue = is_object($item) ? $item->{$attribute} : $item[$attribute];

                $rank = 1;
                foreach ($values as $i => $val) {
                    if ($val == $itemValue) {
                        $rank = $i + 1;
                        break;
                    }
                }

                return $rank;
            }
        );

        // RANKMEDIAN(results, 'attribute', item, 'DESC'|'ASC'): the item's place, ties sharing the
        // middle of the places they cover. Values are grouped as array keys, so floats are cut to
        // whole numbers first (Q3).
        $this->el->register(
            'RANKMEDIAN',
            fn($array, $attribute, $item, $direction = 'DESC') => sprintf('RANKMEDIAN(%s, %s, %s, %s)', $array, $attribute, $item, $direction),
            function ($arguments, $array, $attribute, $item, $direction = 'DESC') {
                $items = $this->toCollection($array);

                $ids = $items->pluck('id')->sort()->values()->all();
                $cacheKey = md5(json_encode($ids) . $attribute);

                if (!isset($this->rankMedianCache[$cacheKey])) {
                    if ($direction == 'ASC') {
                        $sorted = $items->sortBy($attribute)->values();
                    } else {
                        $sorted = $items->sortByDesc($attribute)->values();
                    }

                    $rankMap = [];
                    foreach ($sorted as $i => $entry) {
                        $val = is_object($entry) ? $entry->{$attribute} : $entry[$attribute];
                        $rankMap[$val][] = $i + 1;
                    }

                    $this->rankMedianCache[$cacheKey] = $rankMap;
                }

                $itemValue = is_object($item) ? $item->{$attribute} : $item[$attribute];
                $ranks = $this->rankMedianCache[$cacheKey][$itemValue] ?? [];

                if (empty($ranks)) {
                    return null;
                }

                $count = count($ranks);
                $middle = floor($count / 2);

                return ($count % 2 === 0)
                    ? ($ranks[$middle - 1] + $ranks[$middle]) / 2
                    : $ranks[$middle];
            }
        );
    }

    /**
     * Evaluates an expression, naming the part of the formula it belongs to if it fails
     */
    private function safeEval(Expression|string $expr, array $context, string $label)
    {
        try {
            return $this->el->evaluate($expr, $context);
        } catch (\Throwable $e) {
            throw new ScoringException("Error evaluating {$label}: {$e->getMessage()} - '{$expr}'", $context);
        }
    }

    private function toCollection($value): Collection
    {
        if ($value instanceof Collection) {
            return $value;
        }

        if (is_array($value)) {
            return collect($value);
        }

        return collect([$value]);
    }
}
