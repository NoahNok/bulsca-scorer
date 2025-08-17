<?php

namespace App\Models\Event;

use App\DTO\RankedResult;
use App\DTO\ResolvedResult;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MathParser\Interpreting\Evaluator;
use MathParser\StdMathParser;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ScoringSchema extends Model
{
    use HasFactory;

    protected $casts = [
        'schema' => 'array'
    ];

    private $evaluatedVariables = [];

    private StdMathParser $parser;

    public function __construct()
    {
        $this->parser = new StdMathParser();
    }

    /**
     * @param Collection<int, \App\DTO\ResolvedResult> $results
     * 
     * @return Collection<int, \App\DTO\ResolvedResult>
     */
    public function applyToResults(Collection $results): Collection
    {


        $equation = $this->schema['equation'];
        $variables = collect($this->schema['variables'])->sortBy('order')->map(function ($var) {
            unset($var['order']);
            return $var;
        })->toArray();

        $this->resolveVariables($variables, $results);

        $pre = new ExpressionPreprocessor(array_merge(['result'], array_keys($this->evaluatedVariables)));

        // Setatic vars that are always available


        $scoring_equation = $this->parser->parse($pre->transformExpression($equation));


        $results->each(function ($result) use ($scoring_equation, $pre, $variables) {
            $evaluator = new Evaluator();
            $evaluator->setVariables($pre->transformValues(array_merge($this->evaluatedVariables, [
                "result" => $result->resolvedResult
            ], $variables)));

            $result->points = $scoring_equation->accept($evaluator);
        });


        return $results;
    }

    private function getAggregates(): array
    {
        // Aggregates you want to allow
        return [
            'COUNT' => fn($col, $field) => $col->count(),
            'SUM'   => fn($col, $field)        => $col->sum($field),
            'MAX'   => fn($col, $field)        => $col->max($field),
            'MIN'   => fn($col, $field)        => $col->min($field),
            'AVG'   => fn($col, $field)        => $col->avg($field),
        ];
    }

    private function getPattern(): string
    {
        return $pattern = '/\b(COUNT|SUM|AVG|MAX|MIN)      # function name
            \s*\(                           # opening parenthesis
            \s*([a-zA-Z_][a-zA-Z0-9_]*|)    # optional field
            \s*(?:,\s*where\((.*?)\))?      # optional where(condition)
            \s*\)                           # closing parenthesis
           /ix';
    }

    private function resolveVariables(array $variables, $results)
    {
        $conditionLang = new ExpressionLanguage();

        dump($variables);

        foreach ($variables as $variable_name => $variable_expression) {
            $converted = $this->convert_expression($variable_expression, $results, $this->getAggregates(), $conditionLang);

            dump($converted, $this->evaluatedVariables);

            $pre = new ExpressionPreprocessor(array_keys($this->evaluatedVariables));

            $expression = $this->parser->parse($pre->transformExpression($converted));

            $evaluator = new Evaluator();
            $evaluator->setVariables($this->evaluatedVariables);
            $this->evaluatedVariables[$variable_name] = $expression->accept($evaluator);
        }
    }

    function convert_expression(string $expr, $results, $supportedAggregates, $conditionLang)
    {
        return preg_replace_callback($this->getPattern(), function ($m) use ($results, $supportedAggregates, $conditionLang) {
            $func      = strtoupper($m[1]);
            $field     = trim($m[2] ?? '');
            $condition = trim($m[3] ?? '');

            // Start with full collection
            $filtered = $results;

            // Apply condition if present
            if ($condition !== '') {
                $filtered = $filtered->filter(fn($r) => $this->eval_condition($r, $condition, $conditionLang));
            }

            // Run aggregate function
            if (!array_key_exists($func, $supportedAggregates)) {
                return '0';
            }

            $agg = $supportedAggregates[$func];
            $value = $agg($filtered, $field);

            return (string) $value;
        }, $expr);
    }

    function eval_condition($row, string $expr, ExpressionLanguage $lang): bool
    {
        // Only expose whitelisted fields
        $vars = [
            'disqualified' => (bool) $row->isDisqualified(),
            'penalties'        => (float) $row->penalties->count(),
        ];
        return (bool) $lang->evaluate($expr, $vars);
    }
}


class ExpressionPreprocessor
{
    protected array $variableMap = [];
    protected array $reverseMap = [];
    protected string $alphabet = 'abcdefghijklmnopqrstuvwxyz';

    public function __construct(array $variables)
    {
        $this->generateMap($variables);
    }

    protected function generateMap(array $variables): void
    {

        $usedLetters = [];
        foreach ($variables as $index => $name) {
            $letter = $this->alphabet[$index] ?? null;
            if (!$letter) {
                throw new \Exception("Too many variables — ran out of letters.");
            }
            $this->variableMap[$name] = $letter;
            $this->reverseMap[$letter] = $name;
        }
    }

    public function transformExpression(string $expression): string
    {
        // Sort by longest name first to avoid partial replacements
        $sorted = array_keys($this->variableMap);
        usort($sorted, fn($a, $b) => strlen($b) <=> strlen($a));

        foreach ($sorted as $name) {
            $expression = preg_replace('/\b' . preg_quote($name, '/') . '\b/', $this->variableMap[$name], $expression);
        }

        return $expression;
    }

    public function transformValues(array $values): array
    {
        $shortVars = [];
        foreach ($this->variableMap as $long => $short) {
            if (array_key_exists($long, $values)) {
                $shortVars[$short] = $values[$long];
            }
        }
        return $shortVars;
    }

    public function getMap(): array
    {
        return $this->variableMap;
    }

    public function getReverseMap(): array
    {
        return $this->reverseMap;
    }
}
