<?php

namespace App\Models\Event;

use App\DTO\RankedResult;
use App\DTO\ResolvedResult;
use App\DTO\Result;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use MathParser\Interpreting\Evaluator;
use MathParser\StdMathParser;
use RuntimeException;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ScoringSchema extends Model
{
    use HasFactory;

    protected $casts = [
        'schema' => 'array'
    ];

    private ScoringEngine $engine;

    public function __construct()
    {
        $this->engine = new ScoringEngine();
    }

    /**
     * @param Collection<int, \App\DTO\ResolvedResult> $results
     * 
     * @return Collection<int, \App\DTO\ResolvedResult>
     */
    public function applyToResults(Collection $results): Collection
    {


        /**
         * Move to following setup:
         * 1. Apply automatic penalties and disqualifications in one loop to results applying pens and then dqs
         *    Each should have a condition to check if it applies, and an expression that determines how many get applied annd the code to apply (e.g apply P900 5 times)
         *    These should only have the current result available to them and nothing else (unless we decide its needed)
         * 2. Do a second round where we first apply the penalty function which should use only the data available on the current result, no global variables allowed here (e.g. result + no_pens * 15s or rope throw logic)
         *    Apply the result equation to each result. This will support having the global variables and all methods available to it
         * 
         * Make sure we make use of the fact we can pre-compile expressions like the per result equation and then pass all the data into it to evaluate
         *  Could also pre-gen the auto pen and dq expressions and then call them for each result row (potentailly multiple dq/pen expressions for each result O(r * (d + p))
         * 
         * This setup means we can support any equation and penalties, whilst allowing the automatic application of DQs and Penalties based on given rules
         * 
         * Need to clean up below to easily have a few methods such as evaluateExpression(expr, ?context) evaluateConditional(cond, ?context) where context can just be a singular result or a global
         * context including global vars etc
         */

        $schema = $this->schema;

        $schema['global_variables'] = collect($schema['global_variables'])->sortBy('order')->toArray();
        $schema['local_variables'] = collect($schema['local_variables'] ?? [])->sortBy('order')->toArray();

        return $this->engine->process($this->schema, $results);
    }
}



class ScoringEngine
{
    private ExpressionLanguage $el;

    /**
     * @var array<string, callable> Map of exposed variable => fn($result): mixed
     * Example: ['disqualified' => fn($r) => $r->isDisqualified()]
     */
    private array $bindings;

    /**
     * When true, the raw result object is also exposed as 'result'.
     * Keep false for stricter sandboxing.
     */
    private bool $exposeResult;

    public function __construct(array $bindings = [], bool $exposeResult = false, ?ExpressionLanguage $el = null)
    {
        $this->el = $el ?? new ExpressionLanguage();
        $this->bindings = $bindings;
        $this->exposeResult = $exposeResult;

        $this->registerBuiltins();
    }

    /**
     * Process a payload:
     * [
     *   'equation' => string,
     *   'global_variables' => [ ['name' => string, 'expression' => string], ... ],
     *   'local_variables'  => [ ['name' => string, 'expression' => string], ... ],
     *   'results' => array|\Illuminate\Support\Collection
     * ]
     *
     * @return Collection Evaluated outputs, one per result.
     */
    public function process(array $payload, Collection $results): Collection
    {
        $equation = $payload['equation'] ?? null;
        $penaltyFunction = $payload['penalty_func'] ?? null;
        $globalVars = $payload['global_variables'] ?? [];
        $localVars  = $payload['local_variables'] ?? [];

        if (!is_string($equation) || $equation === '') {
            throw new InvalidArgumentException("Missing or invalid 'equation'.");
        }



        // Global context is computed once for penaties so that we can recalcuate resolvedResult
        $penaltyGlobalContext = [
            'results' => $results,
        ];

        // Apply penalty function
        foreach ($results as $result) {
            $perResultContext = $this->buildPerResultContext($result, $penaltyGlobalContext);



            // Local variables in order (each can depend on prior locals + globals)
            foreach ($localVars as $def) {
                $name = $def['name'] ?? null;
                $expr = $def['expression'] ?? null;
                if (!$name || !$expr) {
                    throw new InvalidArgumentException("Each local variable must have 'name' and 'expression'.");
                }

                $perResultContext[$name] = $this->safeEval($expr, $perResultContext, "local variable '{$name}'");
            }

            if ($penaltyFunction) {
                dump($perResultContext);
                dump($result->resolvedResult);
                $result->resolvedResult = $this->safeEval($penaltyFunction, $perResultContext, "penaltyFunction");
                dump($result->resolvedResult);
                $perResultContext['result'] = $result->resolvedResult;
            }
        }



        // New global context for the newly generated result which now has the updated resolvedResult based on penatlys
        $globalContext = [
            'results' => $results,
        ];

        // Evaluate globals in order (each can depend on prior globals)
        foreach ($globalVars as $def) {
            $name = $def['name'] ?? null;
            $expr = $def['expression'] ?? null;
            if (!$name || !$expr) {
                throw new InvalidArgumentException("Each global variable must have 'name' and 'expression'.");
            }


            $globalContext[$name] = $this->safeEval($expr, $globalContext, "global variable '{$name}'");
        }




        // Evaluate per result
        foreach ($results as $result) {
            $perResultContext = $this->buildPerResultContext($result, $globalContext);

            // Local variables in order (each can depend on prior locals + globals)
            foreach ($localVars as $def) {
                $name = $def['name'] ?? null;
                $expr = $def['expression'] ?? null;
                if (!$name || !$expr) {
                    throw new InvalidArgumentException("Each local variable must have 'name' and 'expression'.");
                }

                $perResultContext[$name] = $this->safeEval($expr, $perResultContext, "local variable '{$name}'");
            }




            // Final equation per result
            $result->points = $this->safeEval($equation, $perResultContext, "equation");
        }

        return $results;
    }

