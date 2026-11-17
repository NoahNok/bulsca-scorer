<?php

namespace App\Scoring\Golden;

/**
 * Reads, writes and compares the golden files. They're written one row to a line, so a fixture stays
 * a sensible size and a changed result is a one-line diff.
 */
class GoldenJson
{
    public static function read(string $path): array
    {
        return json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    }

    public static function write(string $path, array $data): void
    {
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0775, true);
        }

        file_put_contents($path, self::encode($data) . "\n");
    }

    public static function encode(mixed $value, int $indent = 0): string
    {
        if (!is_array($value) || $value === [] || self::fitsOnOneLine($value)) {
            return self::scalar($value);
        }

        $pad = str_repeat('  ', $indent + 1);
        $isList = array_is_list($value);

        $lines = [];
        foreach ($value as $key => $item) {
            $lines[] = $pad . ($isList ? '' : json_encode((string) $key) . ': ') . self::encode($item, $indent + 1);
        }

        return ($isList ? '[' : '{') . "\n" . implode(",\n", $lines) . "\n" . str_repeat('  ', $indent) . ($isList ? ']' : '}');
    }

    /**
     * Where two snapshots differ, as "path: expected, got" lines (none when they match). Everything
     * has to be identical, row order included: a number that moves in its last digit is a difference.
     *
     * @return string[]
     */
    public static function differences(mixed $expected, mixed $actual, string $path = ''): array
    {
        if (is_array($expected) && is_array($actual)) {
            $differences = [];

            foreach (array_unique([...array_keys($expected), ...array_keys($actual)]) as $key) {
                $at = $path === '' ? (string) $key : "{$path}.{$key}";

                if (!array_key_exists($key, $expected)) {
                    $differences[] = "{$at}: not expected, got " . self::scalar($actual[$key]);
                } elseif (!array_key_exists($key, $actual)) {
                    $differences[] = "{$at}: expected " . self::scalar($expected[$key]) . ', missing';
                } else {
                    array_push($differences, ...self::differences($expected[$key], $actual[$key], $at));
                }
            }

            return $differences;
        }

        if (self::same($expected, $actual)) {
            return [];
        }

        return ["{$path}: expected " . self::scalar($expected) . ', got ' . self::scalar($actual)];
    }

    private static function same(mixed $expected, mixed $actual): bool
    {
        if ((is_int($expected) || is_float($expected)) && (is_int($actual) || is_float($actual))) {
            // 1000 and 1000.0 are the same result; anything else has to be the same to the last digit
            return $expected == $actual;
        }

        return $expected === $actual;
    }

    /**
     * A list of plain values, or a row of them (a result, with its lists of codes), reads best on one line
     */
    private static function fitsOnOneLine(array $value): bool
    {
        $depth = self::depth($value);

        return $depth <= 1 || ($depth <= 2 && !array_is_list($value));
    }

    private static function depth(mixed $value): int
    {
        if (!is_array($value)) {
            return 0;
        }

        return 1 + ($value === [] ? 0 : max(array_map(self::depth(...), $value)));
    }

    private static function scalar(mixed $value): string
    {
        $precision = ini_set('serialize_precision', '-1');

        try {
            return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR);
        } finally {
            ini_set('serialize_precision', $precision);
        }
    }
}
