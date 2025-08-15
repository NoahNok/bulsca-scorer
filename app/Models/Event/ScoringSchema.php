<?php

namespace App\Models\Event;

use App\DTO\RankedResult;
use App\DTO\ResolvedResult;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MathParser\Interpreting\Evaluator;
use MathParser\StdMathParser;

class ScoringSchema extends Model
{
    use HasFactory;

    protected $casts = [
        'schema' => 'array'
    ];

    /**
     * @param Collection<int, \App\DTO\ResolvedResult> $results
     * 
     * @return Collection<int, \App\DTO\ResolvedResult>
     */
    public function applyToResults(Collection $results): Collection
    {


        $equation = $this->schema['equation'];
        $variables = $this->schema['variables'];


        $parser = new StdMathParser();
        $pre = new ExpressionPreprocessor(array_merge(['result', 'max_result', 'min_result'], array_keys($variables)));

        // Setatic vars that are always available
        $staticVars = [
            'max_result' => $results->max('resolvedResult'),
            'min_result' => $results->min('resolvedResult')
        ];

        $scoring_equation = $parser->parse($pre->transformExpression($equation));


        $results->each(function ($result) use ($staticVars, $scoring_equation, $pre, $variables) {
            $evaluator = new Evaluator();
            $evaluator->setVariables($pre->transformValues(array_merge($staticVars, [
                "result" => $result->resolvedResult
            ], $variables)));

            $result->points = $scoring_equation->accept($evaluator);
        });


        return $results;
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