    /**
     * Build a strict, whitelisted context for a single result.
     */
    private function buildPerResultContext($result, array $globalContext): array
    {
        $ctx = $globalContext;



        // Expose only whitelisted bindings
        // foreach ($this->bindings as $key => $fn) {
        //     $ctx[$key] = $fn($result);
        // }

        // Optionally expose raw result (not recommended unless needed)

        $ctx['item'] = $result;


        // Also keep 'results' available in locals for aggregates
        if (!isset($ctx['results'])) {
            $ctx['results'] = collect([]); // fallback, shouldn't happen
        }

        return $ctx;
    }

    /**
     * Register helper functions like FILTER and MAXIMUM.
     */
    private function registerBuiltins(): void
    {
        // FILTER(arrayOrCollection, "item.condition")
        $this->el->register(
            'FILTER',
            function ($array, $condition) {
                return sprintf('FILTER(%s, %s)', $array, $condition);
            },
            function ($arguments, $array, $condition) {
                $items = $this->toCollection($array);

                return $items
                    ->filter(function ($item) use ($condition) {


                        $vars = $this->buildPerResultContext($item, ['item' => $item]);


                        // Evaluate condition with 'item' in scope
                        return (bool) $this->el->evaluate($condition, $vars);
                    })
                    ->values()
                    ->all();
            }
        );

        // MAXIMUM(arrayOrCollection, "attribute")
        $this->el->register(
            'MAXIMUM',
            function ($array, $attribute) {
                return sprintf('MAXIMUM(%s, %s)', $array, $attribute);
            },
            function ($arguments, $array, $attribute) {
                $items = $this->toCollection($array);

                if ($attribute == 'result') {
                    $attribute = 'resolvedResult';
                }

                return $items->max($attribute);
            }
        );

        // MINIMUM(arrayOrCollection, "attribute")
        $this->el->register(
            'MINIMUM',
            function ($array, $attribute) {
                return sprintf('MINIMUM(%s, %s)', $array, $attribute);
            },
            function ($arguments, $array, $attribute) {
                $items = $this->toCollection($array);

                if ($attribute == 'result') {
                    $attribute = 'resolvedResult';
                }

                return $items->min($attribute);
            }
        );

        $this->el->register(
            'COUNT',
            function ($array, $attribute) {
                return sprintf('COUNT(%s)', $array, $attribute);
            },
            function ($arguments, $array) {

                return count($array);
            }
        );

        $this->el->register(
            'CEIL',
            function ($array, $attribute) {
                return sprintf('CEIL(%s)', $array, $attribute);
            },
            function ($arguments, $numb) {

                return ceil($numb);
            }
        );
    }

    /**
     * Wrapper to provide clearer error messages.
     */
    private function safeEval(string $expr, array $context, string $label)
    {
        try {

            return $this->el->evaluate($expr, $context);
        } catch (\Throwable $e) {
            throw new RuntimeException("Error evaluating {$label}: {$e->getMessage()}");
        }
    }

    /**
     * Convert array|Collection to Collection.
     */
    private function toCollection($value)
    {
        if ($this->isCollection($value)) {
            return $value;
        }
        if (is_array($value)) {
            return collect($value);
        }
        // If a single item sneaks in, wrap it for consistency
        return collect([$value]);
    }

    /**
     * Detect Illuminate Collection without hard dependency.
     */
    private function isCollection($value): bool
    {
        return $value instanceof \Illuminate\Support\Collection;
    }
}
