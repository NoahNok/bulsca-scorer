<?php

namespace App\Scoring\Engine;

/**
 * A penalty or disqualification on a result, as a formula sees it: `item.penalties` and
 * `item.disqualifications` are lists of these, each read as `item.code`.
 */
final class ViolationCode
{
    public function __construct(public readonly int|string $code) {}
}
