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
use Symfony\Component\ExpressionLanguage\Expression;
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

    public function editFromRequest($request)
    {
        $validated = $request->validated();


        if (array_key_exists('name', $validated) && $validated['name']) {
            $this->name = $validated['name'];
        }

        $ss = ['equation' => $validated['equation'], 'global_variables' => $validated['global_variables']];

        if (array_key_exists('local_variables', $validated)) {
            $ss['local_variables'] = $validated['local_variables'];
        }

        if (array_key_exists('penalty_func', $validated)) {
            $ss['penalty_func'] = $validated['penalty_func'];
        }

        if (array_key_exists('auto_penalties', $validated)) {
            $ss['auto_penalties'] = $validated['auto_penalties'];
        }

        if (array_key_exists('auto_disqualifications', $validated)) {
            $ss['auto_disqualifications'] = $validated['auto_disqualifications'];
        }

        $this->schema = $ss;



        $this->save();
    }

    public function applyViolations(Collection $results): Collection
    {
        $schema = $this->schema;

        $schema['auto_disqualifications'] = collect($schema['auto_disqualifications'] ?? []);
        $schema['auto_penalties'] = collect($schema['auto_penalties'] ?? []);

        return $this->engine->applyViolations($schema, $results);
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


        $schema['global_variables'] = collect($schema['global_variables'] ?? [])->sortBy('order')->toArray();
        $schema['local_variables'] = collect($schema['local_variables'] ?? [])->sortBy('order')->toArray();


        return $this->engine->process($this->schema, $results);
    }

    public static function engine(): ScoringEngine
    {
        return (new ScoringEngine());
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

    public function __construct(array $bindings = [])
    {
        $this->el = new ExpressionLanguage();
        $this->bindings = $bindings;

        $this->registerBuiltins();
    }

    public function applyViolations(array $payload, Collection $results): Collection
    {
        $autoPens = $payload['auto_penalties'] ?? [];
        $autoDqs = $payload['auto_disqualifications'] ?? [];

        // Apply pens
        foreach ($autoPens as $autoPen) {
            $applyCode = $autoPen['code'];

            $applyIf = $this->el->parse($autoPen['condition'], ['item']);
            $applyAmount = $this->el->parse($autoPen['amount'], ['item']);

            foreach ($results as $result) {

                if (!((bool) $this->el->evaluate($applyIf, ['item' => $result]))) {
                    continue;
                }

                $amount = $this->el->evaluate($applyAmount, ['item' => $result]);

                for ($i = 0; $i < $amount; $i++) {
                    $pen = new Penalty();
                    $pen->code = $applyCode;
                    $result->penalties->push($pen);
                }
            }
        }

        // Apply auto dq
        foreach ($autoDqs as $autoDq) {
            $applyCode = $autoPen['code'];

            $applyIf = $this->el->parse($autoDq['condition'], ['item']);
            $applyAmount = $this->el->parse($autoDq['amount'], ['item']);

            foreach ($results as $result) {

                if (!((bool) $this->el->evaluate($applyIf, ['item' => $result]))) {
                    continue;
                }

                $amount = $this->el->evaluate($applyAmount, ['item' => $result]);

                for ($i = 0; $i < $amount; $i++) {
                    $pen = new Disqualification();
                    $pen->code = $applyCode;
                    $result->disqualifications->push($pen);
                }
            }
        }

        $penalty_func = array_key_exists('penalty_func', $payload) && $payload['penalty_func'] ? $this->el->parse($payload['penalty_func'], ['item']) : null;

        // Apply penalty function 
        $resolvedResults = collect([]);
        foreach ($results as $result) {
            $resolvedResult = $result->result;

            // Do penalty func
            if ($penalty_func && $result->penalties->count() > 0) {
                $resolvedResult = $this->el->evaluate($penalty_func, ['item' => $result]);
            }

            $resolvedResults[] = new ResolvedResult(
                $result->id,
                $resolvedResult,
                $result->result,
                $result->entity,
                $result->event,
                $result->disqualifications,
                $result->penalties
            );
        }

        return $resolvedResults;
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
    public function process(array $payload, Collection $results, string $targetField = 'points'): Collection
    {
        $equation = $payload['equation'] ?? null;

        $globalVars = $payload['global_variables'] ?? [];
        $localVars  = $payload['local_variables'] ?? [];

        // Filter out any local/global vars that aren't correct

        $localVars = array_filter($localVars, fn($item) => $item['expression'] != null);


        if (!is_string($equation) || $equation === '') {
            throw new InvalidArgumentException("Missing or invalid 'equation'.");
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




        // Lets precopile each local variable's expression
        $allLocalVariableNames = array_map(fn($item) => $item['name'], $localVars);
        array_push($allLocalVariableNames, 'item');
        foreach ($localVars as $index => $var) {

            $localVars[$index]['expression'] = $this->el->parse($var['expression'], $allLocalVariableNames);
        }

        $equation = $this->el->parse($equation, array_merge($allLocalVariableNames, array_keys($globalContext)));

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
            $result->{$targetField} = $this->safeEval($equation, $perResultContext, "equation");
        }

        return $results;
    }

    /**
     * Build a strict, whitelisted context for a single result.
     */
    private function buildPerResultContext($result, array $globalContext): array
    {
        $ctx = $globalContext;

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

        $this->el->register(
            'FLOOR',
            function ($array, $attribute) {
                return sprintf('FLOOR(%s)', $array, $attribute);
            },
            function ($arguments, $numb) {

                return floor($numb);
            }
        );
    }

    /**
     * Wrapper to provide clearer error messages.
     */
    private function safeEval(Expression|string $expr, array $context, string $label)
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
